<?php

namespace App\Http\Controllers;

use App\Http\Filters\SegmentFilter;
use App\Http\Requests\SegmentRequest;
use App\Http\Resources\SegmentResource;
use App\Models\Account;
use App\Models\Criterion;
use App\Models\Segment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Throwable;

class SegmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param SegmentRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(SegmentRequest $request)
    {

        $segments = Segment::filter(new SegmentFilter($request))
            ->with(['customers', 'criteria'])
            ->orderBy('id', 'desc')
            ->simplePaginate($request->get('per_page', 50));
        return SegmentResource::collection($segments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SegmentRequest $request
     * @return SegmentResource
     * @throws Throwable
     */
    public function store(SegmentRequest $request)
    {

        $segment = DB::transaction(function () use ($request) {
            $place_id = currentPlace()->id;
            $segment = Segment::create(
                array_merge(
                    $request->only('title', 'description'),
                    [
                        'uuid' => nanoId(),
                        'place_id' => $place_id,
                    ]
                )
            );
            //SYNC CUSTOMERS
            $customer_uuid_array = array_column($request->get('customers',[]),'uuid');
            if ($customer_uuid_array){
                $customer_ids = Account::whereIn('uuid', $customer_uuid_array)->pluck('id');
                $segment->customers()->sync($customer_ids);
            }

            //SYNC CRITERIA
            $criteriaReq = $request->get('criteria', []);
            $criteria = [];
            foreach ($criteriaReq as $cre) {
                array_push($criteria, new Criterion(array_merge($cre, ['place_id' => $place_id, 'uuid' => nanoId()])));
            }
            $segment->criteria()->saveMany($criteria);
            return $segment;
        });

        return SegmentResource::make($segment->load(['customers', 'criteria']))
            ->additional(['message' => 'Tạo thành công']);
    }

    /**
     * Display the specified resource.
     *
     * @param Segment $segment
     * @return SegmentResource
     */
    public function show(Segment $segment)
    {
        return SegmentResource::make($segment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Segment $segment
     * @return SegmentResource
     * @throws Throwable
     */
    public function update(Request $request, Segment $segment)
    {


        $segment = DB::transaction(function () use ($request, $segment) {
            $place_id = currentPlace()->id;;

            $segment->title = $request->input('title');
            $segment->description = $request->input('description');
            $segment->save();

            //SYNC CUSTOMERS
            $customer_uuid_array = array_column($request->get('customers',[]),'uuid');
            if ($customer_uuid_array){
                $customer_ids = Account::whereIn('uuid', $customer_uuid_array)->pluck('id');
                $segment->customers()->sync($customer_ids);
            }

            //SYNC CRITERIA*/
            $segment->criteria()->delete();
            $criteriaReq = $request->get('criteria', []);
            foreach ($criteriaReq as $cre) {
                $cre['uuid'] = $cre['uuid'] ?? nanoId();
                $segment->criteria()->updateOrCreate(['uuid' => $cre['uuid']], array_merge($cre, ['place_id' => $place_id]));
            }
            return $segment;
        });

        return SegmentResource::make($segment->load(['customers', 'criteria']))
            ->additional(['message' => 'Cập nhật thành công nhóm '.$segment->title ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Segment $segment
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     * @throws Throwable
     */
    public function destroy(Segment $segment)
    {
        DB::transaction(function () use ($segment) {
            $segment->criteria()->delete();
            $segment->customers()->detach();
            $segment->delete();
        }, 5);
        return response()->json([ 'message' => 'Đã xoá nhóm khách hàng' ]);
    }
}
