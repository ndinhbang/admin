<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Place;
use App\Http\Requests\PlaceRequest;
use Illuminate\Support\Str;
use Validator;

class PlaceController extends Controller
{
    public function getMy(Request $request)
    {
        $user = $request->user();
        $places = $user->places;
        dump($places);
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
        $user = $request->user();

        $place = null;

        \DB::transaction(function () use ($user, &$place) {
            $place           = new \App\Models\Place;
            $place->title    = request()->title;

            $place->code     = Str::slug(request()->title);
            $place->address  = request()->address;

            $place->contact_name  = $user->display_name;
            $place->contact_phone  = $user->phone;
            $place->contact_email  = $user->email;
            $place->status  = 'trial';
            $place->user_id  = $user->id;

            $place->save();

            // add default roles
            // foreach (config('demo.roles') as $slug => $r) {

            //     $role = new Role;
            //     $role->name = $slug.'.'.$netroom->id;
            //     $role->display_name = $r['display_name'];
            //     $role->netroom_id = $netroom->id;
            //     $role->save();

            //     switch ($role->name) {
            //         case 'manager'.'.'.$netroom->id:
            //             $role->givePermissionTo(Permission::all());
            //             $user->assignRole('manager'.'.'.$netroom->id);
            //             break;

            //         case 'cashier'.'.'.$netroom->id:
            //             $role->givePermissionTo(config('demo.roles')['cashier']['permissions']);
            //             break;

            //         case 'waiter'.'.'.$netroom->id:
            //             $role->givePermissionTo(config('demo.roles')['waiter']['permissions']);
            //             break;

            //         case 'chef'.'.'.$netroom->id:
            //             $role->givePermissionTo(config('demo.roles')['chef']['permissions']);
            //             break;

            //         default:
            //             # code...
            //             break;
            //     }
            // }

            // add order status records for netroom
            // $defaultStates = config('order.states');

            // $managerRole = Role::where('name', 'manager'.'.'.$netroom->id)->where('guard_name', 'api')->first();

            // $stateDatas = [];
            // foreach ($defaultStates as $status) {
            //     $data = $status;
            //     $data['netroom_id'] = $netroom->id;
            //     $data['levels'] = json_encode([$managerRole->toArray()]);
            //     $data['created_at'] = Carbon::now();
            //     $data['updated_at'] = Carbon::now();
            //     $stateDatas[] = $data;
            // }

            // \App\OrderState::insert($stateDatas);

            // $suppliesArr = [];

            // // init supplies
            // foreach (config('demo.supplies') as $key => $s) {
            //     $supply = new \App\Supply;
            //     $supply->name = $s['name'];
            //     $supply->unit_id = $s['unit_id'];
            //     $supply->netroom_id = $netroom->id;
            //     $supply->save();

            //     $suppliesArr[$supply->name] = $supply->id;
            // }

            // // init products
            // foreach (config('demo.products') as $key => $s) {
            //     $product = new \App\Product;
            //     $product->name = $s['name'];
            //     $product->price = $s['price'];
            //     $product->thumbnail = $s['thumbnail'];
            //     $product->category_id = $s['category_id'];
            //     $product->netroom_id = $netroom->id;
            //     $product->status = 1;
            //     $product->save();

            //     if(isset($s['supplies']) && count($s['supplies'])) {
            //         foreach ($s['supplies'] as $key => $ps) {
            //             if(isset($ps['supply_name']) && isset($suppliesArr[$ps['supply_name']]) && $suppliesArr[$ps['supply_name']])
            //                 $product->supplies()->attach($suppliesArr[$ps['supply_name']], ['quantity' => (int) $ps['quantity']]);
            //         }
            //     }
            // }

            // attach manager role
            $user->places()->attach($place->id);

            // set active netroom
            // $this->data['u']->place_id = $netroom->id;
            // $this->data['u']->save();
        }, 5);


        $netrooms = $user->netrooms;

        return response()->json(['message' => 'Thêm thông tin cửa hàng thành công!', 'place' => $place, 'places' => $user->places]);
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

    public function update(Request $request, $id)
    {

        // $netroom = \App\Netroom::find($id);

        // if (!$netroom) {
        //     return response()->json(['message' => 'Couldnot find netroom!']);
        // }

        // $validation = Validator::make($request->all(), [
        //     'title'   => 'required|min:3',
        //     'address' => 'required|min:10',
        //     'mobile'  => 'required|digits_between:9,11',
        // ]);

        // if ($validation->fails()) {
        //     return response()->json(['message' => $validation->messages()->first()], 422);
        // }

        // // return response()->json(request()->all());

        // $netroom->title       = request()->title;
        // $netroom->address     = request()->address;
        // $netroom->mobile      = request()->mobile;
        // $netroom->description = request()->description;

        // if ($this->data['u']->admin) {
        //     $netroom->status       = request()->status;
        //     $netroom->expired_date = request()->expired_date;
        // }
        // $netroom->save();

        // $user = \JWTAuth::parseToken()->authenticate();

        // $netrooms = \App\Netroom::whereUserId($user->id)->get();

        // return response()->json(['message' => 'Cập nhật thành công!', 'netroom' => $netroom, 'netrooms' => $netrooms]);
    }

    public function updateUserLevel(Request $request)
    {

        // $id     = request()->id;
        // $userId = request()->user_id;
        // $level  = request()->level;
        // $remove = request()->remove;
        // $user   = \App\User::find($userId);

        // // check exist netroom user
        // $existsLevel = $user->netrooms()->where('netroom_user.netroom_id', $id)->where('netroom_user.level', $level)->first();

        // // return response()->json(compact('existsLevel'));

        // if ($remove) {
        //     // remove all level
        //     $user->netrooms()->detach($id);
        // } else {
        //     if ($level && $existsLevel) {
        //         // update level
        //         $user->netrooms()->updateExistingPivot($id, ['level' => $level, 'active' => 1]);
        //     } elseif ($level) {
        //         // remove all level
        //         $user->netrooms()->detach($id);
        //         // add level
        //         $user->netrooms()->attach($id, ['level' => $level, 'active' => 1]);
        //     }
        // }

        // $userNetrooms = $user->netrooms;

        // return response()->json(compact('userNetrooms'));
    }
}
