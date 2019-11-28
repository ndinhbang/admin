<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
        // Kiểm tra thằng này có hợp đồng trong gói nào? Đã tạo bao nhiêu cửa hàng rồi?
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->routeIs('place.my') || $this->routeIs('admin.place.store')) {
            return [
                'title'   => 'required',
                'code'    => 'required|unique:places',
                'address' => 'required',
            ];
        }

        if ($this->routeIs('place.update') || $this->routeIs('admin.place.update')) {
            return [
                'title'   => 'required',
                'code'    => 'required|unique:places,code,'.$this->uuid.',uuid',
                'address' => 'required',
            ];
        }

        if ($this->routeIs('place.printers')) {
            return [
                'printers'   => 'required|array|min:1',
                'printers.*.name'   => 'required|string|min:1',
                'printers.*.enable'   => 'required|boolean',
            ];
        }

        if ($this->routeIs('place.update-logo')) {
            return [
                'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:1024',
            ];
        }

        return [];
    }
}
