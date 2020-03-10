<?php

namespace App\Http\Controllers;

use App\Http\Filters\PromotionFilter;
use App\Http\Requests\PromotionRequest;
use App\Http\Resources\PromotionResource;
use App\Models\Promotion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  PromotionRequest  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(PromotionRequest $request)
    {
        $promotions = Promotion::filter(new PromotionFilter($request))
            ->orderBy('id', 'desc')
            ->simplePaginate($request->input('per_page', 20));
        return PromotionResource::collection($promotions);
    }

    public function current(PromotionRequest $request)
    {
        $promotions = Promotion::filter(new PromotionFilter($request))
            ->active()
            ->orderBy('type', 'asc') // order -> product
//            ->orderBy('id', 'desc')
            ->get();
        return PromotionResource::collection($promotions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PromotionRequest  $request
     * @return PromotionResource
     * @throws \Throwable
     */
    public function store(PromotionRequest $request)
    {
        $this->transform($request);
        $promotion = DB::transaction(
            function () use ($request) {
                $promotion = new Promotion;
                $promotion->forceFill(
                    array_merge(
                        $request->only($promotion->getFillable()),
                        [
                            'uuid'     => nanoId(),
                            'place_id' => currentPlace()->id,
                            'code'     => $request->code ?? strtoupper(Str::studly(Str::slug($request->name))),
                            'stats'    => [
                                'amount'          => 0,
                                'discount_amount' => 0,
                            ],
                        ]
                    )
                );
                $promotion->save();
                return $promotion;
            }
        );
        return PromotionResource::make($promotion)
            ->additional([ 'message' => 'Tạo thành công' ]);
    }

    protected function transform(PromotionRequest $request)
    {
        $meregeArr = $request->all();
        $applied   = $request->applied;
        $type      = $request->type;
        if ( !$applied[ 'someSegment' ] || $applied[ 'allCustomer' ] ) {
            $meregeArr[ 'segments' ] = [];
        }
        if ( !$applied[ 'someCustomer' ] || $applied[ 'allCustomer' ] ) {
            $meregeArr[ 'customers' ] = [];
        }
        if ( $type !== 'order' ) {
            $meregeArr[ 'rule' ][ 'order' ] = [];
        }
        if ( !$applied[ 'allProduct' ] || $type === 'order' ) {
            $meregeArr[ 'rule' ][ 'all' ] = [
                'minimumQty'    => null,
                'discountType'  => '%',
                'discountValue' => null,
            ];
        }
        if ( !$applied[ 'someProduct' ] || $type === 'order' ) {
            $meregeArr[ 'rule' ][ 'product' ] = [];
        }
        if ( !$applied[ 'someCategory' ] || $type === 'order' ) {
            $meregeArr[ 'rule' ][ 'category' ] = [];
        }
        $request->merge($meregeArr);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Promotion  $promotion
     * @return PromotionResource
     */
    public function show(Promotion $promotion)
    {
        return PromotionResource::make($promotion);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\PromotionRequest  $request
     * @param  \App\Models\Promotion                $promotion
     * @return \App\Http\Resources\PromotionResource
     * @throws \Throwable
     */
    public function update(PromotionRequest $request, Promotion $promotion)
    {
        $this->transform($request);
        $promotion = DB::transaction(
            function () use ($request, $promotion) {
                if ( !$promotion->state ) {
                    $promotion->forceFill(
                        array_merge(
                            $request->only($promotion->getFillable()),
                            [
                                'code' => $request->input('code', strtoupper(Str::studly(Str::slug($request->name)))),
                            ]
                        )
                    );
                }
                $promotion->state = $request->state;
                $promotion->save();
                return $promotion;
            }
        );
        return PromotionResource::make($promotion)
            ->additional([ 'message' => 'Cập nhật thành công' ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Promotion  $promotion
     * @return \Illuminate\Http\Response
     * @throws \Throwable
     */
    public function destroy(Promotion $promotion)
    {
        if ( $promotion->delete() ) {
            return response()->json([ 'message' => 'Đã xóa chương trình khuyến mãi' ]);
        }
        return response()->json([ 'message' => 'Có lỗi xảy ra' ], 500);
    }
}
