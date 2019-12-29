<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller {

	protected $avatar_path = 'medias/avatars/';

	public function index(EmployeeRequest $request) {
		$currentPlace = currentPlace();
		$users = \App\User::with('roles')
			->where(function ($query) use ($request) {
				if ($request->keyword) {
					$query->where('users.display_name', 'like', '%' . $request->keyword . '%');
				}
			})
			->whereHas('places', function ($query) use ($currentPlace) {
                $query->where('places.id', $currentPlace->id);
            })
			->paginate($request->per_page);

		return $users->toJson();
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @param CategoryRequest $request
	 * @param CategoryFilter  $filter
	 * @return \Illuminate\Http\Response
	 */
	public function all_active(Request $request) {
		$employees = \App\User::with('roles')
			->orderBy('name', 'asc')
			->where('status', 'activated')
			->get();
		return $employees->toJson();
	}

	public function store(EmployeeRequest $request) {
		// \DB::enableQueryLog();
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

		// dump(\DB::getQueryLog());

		return response()->json([
			'message' => 'Thêm nhân viên thành công!',
			'employee' => $employee,
		]);
	}

	public function update(EmployeeRequest $request, \App\User $employee) {
		$employee->display_name = $request->display_name;
		$employee->name = $request->name;
		$employee->email = $request->email;
		$employee->phone = $request->phone;

		$roleNames = $request->input('role_names', []);
		$employee->syncPlaceRoles(currentPlace()->id, $roleNames);

		if ($request->password) {
			$employee->password = \Hash::make($request->password);
		}

		$employee->save();

		return response()->json(['message' => 'Cập nhật thông tin nhân viên thành công!', 'employee' => $employee]);
	}

	public function updateAvatar(EmployeeRequest $request, $uuid) {
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
