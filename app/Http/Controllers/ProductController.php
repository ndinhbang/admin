<?php

namespace App\Http\Controllers;

use App\Events\ProductChanged;
use App\Http\Filters\ProductFilter;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Supply;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller {
	protected $thumbnail_path = 'medias/products/';
	protected $exceptAttributes = [
		'supplies',
		'category_uuid',
		'category_name',
		'thumbnail',
		'thumbnailFile',
		'updated_at',
		'created_at',
	];

	/**
	 * Display a listing of the resource.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
	 */
	public function index(ProductRequest $request) {
		$products = Product::with(['supplies', 'category', 'place'])
			->filter(new ProductFilter($request))
			->orderBy('products.id', 'desc')
			->paginate(20);

		// return response()->json($products);
		return ProductResource::collection($products);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param ProductRequest $request
	 * @return \Illuminate\Http\Response
	 * @throws \Throwable
	 */
	public function store(ProductRequest $request) {
		$product = DB::transaction(function () use ($request) {
			$placeId = currentPlace()->id;
			$category = getBindVal('category');
			// Upload image
			$baseName = uploadImage($request->file('thumbnailFile'), $this->thumbnail_path);
			// create product
			$product = Product::create(array_merge($request->except($this->exceptAttributes), [
				'category_id' => $category->id,
				'uuid' => nanoId(),
				'place_id' => $placeId,
				'thumbnail' => $baseName ?? '',
				'code' => $request->input('code'),
			]));

			// tao supply neu san pham co quan ly ton kho
			if ($product->can_stock) {
				$keyedArr = $this->addSupplies($product, $request->input('supplies', []));
				$product->supplies()->attach($keyedArr);
			}

			return $product;
		}, 5);

		$product->load(['supplies', 'category']);

//        broadcast(new ProductChanged($product));

		return response()->json([
			'message' => 'Product added!',
			'data' => new ProductResource($product),
		]);
	}

	/**
	 * Create supplies and then return array that is ready for attach to pivot table
	 *
	 * @param Product $product
	 * @param array   $arrSupplies
	 * @return array
	 */
	protected function addSupplies(Product $product, array $arrSupplies) {
		$result = [];
		$collection = new Collection($arrSupplies);
		// neu san pham khong co supply thi tu dong tao theo ten san pham
		if ($collection->isEmpty()) {
			$supply = Supply::firstOrNew([
				'place_id' => $product->place_id,
				'name' => $product->name,
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
				'name' => $item['name'],
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
	 * @return ProductResource
	 */
	public function show(Product $product) {
		// return response()->json($product->load('supplies'));
		return new ProductResource($product->load(['supplies', 'category', 'place']));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param ProductRequest $request
	 * @param Product        $product
	 * @return \Illuminate\Http\Response
	 * @throws \Throwable
	 */
	public function update(ProductRequest $request, Product $product) {
		$product = DB::transaction(function () use ($request, $product) {
			$placeId = currentPlace()->id;
			$category = getBindVal('category');

			$baseName = uploadImage($request->file('thumbnailFile'), $this->thumbnail_path);
			// create product
			$product->guard(['id', 'uuid', 'place_id', 'code']);
			$product->update(array_merge($request->except($this->exceptAttributes), [
				'category_id' => $category->id,
				'thumbnail' => $baseName ? $baseName : $product->thumbnail,
			]));
			// tao supply neu san pham co quan ly ton kho
			if ($product->can_stock) {
				$keyedArr = $this->addSupplies($product, $request->input('supplies', []));
				$product->supplies()->sync($keyedArr);
			}

			return $product;
		}, 5);

		$product->load(['supplies', 'category']);

//        broadcast(new ProductChanged($product));

		return response()->json([
			'message' => 'Product added!',
			'data' => $product,
		]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Product $product
	 * @return \Illuminate\Http\Response
	 * @throws \Exception
	 */
	public function destroy(Product $product) {

		// kiểm tra đơn hàng theo sản phẩm
		return response()->json($product->orders);
		// $product->delete();

		// return response()->json(['message' => 'Product deleted!']);
	}

	/**
	 * Toggle product hot and status
	 *
	 * @param ProductRequest $request
	 * @param Product        $product
	 * @param                $toggle
	 * @return \Illuminate\Http\Response
	 */
	public function toggle(ProductRequest $request, Product $product, $toggle) {
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

}
