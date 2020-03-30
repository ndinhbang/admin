<?php

namespace App\Http\Controllers;

use App\Http\Filters\SegmentFilter;
use App\Http\Requests\SegmentRequest;
use App\Http\Resources\SegmentResource;
use App\Models\Account;
use App\Models\Segment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class SegmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  SegmentRequest  $request
     * @return AnonymousResourceCollection
     */
    public function index(SegmentRequest $request)
    {
        $builder = Segment::filter(new SegmentFilter($request))
            ->orderBy('id', 'desc');
        return SegmentResource::collection($builder->simplePaginate($request->per_page ?? 20));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  SegmentRequest  $request
     * @return SegmentResource
     * @throws Throwable
     */
    public function store(SegmentRequest $request)
    {
        // todo: giới hạn 20 nhóm KH
        $segment = DB::transaction(
            function () use ($request) {
                $placeId = currentPlace()->id;
                $segment = new Segment;
                $segment->forceFill(
                    array_merge(
                        $request->only([ 'name', 'desc', 'conditions' ]),
                        [
                            'uuid'     => nanoId(),
                            'place_id' => $placeId,
                        ]
                    )
                );
                $segment->save();
                // sync customers
                $customers = new Collection($request->input('fixedCustomers', []));
                if ( !$customers->isEmpty() ) {
                    $keyedData = Account::select('id')
                        ->whereIn('uuid', $customers->pluck('uuid'))
                        ->get()
                        ->mapWithKeys(
                            function ($row) {
                                return [ $row[ 'id' ] => [ 'is_fixed' => true ] ];
                            }
                        );
                    $segment->customers()->sync($keyedData->toArray());
                }
                $conditions = $request->conditions ?? [];
                if (!empty($conditions)) {
                    $pivotData = [];
                    $customers = Account::isCustomer()->cursor();
                    foreach ($customers as $customer) {
                        if ($customer->isSatisfiedAllConditions($conditions)) {
                            $pivotData[] = $customer->id;
                        }
                    }
                    $segment->customers()->syncWithoutDetaching($pivotData);
                }
                return $segment;
            }
        );
        $segment->load('fixedCustomers');
        return ( new SegmentResource($segment) )
            ->additional([ 'message' => 'Tạo thành công' ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Http\Requests\SegmentRequest  $request
     * @param  \App\Models\Segment                $segment
     * @return SegmentResource
     */
    public function show(SegmentRequest $request, Segment $segment)
    {
        $segment->load('fixedCustomers');
        return ( new SegmentResource($segment) );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\SegmentRequest  $request
     * @param  Segment                            $segment
     * @return SegmentResource
     * @throws \Throwable
     */
    public function update(SegmentRequest $request, Segment $segment)
    {
        $segment = DB::transaction(
            function () use ($request, $segment) {
                $segment->fill($request->only([ 'name', 'desc', 'conditions' ]));
                $segment->save();
                // sync customers
                $customers = new Collection($request->input('fixedCustomers', []));
                if ( !$customers->isEmpty() ) {
                    $keyedData = Account::select('id')
                        ->whereIn('uuid', $customers->pluck('uuid'))
                        ->get()
                        ->mapWithKeys(
                            function ($row) {
                                return [ $row[ 'id' ] => [ 'is_fixed' => true ] ];
                            }
                        );
                    $segment->customers()->sync($keyedData->toArray());
                }
                $conditions = $request->conditions ?? [];
                if (!empty($conditions)) {
                    $pivotData = [];
                    $customers = Account::isCustomer()->cursor();
                    foreach ($customers as $customer) {
                        if ($customer->isSatisfiedAllConditions($conditions)) {
                            $pivotData[] = $customer->id;
                        }
                    }
                    $segment->customers()->syncWithoutDetaching($pivotData);
                }
                return $segment;
            }
        );
        $segment->load('fixedCustomers');
        return ( new SegmentResource($segment) )
            ->additional([ 'message' => 'Cập nhật thành công' ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Segment  $segment
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     * @throws Throwable
     */
    public function destroy(Segment $segment)
    {
        DB::transaction(
            function () use ($segment) {
                $segment->fixedCustomers()->detach();
                $segment->delete();
            },
            5
        );
        return response()->json([ 'message' => 'Đã xoá nhóm khách hàng' ]);
    }
}
