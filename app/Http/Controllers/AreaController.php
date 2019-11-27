<?php

namespace App\Http\Controllers;

use App\Http\Filters\AreaFilter;
use App\Http\Requests\AreaRequest;
use App\Http\Resources\AreaResource;
use App\Models\Area;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  AreaRequest  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(AreaRequest $request)
    {
        $areas = Area::with([
            'tables' => function ($query) {
                $query->withCount(['orders']);
            },
        ])
            ->filter(new AreaFilter($request))
            ->orderBy('areas.id', 'desc')
            ->simplePaginate(50);
        return AreaResource::collection($areas);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\AreaRequest  $request
     * @return \Illuminate\Http\Response
     * @throws \Throwable
     */
    public function store(AreaRequest $request)
    {
        // todo: gioi han so luong area dc tao cua 1 place ~50
        $area = DB::transaction(function () use ($request) {
            $placeId         = currentPlace()->id;
            $area            = Area::create(array_merge($request->only('name'), [
                'uuid'     => nanoId(),
                'place_id' => $placeId,
            ]));
            $alsoCreateTable = $request->alsoCreateTable ?? false;
            $quantity        = $request->table_quantity ?? 0;
            if ( $alsoCreateTable && $quantity ) {
                for ( $i = 0; $i < $quantity; $i++ ) {
                    Table::create([
                        'area_id'  => $area->id,
                        'place_id' => $area->place_id,
                        'uuid'     => nanoId(),
                        'name'     => $area->name . '-' . ( $i + 1 ),
                    ]);
                }
            }
            return $area;
        }, 5);
        return response()->json([
            'message' => 'Tạo thành công',
            'data'    => new AreaResource($area),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Area  $area
     * @return AreaResource
     */
    public function show(Area $area)
    {
        $area->load('tables');
        return new AreaResource($area);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Area                      $area
     * @return AreaResource
     */
    public function update(Request $request, Area $area)
    {
        $area->name = $request->input('name');
        $area->save();
        $area->load('tables');
        return new AreaResource($area);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Area  $area
     * @return \Illuminate\Http\Response
     * @throws \Throwable
     */
    public function destroy(Area $area)
    {
        DB::transaction(function () use ($area) {
            $area->tables()
                ->delete();
            $area->delete();
        }, 5);
        return response()->json([ 'message' => 'Deleted' ]);
    }
}