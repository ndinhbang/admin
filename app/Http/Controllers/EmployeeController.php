<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{

    protected $avatar_path = 'medias/avatars/';

    public function index(Request $request)
    {

        $users = \App\User::paginate(10);

        return $users->toJson();
    }

    public function store(EmployeeRequest $request)
    {
        $employee = DB::transaction(function () use ($request) {
            $arr = $request->all();
            $arr['uuid'] = $this->nanoId();
            $arr['password'] = \Hash::make($request->password);
            // create employee
            $employee = User::create($arr);
            // assign employee to place
            $employee->places()->attach($request->place->id);

            $roles = $request->input('roles');
            // assign roles for employee
            foreach ($roles as $role) {
                $employee->assign($role);
            }

            return $employee;
        }, 5);

        return response()->json([
            'message' => 'Thêm nhân viên thành công!',
            'employee' => $employee
        ]);
    }

    public function update(EmployeeRequest $request, \App\User $employee)
    {
        $employee->display_name = $request->display_name;
        $employee->name = $request->name;
        $employee->email = $request->email;
        $employee->phone = $request->phone;

        if ($request->password) {
            $employee->password = \Hash::make($request->password);
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
        $filename = $employee->uuid . '-' . $employee->name . "-goido.net.";

        $img = \Image::make($request->file('avatar'));

        $filePath = $this->avatar_path . $filename . $extension;
        $img->fit(200, 200);
        $img->save($filePath);

        $employee->avatar = $filename . $extension;
        $employee->save();

        return response()->json(['message' => 'Cập nhật ảnh đại diện thành công!', 'employee' => $employee]);
    }
}
