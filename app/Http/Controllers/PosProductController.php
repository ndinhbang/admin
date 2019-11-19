<?php

namespace App\Http\Controllers;

use App\Http\Resources\PosProductResource;
use App\Models\Product;
use App\Http\Filters\ProductFilter;
use App\Http\Requests\PosProductRequest;

class PosProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(PosProductRequest $request)
    {
        $products = Product::with(['supplies'])
            ->filter(new ProductFilter($request))
            ->orderBy('products.id', 'desc')
            ->paginate(20);

        return PosProductResource::collection($products);
    }

    /**
     * Display the specified resource.
     *
     * @param Product $product
     * @return PosProductResource
     */
    public function show(Product $product)
    {
        return new PosProductResource($product->load(['supplies', 'category', 'place']));
    }

}
