<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Todo: need test carefully
        $user  = $this->user();
        $uuids = $this->input('role_uuids', []);
        $count = count($uuids);
        if (!is_null($currentPlace = currentPlace())
            && $user->can(vsprintf('manage.staffs__%s', $currentPlace->uuid))
            && $count == Role::findByUuids($uuids)
                ->where('level', '<', $user->roles()->max('level') ?? 0)
                ->count()) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->routeIs('employee.store')) {
            return [
                'display_name' => 'bail|required',
                'name' => 'bail|required|unique:users',
                'phone' => 'bail|unique:users|min:10|max:11',
                'email' => 'bail|unique:users',
                'password' => 'bail|required',
                'role_uuids' => 'bail|required|array|min:1|max:5'
            ];
        }

        if ($this->routeIs('employee.update')) {
            return [
                'display_name' => 'bail|required',
                'name' => 'bail|required|unique:users,name,'.$this->uuid.',uuid',
                'phone' => 'bail|min:10|max:11|unique:users,phone,'.$this->uuid.',uuid',
                'email' => 'bail|unique:users,email,'.$this->uuid.',uuid',
                'role_uuids' => 'bail|required|array|min:1|max:5'
            ];
        }

        if ($this->routeIs('employee.update-avatar')) {
            return [
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ];
        }
        return [];
    }
}
