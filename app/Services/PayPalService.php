<?php
namespace App\Services;

use Mockery\Exception;
use PayPal\Api\Payer;
use PayPal\Api\Item;
use PayPal\Api\Amount;
use PayPal\Api\Payment;
use PayPal\Api\Details;
use PayPal\Api\ItemList;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\PaymentExecution;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;

class PaypalService
{
protected $payPal;

    public function __construct()
    {
        if (config('settings.paypal_client_id')  == '' || config('settings.paypal_secret_id') == '') {
            return redirect()->back()->with('error', 'No Paypal settings found');
        }

        $this->payPal = new ApiContext (
            new OAuthTokenCredential(
                config('settings.paypal_client_id'),
                config('settings.paypal_secret_id')
            )
        );

        $this->payPal->setConfig(
            array(
                'mode' => 'sandbox',
                'log.LogEnabled' => true,
                'log.FileName' => '../PayPal.log',
                'log.LogLevel' => 'DEBUG', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
                'cache.enabled' => true,
              //implementing \PayPal\Log\PayPalLogFactory
            )
        );
    }

    public function processPayment($order) {
        $shipping = sprintf('%0.2f', 0);
        $tax = sprintf('%0.2f', 0);

        //creating a new payer instance
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        //ading items to the list
        $items = array();
        foreach ($order->items as $item)
        {
            $orderItems[$item->id] = new Item();
            $orderItems[$item->id]->setName($item->product->name)
                ->setCurrency(config('settings.currency_code'))
                ->setQuantity($item->quantity)
                ->setPrice(sprintf('%0.2f', $item->price));

            array_push($items, $orderItems[$item->id]);
        }

        $itemList = new ItemList();
        $itemList->setItems($items);

        //setting shipping details
        $details = new Details();
        $details->setShipping($shipping)
            ->setTax($tax)
            ->setSubTotal(sprintf('%0.2f', $order->grand_total));

        //create chargeable amount
        $amount = new Amount();
        $amount->setCurrency(config('settings.currency_code'))
            ->setTotal($order->grand_total)
            ->setDetails($details);

        //creating a transaction
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription($order->user->full_name)
            ->setInvoiceNumber($order->order_number);

        //setting up redirection urls
        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(route('checkout.payment.complete'))
            ->setCancelUrl(route('checkout.index'));

        //creating payment instance
        $payment = new Payment();
        $payment->setIntent("sale")
            ->SetPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction));


        try {
            $payment->create($this->payPal);
        } catch (PaypalConnectionException $exception) {
            echo $exception->getCode();// prints the error code
            echo $exception->getData();// prints the detailed error message
            exit(1);
        } catch (Exception $e) {
            echo $e->getMessage();
            exit(1);
        }


        $approvalUrl = $payment->getApprovalLink();
        header("Location: {$approvalUrl}");

        exit;
    }

    public function completePayment($paymentId, $payerId)
    {
        $payment = Payment::get($paymentId, $this->payPal);
        $execute = new PaymentExecution();
        $execute->setPayerId($payerId);

        try {
            $result = $payment->execute($execute, $this->payPal);
        } catch (PayPalConnectionException $exception) {
            $data = json_decode($exception->getData());
            $_SESSION['message'] = 'Error, '.$data->message;

            //todo show errors from paypal
            exit;
        }

        if ($result->state === 'approved') {
            $transactions = $result->getTransactions();
            $transaction = $transactions[0];
            $invoiceId = $transaction->invoice_number;

            $relatedResources = $transactions[0]->getRelatedResources();
            $sale = $relatedResources[0]->getSale();
            $saleId = $sale->getId();

            $transactionData = ['salesId' => $saleId, 'invoiceId' => $invoiceId];

            return $transactionData;
        } else {
            echo "<h3>" .$result->state. "<h3>";
            var_dump($result);
            exit(1);
        }
    }
}

