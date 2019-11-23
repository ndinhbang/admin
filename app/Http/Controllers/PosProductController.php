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
     * @param  \App\Http\Requests\PosProductRequest  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(PosProductRequest $request)
    {
        $products = Product::with('category')
            ->filter(new ProductFilter($request))
            ->orderBy('products.position', 'asc')
            ->orderBy('products.id', 'desc')
            ->take(100) // max:100 products
            ->get();

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
        return new PosProductResource($product);
    }

}
