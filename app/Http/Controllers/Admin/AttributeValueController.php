<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Contracts\AttributeContract;
use App\Http\Controllers\BaseController;
use App\Models\AttributeValue;

class AttributeValueController extends BaseController
{
    protected $attributeRepository;

    public function __construct(AttributeContract $attributeRepository)
    {
        $this->attributeRepository = $attributeRepository;
    }

    public function getValues(Request $request)
    {
        $attributeId = $request->input('id');
        $attribute = $this->attributeRepository->findAttributeById($attributeId);

        $values = $attribute->values;

        return response()->json($values);
    }

    public function addValues(Request $request) {
        //todo try and implement the createOrUpdate() method for this instead
        $value = new AttributeValue();
        $value->attribute_id =  $request->input(('id'));
        $value->value = $request->input('value');
        $value->price = $request->input('price');
        $value->save();

        return response()->json($value);

    }

    public function updateValues(Request $request) {
        $attributeValue = AttributeValue::findOrFail($request->input('valueId'));
        $attributeValue->attribute->id = $request->input('id');
        $attributeValue->value = $request->input('value');
        $attributeValue->price = $request->input('price');
        $attributeValue->save();

        return response()->json($attributeValue);
    }

    public function deleteValues(Request $request) {
        $attributeValue = AttributeValue::findOrFail($request->input('id'));

        $attributeValue->delete();

        return response()->json($attributeValue);
    }
}
