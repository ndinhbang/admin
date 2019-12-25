<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlaceRequest;
use App\Models\Place;
use App\Models\Role;
use App\User;
use Illuminate\Http\Request;

//use Bouncer;
class PlaceController extends Controller
{
    protected $place_path = 'medias/places/';

    public function index( Request $request )
    {
        $places = Place::select('places.*')
            ->with('user')
            ->join('place_user', 'place_user.place_id', '=', 'places.id')
            ->groupBy('places.id')
            ->orderBy('id', 'desc')
            ->paginate($request->per_page);
        return $places->toJson();
    }

    public function store( PlaceRequest $request )
    {
        $place = \DB::transaction(function () use ( $request ) {
            if ( $user = User::findUuid($request->user['uuid']) ) {
                // print templates
                $templates = [
                    'pos80' => minifyHtml(view('print.templates.pos80')->render()),
                    'pos58' => minifyHtml(view('print.templates.pos58')->render()),
                ];
                $arr       = array_merge($request->all(), [
                    'uuid'            => nanoId(),
                    'contact_name'    => $user->display_name,
                    'contact_phone'   => $user->phone,
                    'contact_email'   => $user->email,
                    'status'          => 'trial',
                    'user_id'         => $user->id,
                    'print_templates' => $templates,
                    'print_config'    => config('default.print.config', []),
                ]);
                $place     = Place::create($arr);
                $user->places()
                    ->attach($place->id);
                $roles       = config('default.roles.place');
                $permissions = config('default.permissions');
                // create place roles
                foreach ( $roles as $r ) {
                    $role = Role::create([
                        'uuid'     => nanoId(),
                        'name'     => vsprintf($r['name'], $place->uuid),
                        'title'    => $r['title'],
                        'level'    => $r['level'],
                        'place_id' => $place->id,
                    ]);
                    // Gán role chủ cửa hàng cho chủ quản
                    if ( $role->level == 50 ) {
                        $user->assignRole($role);
                    }
                    $rolePermissions = [];
                    // Gán permission cho role tương ứng
                    foreach ( $permissions as $perm ) {
                        foreach ( $perm['roles'] as $roleName ) {
                            if ( $role->name == vsprintf($roleName, $place->uuid) ) {
                                $rolePermissions[] = $perm['name'];
                            }
                        }
                    }
                    if ( count($rolePermissions) ) {
                        $role->givePermissionTo($rolePermissions);
                    }
                }
                return $place;
            }
        }, 5);
        return response()->json([
            'message' => 'Thêm thông tin cửa hàng thành công!',
            'place'   => $place->with('user')
                ->first(),
            'places'  => $request->user()->places,
        ]);
    }

    public function destroy( Request $request, $id )
    {
    }

    public function show( Place $place )
    {
        $place->load([ 'user' ]);
        $placeUsers = \App\User::select('users.*')
            ->join('place_user', 'place_user.user_id', '=', 'users.id')
            ->join('places', 'places.id', '=', 'place_user.place_id')
            ->where('places.id', $place->id)
            ->groupBy('users.id')
            ->with([
                'roles' => function ( $q ) use ( $place ) {
                    $q->where('roles.place_id', $place->id);
                },
            ])
            ->get();
        return response()->json(compact('place', 'placeUsers'));
    }

    public function update( PlaceRequest $request, Place $place )
    {
        $place = \DB::transaction(function () use ( $place, $request ) {
            $place->title         = $request->title;
            $place->code          = $request->code;
            $place->address       = $request->address;
            $place->contact_name  = $request->contact_name;
            $place->contact_phone = $request->contact_phone;
            $place->contact_email = $request->contact_email;
            $place->save();
            return $place;
        });
        return response()->json([
            'message' => 'Cập nhật thông tin cửa hàng thành công!',
            'place'   => $place->with('user')
                ->first(),
        ]);
    }
}
