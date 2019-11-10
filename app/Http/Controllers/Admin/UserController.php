<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{

    protected $avatar_path = 'images/users/';

    /**
     * Display a listing of the resource.
     *
     * @param UserFilter $filters
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $account = User::where(function ($query) use ($request) {
                if($request->keyword) {
                    $query->orWhere('display_name', 'like', '%'.$request->keyword.'%');
                    $query->orWhere('email', 'like', '%'.$request->keyword.'%');
                    $query->orWhere('phone', 'like', '%'.$request->keyword.'%');
                }
            })->orderBy('id', 'desc')->take(20)->get();
        
        return $account->toJson();
    }

    public function store(EmployeeRequest $request)
    {
        \DB::enableQueryLog();
        $employee = DB::transaction(function () use ($request) {
            $currentPlace = currentPlace();
            $arr = $request->all();
            $arr['uuid'] = nanoId();
            $arr['password'] = \Hash::make($request->password);
            // create employee
            $employee = User::create($arr);
            // assign employee to place
            $employee->places()->attach($currentPlace->id);

            // assign roles for employee
            $roleNames = $request->input('role_names', []);
            
            $employee->assignRole($roleNames);

            return $employee;
        }, 5);

        dump(\DB::getQueryLog());

        return response()->json([
            'message'  => 'Thêm nhân viên thành công!',
            'employee' => $employee,
        ]);
    }

    public function update(EmployeeRequest $request, \App\User $employee)
    {
        $employee->display_name = $request->display_name;
        $employee->name = $request->name;
        $employee->email = $request->email;
        $employee->phone = $request->phone;

        $roleNames = $request->input('role_names', []);
        $employee->syncRoles($roleNames);

        if ($request->password) {
            $employee->password = \Hash::make($request->password);
        }

        $employee->save();

        return response()->json(['message' => 'Cập nhật thông tin nhân viên thành công!', 'employee' => $employee]);
    }
}
