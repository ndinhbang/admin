<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceRequest;
use App\Models\Place;
use Bouncer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PlaceController extends Controller
{

    protected $place_path = 'medias/places/';

    public function getMy(Request $request)
    {
        $user = $request->user();
        $places = $user->places;
        return $places->toJson();
    }

    public function index()
    {
        // $netrooms = \App\Models\Place::select('netrooms.*')->whereNotNull('netrooms.id');

        // if (!$this->data['u']->admin) {
        //     $netrooms->join('netroom_user', 'netroom_user.netroom_id', '=', 'netrooms.id')->where('netroom_user.user_id', $this->data['u']->id);
        // }

        // $netrooms->orderBy('netrooms.'.request('sortBy', 'created_at'), 'netrooms.'.request('order', 'desc'));

        // return $netrooms->paginate((int) request('pageLength'));
    }

    public function store(PlaceRequest $request)
    {
        $request->validated();

        $user = $request->user();
        $place = null;

        \DB::transaction(function () use ($user, &$place) {
            $place           = new Place;
            $place->uuid    = $this->nanoId();
            $place->title    = request()->title;

            $place->code     = request()->code;
            $place->address  = request()->address;

            $place->contact_name  = $user->display_name;
            $place->contact_phone  = $user->phone;
            $place->contact_email  = $user->email;
            $place->status  = 'trial';
            $place->user_id  = $user->id;

            $place->save();

            // scope to place id
            Bouncer::scope()->to($place->id);
            // asign owner for place
            Bouncer::allow($user)->toOwn($place);
            // give user abilities with this place
            $abilities = config('default.permissions.tenants');
            $assignedAbilities = [];
            foreach ($abilities as $ability) {
//                foreach ($grouped as $ability) {
                Bouncer::ability()->firstOrCreate(Arr::only($ability, ['name', 'title']));
                Bouncer::allow($user)->to($ability['name']);
//                }
            }

            // attach manager role
            $user->places()->attach($place->id);
        }, 5);

//        }


        return response()->json([
            'message' => 'Thêm thông tin cửa hàng thành công!',
            'place'   => $place,
            'places'  => $user->places,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        // $netroom = \App\Netroom::find($id);

        // if (!$netroom) {
        //     return response()->json(['message' => 'Couldnot find netroom!'], 422);
        // }

        // DB::transaction(function () use ($netroom){
        //     $supplies = \App\Supply::with('products')->where('netroom_id', $netroom->id)->delete();
        //     $products = \App\Product::with('supplies')->where('netroom_id', $netroom->id)->delete();

        //     $orders = \App\Order::where('netroom_id', $netroom->id)->delete();
        //     $spends = \App\Spend::where('netroom_id', $netroom->id)->delete();
        //     $roles = Role::where('netroom_id', $netroom->id)->delete();
        //     $orderStates = \App\OrderState::where('netroom_id', $netroom->id)->delete();
        //     $discount = \App\Discount::where('netroom_id', $netroom->id)->delete();
        //     $categories = \App\Category::where('netroom_id', $netroom->id)->delete();

        //     $netroom->users()->detach();
        //     $netroom->delete();
        // });

        // return response()->json(['message' => 'Netroom deleted!']);
    }

    public function show($id)
    {
        // $netroom = \App\Netroom::where('netrooms.id', $id)->with('roles')
        //     ->first();

        // if (!$netroom) {
        //     return response()->json(['message' => 'Couldnot find netroom!'], 422);
        // }

        // return response()->json(compact('netroom'));
    }

    public function update(PlaceRequest $request, Place $place)
    {

        $user = $request->user();
        // $place = Place::curr();

        $place->title = request()->title;

        $place->code = request()->code;
        $place->address = request()->address;

        $place->contact_name = request()->contact_name;
        $place->contact_phone = request()->contact_phone;
        $place->contact_email = request()->contact_email;

        $place->save();

        return response()->json([
            'message' => 'Cập nhật thông tin cửa hàng thành công!',
            'place'   => $place,
            'places'  => $user->places,
        ]);

    }

    public function updateLogo(PlaceRequest $request)
    {
        $user = $request->user();
        $place = Place::curr();

        if (!$place) {
            return response()->json(['errors' => ['' => ['Không tìm thấy thông tin cửa hàng!']]], 422);
        }

        if ($place->logo && \File::exists($this->place_path . $place->logo)) {
            \File::delete($this->place_path . $place->logo);
        }

        $extension = $request->file('logo')->getClientOriginalExtension();
        $filename = $place->id . '-' . $place->code . "-goido.net.";

        $img = \Image::make($request->file('logo'));

        $filePath = $this->place_path . $filename . $extension;
        $img->fit(200, 200);
        $img->save($filePath);

        $place->logo = $filename . $extension;
        $place->save();

        return response()->json(['message' => 'Cập nhật ảnh đại diện thành công!', 'place' => $place]);
    }
}
