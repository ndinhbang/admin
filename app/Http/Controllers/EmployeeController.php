<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\EmployeeRequest;

class EmployeeController extends Controller
{

    protected $avatar_path = 'medias/avatars/';
    
    public function index(Request $request)
    {
        $user = $request->user();

        $place = $user->places;

        $users = \App\User::select('users.*')
            ->join('place_user', 'place_user.user_id', '=', 'users.id')
            ->join('places', 'places.id', '=', 'place_user.place_id')
            ->where('places.id', request()->place_id)
            ->paginate(10);

        return $users->toJson();
    }

    public function store(EmployeeRequest $request)
    {
        $user = $request->user();

        $place = \App\Models\Place::find(request()->place_id);

        if (!$place) {
            return response()->json(['errors' => ['' => ['Không tìm thấy thông tin cửa hàng!']]], 422);
        }

        $employee = new \App\User;
        $employee->uuid = $this->nanoId();
        $employee->display_name = request()->display_name;
        $employee->name = request()->name;
        $employee->email = request()->email;
        $employee->phone = request()->phone;
        $employee->password = \Hash::make(request()->password);

        $employee->save();

        // thêm nhân viên vào cửa hàng
        $employee->places()->attach($place->id);

        return response()->json(['message' => 'Thêm nhân viên thành công!', 'employee' => $employee]);
    }

    public function update(EmployeeRequest $request, \App\User $employee)
    {
        $employee->display_name = request('display_name');
        $employee->name = request('name');
        $employee->email = request('email');
        $employee->phone = request('phone');

        if(request()->password)
        {
            $employee->password = \Hash::make(request()->password);
        }
        
        $employee->save();

        return response()->json(['message' => 'Cập nhật thông tin nhân viên thành công!', 'employee' => $employee]);
    }

    public function updateAvatar(EmployeeRequest $request, $uuid)
    {
        $employee = \App\User::where('uuid', $uuid)->first();

        if (!$employee) {
            return response()->json(['errors' => ['' => ['Không tìm thấy thông tin nhân viên!']]], 422);
        }

        $extension = $request->file('avatar')->getClientOriginalExtension();
        $filename  = $employee->uuid.'-'.$employee->name. "-goido.net.";

        $img       = \Image::make($request->file('avatar'));

        $filePath = $this->avatar_path . $filename . $extension;
        $img->fit(200, 200);
        $img->save($filePath);

        $employee->avatar = $filename . $extension;
        $employee->save();

        return response()->json(['message' => 'Cập nhật ảnh đại diện thành công!', 'employee' => $employee]);
    }
}
