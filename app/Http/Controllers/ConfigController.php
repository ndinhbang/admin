<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfigRequest;
use App\Http\Resources\PlaceResource;
use Illuminate\Support\Facades\File;

class ConfigController extends Controller
{
    protected $thumbnail_path = 'medias/screen2nd/';

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ConfigRequest  $request
     * @return void
     */
    public function configScreen2nd(ConfigRequest $request)
    {
        $place = getBindVal('__currentPlace');
        $image = $request->image ?? '';
        if ( $request->hasFile('imageFile') ) {
            $image = uploadImage($request->file('imageFile'), $this->thumbnail_path, true);
            // remove old image
            $oldImage = $place->config_screen2nd['image'] ?? '';
            if ( !empty($oldImage) ) {
                $oldImagePath = public_path($this->thumbnail_path . $oldImage);
                if ( File::exists($oldImagePath) ) {
                    unlink($oldImagePath);
                }
            }
        }
        // Get only file name before save
        // http://media.goido.local/screen2nd/5df7481e40c0e.png => 5df7481e40c0e.png
        if ( !empty($image) ) {
            $tmpArr = explode('/', $image);
            $image  = end($tmpArr);
        }
        $place->update([
            'config_screen2nd' => [
                'useImage' => (bool) $request->useImage,
                'image'    => $image,
            ],
        ]);
        return response()->json([
            'message'          => 'Lưu cấu hình thành công!',
            'config_screen2nd' => [
                'useImage' => $place->config_screen2nd['useImage'],
                'image'    => mediaUrl($this->thumbnail_path . $place->config_screen2nd['image']),
            ],
        ]);
    }

    public function configSale(ConfigRequest $request)
    {
        $place   = getBindVal('__currentPlace');
        $default = config('default.config.sale');
        $place->update([
            'config_sale' => array_replace_recursive($default, $request->config),
        ]);
        return response()->json([
            'message'     => 'Lưu cấu hình bán hàng thành công!',
            'config_sale' => $place->config_sale,
        ]);
    }

    public function configPrint(ConfigRequest $request)
    {
        $place     = getBindVal('__currentPlace');
        $templates = [
            'pos80kitchen' => minifyHtml(view('print.templates.pos80kitchen')->render()),
            'pos80'        => minifyHtml(view('print.templates.pos80')->render()),
            'pos58'        => minifyHtml(view('print.templates.pos58')->render()),
        ];
        $place->update([
            'print_templates' => $templates,
            'config_print'    => array_replace_recursive(config('default.print.config'), $request->config ?? []),
            'print_info'      => array_replace_recursive(config('default.print.info'), $request->configInfo ?? []),
        ]);
        return response()->json([
            'message' => 'Lưu cấu hình máy in thành công!',
            'place'   => new PlaceResource($place),
        ]);
    }
}
