<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CategoryRequest $request)
    {
        $categories = Category::where(function ($query) use ($request) {
                if($request->type) {
                    $query->where('type', $request->type);
                }
            })
            ->with('place')
            ->orderBy('position', 'asc')
            ->paginate(100);
        return $categories->toJson();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        $category = new Category;
        $category->uuid = nanoId();
        $category->name = $request->name;
        $category->description = $request->description;
        $category->parent_id = $request->parent_id ?? 0;
        $category->type = $request->type;
        $category->place_id = currentPlace()->id;
        $category->save();

        return response()->json(['message' => 'Tạo danh mục thành công!', 'category' => $category]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return $category->toJson();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $category->name = $request->name;
        $category->description = $request->description;
        $category->parent_id = $request->parent_id ?? 0;
        $category->position = $request->position;
        $category->state = $request->state;
        $category->save();

        return response()->json(['message' => 'Cập nhật danh mục thành công!', 'category' => $category]);
    }

    public function updatePosition(Request $request) {
        $categories = $request->categories;
        if(is_null($categories) || count($categories) < 1)
            return response()->json(['message' => 'Có lỗi xảy ra!']);

        \DB::transaction(function () use ($request, $categories) {
            $position = 100;
            foreach ($categories as $key => $category) {
                if(isset($category['place']) && !is_null($category['place'])) {
                    $cat = Category::findUuid($category['uuid']);
                    $cat->position = $position++;
                    $cat->save();
                }
            }
        });

        return response()->json(['message' => 'Cập nhật vị trí thành công!']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
