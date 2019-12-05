<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PlaceRequest;
use App\Models\Place;
use App\Models\Role;
use App\Models\Permission;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//use Bouncer;

class PlaceController extends Controller
{

    protected $place_path = 'medias/places/';

    public function index(Request $request)
    {
        $places = Place::select('places.*')
            ->with('user')
            ->join('place_user', 'place_user.place_id', '=', 'places.id')
            ->groupBy('places.id')
            ->orderBy('id', 'desc')
            ->paginate($request->per_page);

        return $places->toJson();
    }

    public function store(PlaceRequest $request)
    {
        $place = \DB::transaction(function () use ($request) {

            if($user = User::findUuid($request->user['uuid'])) {
                // print templates
                $templates = [
                    'pos80' => minifyHtml(view('print.templates.pos80')->render()),
                    'pos58' => minifyHtml(view('print.templates.pos58')->render()),
                ];

                $arr = array_merge($request->all(), [
                    'uuid'          => nanoId(),
                    'contact_name'  => $user->display_name,
                    'contact_phone' => $user->phone,
                    'contact_email' => $user->email,
                    'status'        => 'trial',
                    'user_id'       => $user->id,
                    'print_templates' => $templates,
                    'print_config' => config('default.print.config', [])
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

                    // Gán role chủ cửa hàng cho chủ quản
                    if ($role->level == 50) {
                        $user->assignRole($role);
                    }

                    $rolePermissions = [];

                    // Gán permission cho role tương ứng
                    foreach ($permissions as $perm) {
                        foreach ($perm['roles'] as $roleName) {
                            if ($role->name == vsprintf($roleName, $place->uuid)) {
                                $rolePermissions[] = $perm['name'];
                            }
                        }
                    }

                    if(count($rolePermissions))
                        $role->givePermissionTo($rolePermissions);
                }
                return $place;
            }
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

    public function show(Place $place)
    {
        $place->load(['user']);

        $placeUsers = \App\User::select('users.*')
            ->join('place_user', 'place_user.user_id', '=', 'users.id')
            ->join('places', 'places.id', '=', 'place_user.place_id')
            ->where('places.id', $place->id)
            ->groupBy('users.id')->with(['roles' => function($q) use ($place){
                $q->where('roles.place_id', $place->id);
            }])->get();

        return response()->json(compact('place', 'placeUsers'));
    }

    public function update(PlaceRequest $request, Place $place)
    {

        $place = \DB::transaction(function () use ($place, $request) {
            $place->title = $request->title;

            $place->code = $request->code;
            $place->address = $request->address;

            $place->contact_name = $request->contact_name;
            $place->contact_phone = $request->contact_phone;
            $place->contact_email = $request->contact_email;

            // $oldBoss = User::find($place->user_id);

            // $bossRole = Role::findByName('boss__'.$place->uuid);

            // if($request->user['uuid'] !== $oldBoss->uuid) {
            //     $newBoss = User::findUuid($request->user['uuid']);

            //     if(!is_null($newBoss)) {
            //         // Bỏ quyền boss user cũ
            //         if($oldBoss->hasRole([$bossRole])) {
            //             $oldBoss->removeRole($bossRole);
            //             $oldBoss->places()->dettach($place->id);
            //         }

            //         // Gán quyền boss cho user mới
            //         $newBoss->assignRole($bossRole);
            //         $newBoss->places()->attach($place->id);

            //         $place->user_id = $newBoss->id;
            //     }
            // }

            $place->save();

            return $place;
        });

        return response()->json([
            'message' => 'Cập nhật thông tin cửa hàng thành công!',
            'place'   => $place->with('user')->first()
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
