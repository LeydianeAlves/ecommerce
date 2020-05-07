<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Contracts\ProductContract;
use App\Http\Controllers\BaseController;
use App\Models\ProductImage;
use App\Traits\UploadAble;

class ProductImageController extends BaseController
{
    use UploadAble;

    protected $productRepository;

    public function __construct(ProductContract $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function upload(Request $request)
    {
        $product = $this->productRepository->findProductById($request->product_id);

        if ($request->has('image')) {
            $image = $this->uploadOne($request->image, 'products');

            $productImage = new ProductImage([
                'full' => $image,
            ]);

            $product->images()->save($productImage);

        }

        return response()->json(['status' => 'success']);
    }

    public function delete($id)
    {
        $image = ProductImage::findOrFail($id);

        if ($image->full != '') {
            $this->deleteOne($image->full);
        }
        $image->delete();

        return redirect()->back();
    }
}
