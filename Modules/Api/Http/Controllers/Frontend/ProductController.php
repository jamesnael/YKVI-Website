<?php

namespace Modules\Api\Http\Controllers\Frontend;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\MasterData\Entities\Product;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $validator = $this->validateTableRequest($request);

        if ($validator->fails()) {
            return response_json(false, 'Get data failed.', $validator->errors()->first());
        }

        $data = Product::with('product_details')->orderBy('created_at', 'desc')->get();

        $data->transform(function($item) {
            $item->short_description = clean_string($item->description);
            return $item;
        });

        return response_json(true, null, 'Data retrieved.', $data);
    }

    public function detail(Request $request, $product)
    {
        $validator = $this->validateTableRequest($request);

        if ($validator->fails()) {
            return response_json(false, 'Get data failed.', $validator->errors()->first());
        }

        $data = Product::whereSlug($product)->with('product_details')->orderBy('created_at', 'desc')->first();
        $data->short_description = clean_string($data->description);

        return response_json(true, null, 'Data retrieved.', $data);
    }

    /**
     *
     * Validation Rules for Get Table Data
     *
     */
    public function validateTableRequest($request)
    {
        return Validator::make($request->all(), [
            "paginate" => "bail|sometimes|required|numeric",
        ]);
    }
}
