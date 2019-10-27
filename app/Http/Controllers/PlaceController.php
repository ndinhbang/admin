<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Place;
use App\Http\Requests\PlaceRequest;
use Illuminate\Support\Str;
use Validator;

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

    public function update(PlaceRequest $request, $id)
    {

        $user = $request->user();
        $place = \App\Models\Place::find($id);

        if (!$place) {
            return response()->json(['errors' => ['' => ['Không tìm thấy thông tin cửa hàng!']]], 422);
        }

        \DB::transaction(function () use ($user, &$place) {
            $place->title    = request()->title;

            $place->code     = Str::slug(request()->title);
            $place->address  = request()->address;

            $place->contact_name  = request()->contact_name;
            $place->contact_phone  = request()->contact_phone;
            $place->contact_email  = request()->contact_email;

            $place->save();
        }, 5);

        return response()->json(['message' => 'Cập nhật thông tin cửa hàng thành công!', 'place' => $place, 'places' => $user->places]);

    }

    public function updateLogo(PlaceRequest $request)
    {
        $user = $request->user();
        $place = \App\Models\Place::find(request()->place_id);

        if (!$place) {
            return response()->json(['errors' => ['' => ['Không tìm thấy thông tin cửa hàng!']]], 422);
        }

        if ($place->logo && \File::exists($this->place_path . $place->logo)) {
            \File::delete($this->place_path . $place->logo);
        }

        $extension = $request->file('logo')->getClientOriginalExtension();
        $filename  = $place->id.'-'.$place->code. "-goido.net.";

        $img       = \Image::make($request->file('logo'));

        $filePath = $this->place_path . $filename . $extension;
        $img->fit(200, 200);
        $img->save($filePath);

        $place->logo = $filename . $extension;
        $place->save();

        return response()->json(['message' => 'Cập nhật ảnh đại diện thành công!', 'place' => $place]);
    }
}
