<?php

namespace App\Http\Controllers;

use App\Http\Filters\CategoryFilter;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\PosCategoryResource;
use App\Models\Category;

class PosCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Http\Requests\CategoryRequest  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(CategoryRequest $request)
    {
        $categories = Category::filter(new CategoryFilter($request))
            ->orderBy('categories.position', 'asc')
            ->orderBy('categories.id', 'desc')
            ->take(50) // max:50 category
            ->get();
        return PosCategoryResource::collection($categories);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \App\Http\Resources\PosCategoryResource
     */
    public function show(Category $category)
    {
        return new PosCategoryResource($category);
    }

}
