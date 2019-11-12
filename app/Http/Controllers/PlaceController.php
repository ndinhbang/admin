<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceRequest;
use App\Models\Place;
use App\Models\Role;
use Illuminate\Http\Request;

//use Bouncer;

class PlaceController extends Controller
{

    protected $place_path = 'medias/places/';

    public function getMy(Request $request)
    {
        $user = $request->user();

        $roles = $user->roles;
        $permissions = $user->getAllPermissions();

        // Cần lấy cả uuid của chủ cửa hàng để đối chiếu phân quyền
        $places = Place::select('places.*')
            ->with('user')
            ->join('place_user', 'place_user.place_id', '=', 'places.id')
            ->where('place_user.user_id', $user->id)
            ->get();

        return response()->json(compact('user', 'roles', 'permissions', 'places'));
    }

    public function index()
    {

    }

    public function store(PlaceRequest $request)
    {
        $place = \DB::transaction(function () use ($request) {
            $user = $request->user();
            $arr = array_merge($request->all(), [
                'uuid'          => nanoId(),
                'contact_name'  => $user->display_name,
                'contact_phone' => $user->phone,
                'contact_email' => $user->email,
                'status'        => 'trial',
                'user_id'       => $user->id,
            ]);

            $place = Place::create($arr);
            $user->places()->attach($place->id);

            $roles = config('default.roles.place');
            $permissions = config('default.permissions');

            // create place roles
            foreach ($roles as $r) {
                $role = Role::create([
                    'uuid'     => nanoId(),
                    'name'     => vsprintf($r['name'], $place->uuid),
                    'title'    => $r['title'],
                    'level'    => $r['level'],
                    'place_id' => $place->id,
                ]);

                // Gán role chủ cửa hàng cho người tạo
                if ($role->level == 50) {
                    $user->assignRole($role);
                }

                // Gán permission cho role tương ứng
                foreach ($permissions as $perm) {
                    foreach ($perm['roles'] as $roleName) {
                        if ($role->name == vsprintf($roleName, $place->uuid)) {
                            $role->givePermissionTo([$perm['name']]);
                        }
                    }
                }
            }
            // return data from within transaction
            return $place;
        }, 5);

        return response()->json([
            'message' => 'Thêm thông tin cửa hàng thành công!',
            'place'   => $place->with('user')->first(),
            'places'  => $request->user()->places,
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

        $place->title = $request->title;

        $place->code = $request->code;
        $place->address = $request->address;

        $place->contact_name = $request->contact_name;
        $place->contact_phone = $request->contact_phone;
        $place->contact_email = $request->contact_email;

        $place->save();

        return response()->json([
            'message' => 'Cập nhật thông tin cửa hàng thành công!',
            'place'   => $place->with('user')->first(),
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

        return response()->json([
            'message' => 'Cập nhật ảnh đại diện thành công!',
            'place'   => $place->with('user')->first(),
        ]);
    }
}
