<?php

namespace App\Http\Controllers;

use App\Events\ProductChanged;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\Supply;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $products = Product::orderBy('products.id', 'desc')->simplePaginate(100);
        return response()->json($products);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Throwable
     */
    public function store(ProductRequest $request)
    {
        $product = DB::transaction(function () use ($request) {
            $placeId = currentPlace()->id;
            $category = getBindVal('category');
            // create product
            $product = Product::create(
                array_merge($request->except(['supplies', 'category_uuid']), [
                    'uuid'     => nanoId(),
                    'place_id' => $placeId,
                ])
            );
            // tao supply neu san pham co quan ly ton kho
            if ($product->can_stock) {
                $keyedArr = $this->suppliesOfProduct($product, $request->supplies ??  []);
                $product->supplies()->attach($keyedArr);
            }

            return $product;
        }, 5);

        $product->load(['supplies', 'category']);

//        broadcast(new ProductChanged($product));

        return response()->json([
            'message' => 'Product added!',
            'data'    => $product,
        ]);
    }

    /**
     * Create supplies and then return array that is ready for attach to pivot table
     *
     * @param Product $product
     * @param array   $arrSupplies
     * @return array
     */
    protected function suppliesOfProduct(Product $product, array $arrSupplies)
    {
        $result = [];
        $collection = new Collection($arrSupplies);
        // neu san pham khong co supply thi tu dong tao theo ten san pham
        if ($collection->isEmpty()) {
            $supply = Supply::firstOrNew([
                'place_id' => $product->place_id,
                'name'     => $product->name,
            ]);

            if (!$supply->id) {
                // generate uuid
                $supply->uuid = nanoId();
                $supply->save();
            }

            $result[$supply->id] = ['quantity' => 1];
            return $result;
        }

        foreach ($collection as $item) {
            $supply = Supply::firstOrNew([
                'place_id' => $product->place_id,
                'name'     => $item['name'],
            ]);
            if (!$supply->id) {
                // generate uuid
                $supply->uuid = $supply->uuid ?? nanoId();
                $supply->save();
            }
            $result[$supply->id] = ['quantity' => $item['quantity']];
        }
        return $result;
    }

    /**
     * Display the specified resource.
     *
     * @param Product $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return response()->json($product->load('supplies'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProductRequest $request
     * @param Product        $product
     * @return \Illuminate\Http\Response
     * @throws \Throwable
     */
    public function update(ProductRequest $request, Product $product)
    {
        $request->validated();

        $product = DB::transaction(function () use ($request, $product) {
            $product->update($request->except(['supplies', 'netroom_id']));

            $supplies = collect($request->input('supplies'));
            $keyed = $supplies->mapWithKeys(function ($item) {
                return [$item['id'] => ['quantity' => $item['quantity']]];
            });

            $product->supplies()->sync($keyed->toArray());

            return $product;
        }, 5);

        $product->load(['supplies', 'category']);

        broadcast(new ProductChanged($product));

        return response()->json(['message' => 'Product updated!', 'data' => $product]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Product $product
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(['message' => 'Product deleted!']);
    }

    /**
     * Toggle product hot and status
     *
     * @param ProductRequest $request
     * @param Product        $product
     * @param                $toggle
     * @return \Illuminate\Http\Response
     */
    public function toggle(ProductRequest $request, Product $product, $toggle)
    {
        $request->validated();

        if (!in_array($toggle, ['status', 'hot'])) {
            return response()->json(['error' => true, 'message' => 'Route not found'], 404);
        }

        if ($toggle == 'hot') {
            $product->update(['is_hot' => !$product->is_hot]);
        } else {
            $product->update(['status' => !$product->status]);
        }

        broadcast(new ProductChanged($product->load(['supplies', 'category'])));

        return response()->json(['message' => 'Product updated!', 'product' => $product]);
    }

    public function uploadThumbnail(ProductRequest $request)
    {
        $request->validated();

        $extension = $request->file('thumbnail')->getClientOriginalExtension();
        $filename = uniqid();
        $file = $request->file('thumbnail')->move($this->thumbnail_path, $filename . "." . $extension);

        $filePath = $this->thumbnail_path . $filename . "." . $extension;
        $img = \Image::make($filePath);

        $img->fit(600, 358);
        $img->save($filePath);

        return response()->json(['message' => 'Thumbnail updated!', 'filePath' => $filePath]);
    }


}
