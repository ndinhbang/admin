<?php

if (!function_exists('nanoId')) {
    function nanoId()
    {
        $client = new \Hidehalo\Nanoid\Client();
        return $client->generateId($size = 21, $mode = \Hidehalo\Nanoid\Client::MODE_DYNAMIC);
    }
}

if (!function_exists('currentPlace')) {
    /**
     * @return \App\Models\Place|null
     * @throws Exception
     */
    function currentPlace()
    {
        if (app()->offsetExists('_currentPlace')) {
            return app('_currentPlace');
        }
        if (getBindVal('_requirePlace')) {
            throw new \Exception('Place is required');
        }
        return null;
    }
}

if (!function_exists('getBindVal')) {
    /**
     * @param      $key
     * @param null $default
     * @return mixed|null
     */
    function getBindVal($key, $default = null)
    {
        return app()->offsetExists($key) ? app($key) : $default;
    }
}

if (!function_exists('getClassShortName')) {
    /**
     * @param $object
     * @return string
     * @throws ReflectionException
     */
    function getClassShortName($object)
    {
        return (new \ReflectionClass($object))->getShortName();
    }
}

if (!function_exists('uploadImage')) {

    function uploadImage(\Illuminate\Http\UploadedFile $file = null, $targetPath = 'medias/')
    {
        if (!is_null($file)) {
            $extension = $file->getClientOriginalExtension();
            $filename = uniqid();

            $baseName = $filename . "." . $extension;

            $filePath = $targetPath . $baseName;
            $img = \Image::make($file);

            // if ($img->width() > 1024) {
            //     $img->resize(1024, null, function ($constraint) {
            //         $constraint->aspectRatio();
            //     });
            // } elseif ($img->height() > 600) {
            //     $img->resize(null, 600, function ($constraint) {
            //         $constraint->aspectRatio();
            //     });
            // }

            $img->fit(500, 320);
            $img->save($filePath);

            return $baseName;
        }

        return false;
    }
}
