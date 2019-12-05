<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceRequest;
use App\Models\Place;
use App\Models\Role;
use Illuminate\Http\Request;

//use Bouncer;

class PlaceController extends Controller {

	protected $place_path = 'medias/places/';

	public function getMy(Request $request) {
		$user = $request->user();

		// Cần lấy cả uuid của chủ cửa hàng để đối chiếu phân quyền
		$places = Place::select('places.*')
			->with('user')
            ->with('users')
			->join('place_user', 'place_user.place_id', '=', 'places.id')
			->where('place_user.user_id', $user->id)
			->get();

		$currentPlace = null;
		$permissions = [];

		// lấy điểm đầu tiên nếu ko chỉ định
		if ($places->count() == 1) {
			$currentPlace = $places->first();
		} else {

			$placeUuid = request()->header('X-Place-Id');
			if (!is_null($placeUuid) || $placeUuid != 'undefined') {
				$currentPlace = Place::findUuid($placeUuid);
			}

			if (is_null($currentPlace)) {
				$currentPlace = $places->first();
			}
		}

		if (!is_null($currentPlace)) {
			$currentPlace->load(['user']);
		}

		// $roles = $user->roles($currentPlace->id)->get();
		if ($user->hasAnyRole(['superadmin', 'admin'])) {
			$permissions = $user->getAllPermissions()->pluck('name')->toArray();
		} else {
			if (isset($currentPlace->id)) {
				$permissions = $user->getPermissionsOnPlace($currentPlace->id)->pluck('name')->toArray();
			}
		}

		// lay vi tri co level cao nhat cua user
		$maxRoleLevel = $user->roles($currentPlace->id ?? 0)->max('level');
		// chi cho phep nguoi dung phan cac vi tri co level thap hon
		$roles = Role::where('level', '<', $maxRoleLevel ?? 0)
			->get();

		return response()->json(compact('user', 'permissions', 'roles', 'places', 'currentPlace'));
	}

	public function index() {

	}

	public function store(PlaceRequest $request) {
		$place = \DB::transaction(function () use ($request) {
			$user = $request->user();
			$arr = array_merge($request->all(), [
				'uuid' => nanoId(),
				'contact_name' => $user->display_name,
				'contact_phone' => $user->phone,
				'contact_email' => $user->email,
				'status' => 'trial',
				'user_id' => $user->id,
			]);

			$place = Place::create($arr);
			$user->places()->attach($place->id);

			$roles = config('default.roles.place');
			$permissions = config('default.permissions');

			// create place roles
			foreach ($roles as $r) {
				$role = Role::create([
					'uuid' => nanoId(),
					'name' => vsprintf($r['name'], $place->uuid),
					'title' => $r['title'],
					'level' => $r['level'],
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
			'place' => $place->load(['user']),
			'places' => $request->user()->places,
		]);
	}

	public function destroy(Request $request, $id) {
        //
	}

	// public function printers(PlaceRequest $request, Place $place)
	// {
	// 	$place->printers = $request->printers;
	// 	$place->save();

	// 	return response()->json([
	// 		'message' => 'Lưu cấu hình máy in thành công!',
	// 		'printers' => $place->printers,
	// 	]);
	// }

	public function update(PlaceRequest $request, Place $place) {

		$user = $request->user();

		$place->title = $request->title;

		$place->code = $request->code;
		$place->address = $request->address;

		$place->contact_name = $request->contact_name;
		$place->contact_phone = $request->contact_phone;
        $place->contact_email = $request->contact_email;

        // print templates
        $templates = [
            'pos80' => minifyHtml(view('print.templates.pos80')->render()),
            'pos58' => minifyHtml(view('print.templates.pos58')->render()),
        ];

        $place->print_templates = $templates;

		$place->save();

		return response()->json([
			'message' => 'Cập nhật thông tin cửa hàng thành công!',
			'place' => $place->load(['user']),
			'places' => $user->places,
		]);

	}

	public function updateLogo(PlaceRequest $request) {
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
			'place' => $place->with('user')->first(),
		]);
	}
}
