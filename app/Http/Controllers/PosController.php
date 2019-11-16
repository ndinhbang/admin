<?php

namespace App\Http\Controllers;

use App\Http\Requests\PosRequest;
use App\Http\Resources\PosOrderResource;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Filters\OrderFilter;

class PosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(PosRequest $request)
    {
        $orders = Order::with(['creator', 'customer'])
            ->filter(new OrderFilter($request))
            ->orderBy('orders.id', 'desc')
            ->paginate(6);

        return PosOrderResource::collection($orders);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return PosOrderResource
     * @throws \Exception
     */
    public function store(PosRequest $request)
    {
        $now = Carbon::now();

        $order = Order::create(
            array_merge($request->only(['kind']), [
                'uuid'       => nanoId(),
                'place_id'   => currentPlace()->id,
                'creator_id' => $request->user()->id,
                'code'       => $request->input('code'),
                'year'       => $now->year,
                'month'      => $now->month,
                'day'        => $now->day,
            ])
        );

        return new PosOrderResource($order);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
