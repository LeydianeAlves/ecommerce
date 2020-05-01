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
}
