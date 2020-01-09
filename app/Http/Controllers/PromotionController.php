<?php

namespace App\Http\Controllers;

use App\Http\Filters\PromotionFilter;
use App\Http\Requests\PromotionRequest;
use App\Http\Resources\PromotionResource;
use App\Models\Account;
use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\PromotionApplied;
use App\Models\Segment;
use Arr;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param PromotionRequest $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(PromotionRequest $request)
    {
        $promotions = Promotion::filter(new PromotionFilter($request))
            ->orderBy('id', 'desc')
            ->simplePaginate($request->get('per_page', 50));
        return PromotionResource::collection($promotions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PromotionRequest $request
     *
     * @return PromotionResource
     * @throws \Throwable
     */
    public function store(PromotionRequest $request)
    {
        $promotion = DB::transaction(function () use ($request) {
            $place_id = currentPlace()->id;
            $promotion = Promotion::create(
                array_merge($request->only('title', 'description', 'code', 'quantity', 'require_coupon', 'type'),
                    [
                        'uuid'       => nanoId(),
                        'place_id'   => $place_id,
                        'start_date' => Carbon::parse($request->get('start_date', '1970-01-01 00:00:00')),
                        'end_date'   => Carbon::parse($request->get('end_date', '1970-01-01 00:00:00')),
                    ]
                )
            );
            //Customers
            $customer_uuid_array = array_column($request->get('customers', []), 'uuid');
            if ($customer_uuid_array) {
                $customer_ids = Account::whereIn('uuid', $customer_uuid_array)->pluck('id');
                $promotion->customers()->sync($customer_ids);
            }

            //Segments
            $segment_uuid_array = array_column($request->get('segments', []), 'uuid');
            if ($segment_uuid_array) {
                $segment_ids = Segment::whereIn('uuid', $segment_uuid_array)->pluck('id');
                $promotion->segments()->sync($segment_ids);
            }


            //Apply all
            $applied_all_array = $request->get('applied_all', []);
            $applies = [];
            foreach ($applied_all_array as $apply) {
                array_push($applies, new PromotionApplied(array_merge($apply, ['place_id' => $place_id, 'uuid' => nanoId()])));
            }
            $promotion->appliedAll()->saveMany($applies);

            //Apply products
            $applied_products = $request->get('applied_products', []);
            $products_ids = Product::whereIn('uuid', Arr::pluck($applied_products, 'uuid'))->pluck('uuid', 'id')->toArray();
            foreach ($products_ids as $id => $uuid) {
                $products_ids[$id] = Arr::only(array_column($applied_products, null, 'uuid')[$uuid], ['quantity', 'discount', 'unit']);
            }
            $promotion->appliedProducts()->sync($products_ids);


            //Apply categories
            $applied_categories = $request->get('applied_categories', []);
            $category_ids = Category::whereIn('uuid', Arr::pluck($applied_categories, 'uuid'))->pluck('uuid', 'id')->toArray();
            foreach ($category_ids as $id => $uuid) {
                $category_ids[$id] = Arr::only(array_column($applied_categories, null, 'uuid')[$uuid], ['quantity', 'discount', 'unit']);
            }
            $promotion->appliedCategories()->sync($category_ids);

            //Sync orders
            $applied_orders_array = $request->get('applied_orders', []);
            $applies = [];
            foreach ($applied_orders_array as $apply) {
                array_push($applies, new PromotionApplied(array_merge($apply, ['type' => 'order', 'place_id' => $place_id, 'uuid' => nanoId()])));
            }
            $promotion->appliedOrders()->saveMany($applies);


            return $promotion;
        });

        return PromotionResource::make($promotion->load(['customers', 'segments', 'appliedAll', 'appliedOrders', 'appliedProducts', 'appliedCategories']))
            ->additional(['message' => 'Tạo thành công']);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Promotion $promotion
     *
     * @return PromotionResource
     */
    public
    function show(Promotion $promotion)
    {
        return PromotionResource::make($promotion->load(['customers', 'segments', 'appliedAll', 'appliedOrders', 'appliedProducts', 'appliedCategories']));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Promotion $promotion
     *
     * @return \Illuminate\Http\Response
     */
    public
    function update(Request $request, Promotion $promotion)
    {
        $promotion = DB::transaction(function () use ($request, $promotion) {
            $place_id = currentPlace()->id;
            $promotion->update(array_merge($request->only('title', 'description', 'code', 'quantity', 'require_coupon', 'type'),
                [
                    'start_date' => Carbon::parse($request->get('start_date', '1970-01-01 00:00:00')),
                    'end_date'   => Carbon::parse($request->get('end_date', '1970-01-01 00:00:00')),
                ]
            ));

            //Customers
            $customer_uuid_array = array_column($request->get('customers', []), 'uuid');
            if ($customer_uuid_array) {
                $customer_ids = Account::whereIn('uuid', $customer_uuid_array)->pluck('id');
                $promotion->customers()->sync($customer_ids);
            }

            //Segments
            $segment_uuid_array = array_column($request->get('segments', []), 'uuid');
            if ($segment_uuid_array) {
                $segment_ids = Segment::whereIn('uuid', $segment_uuid_array)->pluck('id');
                $promotion->segments()->sync($segment_ids);
            }


            //Apply all
            $applied_all_array = $request->get('applied_all', []);
            $applies = [];
            foreach ($applied_all_array as $apply) {
                array_push($applies, new PromotionApplied(array_merge(['place_id' => $place_id, 'uuid' => nanoId()], $apply)));
            }
            $promotion->appliedAll()->saveMany($applies);

            //Apply products
            $applied_products = $request->get('applied_products', []);
            $products_ids = Product::whereIn('uuid', Arr::pluck($applied_products, 'uuid'))->pluck('uuid', 'id')->toArray();
            foreach ($products_ids as $id => $uuid) {
                $products_ids[$id] = Arr::only(array_column($applied_products, null, 'uuid')[$uuid], ['quantity', 'discount', 'unit']);
            }
            $promotion->appliedProducts()->sync($products_ids);


            //Apply categories
            $applied_categories = $request->get('applied_categories', []);
            $category_ids = Category::whereIn('uuid', Arr::pluck($applied_categories, 'uuid'))->pluck('uuid', 'id')->toArray();
            foreach ($category_ids as $id => $uuid) {
                $category_ids[$id] = Arr::only(array_column($applied_categories, null, 'uuid')[$uuid], ['quantity', 'discount', 'unit']);
            }
            $promotion->appliedCategories()->sync($category_ids);

            //Sync orders
            $applied_orders_array = $request->get('applied_orders', []);
            $applies = [];
            foreach ($applied_orders_array as $apply) {
                array_push($applies, new PromotionApplied(array_merge(['type' => 'order', 'place_id' => $place_id, 'uuid' => nanoId()], $apply)));
            }
            $promotion->appliedOrders()->saveMany($applies);


            return $promotion;
        });

        return PromotionResource::make($promotion->load(['customers', 'segments', 'appliedAll', 'appliedOrders', 'appliedProducts', 'appliedCategories']))
            ->additional(['message' => 'Cập nhật thành công']);
    }

    public function setStatus(Promotion $promotion, PromotionRequest $request)
    {
        DB::transaction(function () use ($promotion, $request) {
            $promotion->setAttribute('status', $request->get('status', 'activated'))->save();
        }, 1);
        return response()->json(['message' => 'Đã ngừng chương trình khuyến mại']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Promotion $promotion
     *
     * @return \Illuminate\Http\Response
     * @throws \Throwable
     */
    public
    function destroy(Promotion $promotion)
    {
        DB::transaction(function () use ($promotion) {
//            $segment->criteria()->delete();
//            $segment->customers()->detach();
            $promotion->delete();
        }, 1);
        return response()->json(['message' => 'Đã ngừng chương trình khuyến mại']);
    }
}
