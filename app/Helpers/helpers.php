<?php
if ( !function_exists('nanoId') ) {
    function nanoId()
    {
        $client = new \Hidehalo\Nanoid\Client();
        return $client->generateId($size = 21, $mode = \Hidehalo\Nanoid\Client::MODE_DYNAMIC);
    }
}
if ( !function_exists('currentPlace') ) {
    /**
     * @return \App\Models\Place|null
     * @throws Exception
     */
    function currentPlace()
    {
        if ( app()->offsetExists('__currentPlace') ) {
            return app('__currentPlace');
        }
        if ( getBindVal('__requirePlace') ) {
            throw new \Exception('Place is required');
        }
        return null;
    }
}
if ( !function_exists('getBindVal') ) {
    /**
     * @param        $key
     * @param  null  $default
     * @return mixed|null
     */
    function getBindVal($key, $default = null)
    {
        return app()->offsetExists($key) ? app($key) : $default;
    }
}
if ( !function_exists('getClassShortName') ) {
    /**
     * @param $object
     * @return string
     * @throws ReflectionException
     */
    function getClassShortName($object)
    {
        return ( new \ReflectionClass($object) )->getShortName();
    }
}
if ( !function_exists('uploadImage') ) {
    /**
     * @param  \Illuminate\Http\UploadedFile|null  $file
     * @param  string                              $targetPath
     * @param  bool                                $keepOriginalSize
     * @return bool|string
     */
    function uploadImage(\Illuminate\Http\UploadedFile $file = null, $targetPath = 'medias/', $keepOriginalSize = false)
    {
        if ( !is_null($file) ) {
            $extension = $file->getClientOriginalExtension();
            // create folder if not exists
            $path = public_path(trim($targetPath, '/'));
            if ( !File::isDirectory($path) ) {
                File::makeDirectory($path, 0755, true, true);
            }
            $filename = uniqid();
            $baseName = $filename . "." . $extension;
            $filePath = $targetPath . $baseName;
            $img      = \Image::make($file);
            // if ($img->width() > 1024) {
            //     $img->resize(1024, null, function ($constraint) {
            //         $constraint->aspectRatio();
            //     });
            // } elseif ($img->height() > 600) {
            //     $img->resize(null, 600, function ($constraint) {
            //         $constraint->aspectRatio();
            //     });
            // }
            if ( !$keepOriginalSize ) {
                $img->fit(500, 320);
            }
            $img->save($filePath);
            return $baseName;
        }
        return false;
    }
}
if ( !function_exists('currentState') ) {
    /**
     * @param  integer  $position
     * @return array
     */
    function currentState($position)
    {
        $stateArr = config('default.orders.state');
        return $stateArr[ (int) $position ] ?? [];
    }
}
if ( !function_exists('nextState') ) {
    /**
     * @param $currentPosition
     * @return array
     */
    function nextState($currentPosition)
    {
        $currentState = currentState($currentPosition);
        $nextPosition = $currentPosition + 1;
        $nextState    = currentState($nextPosition);
        if ( !empty($currentState) && !empty($nextState) ) {
            // neu co thiet lap bep
            $enableKitchen = config('default.pos.enable_kitchen');
            if ( !$enableKitchen && isset($currentState['is_pending']) && $currentState['is_pending'] ) {
                // chuyen tu trang thai 0 -> 2
                return currentState(2);
            }
            // neu co ship do
            $enableShipment = config('default.pos.enable_shipment');
            if ( !$enableShipment && isset($nextState['is_done']) && $nextState['is_done'] ) {
                // chuyen tu tran thai 3,4,5 -> 6
                return currentState(6);
            }
        }
        return $nextState;
    }
}
if ( !function_exists('isOrderClosed') ) {
    /**
     * @param  \App\Models\Order  $order
     * @return bool
     */
    function isOrderClosed(\App\Models\Order $order)
    {
        return $order->is_canceled || $order->is_returned || $order->is_completed;
    }
}
if ( !function_exists('minifyHtml') ) {
    /**
     * @param  string  $html
     * @return string
     */
    function minifyHtml($html)
    {
        $minifier = new \App\Helpers\HtmlMinifier([
            'collapse_whitespace' => true,
            'disable_comments'    => true,
        ]);
        return $minifier->minify($html);
    }
}
if ( !function_exists('mediaUrl') ) {
    /**
     * @param $path
     * @return string
     */
    function mediaUrl($path)
    {
        $replaceArr = [
            '/medias/',
            'medias/',
        ];
        foreach ( $replaceArr as $str ) {
            if ( strpos($path, $str) === 0 ) {
                $path = str_replace($str, '', $path);
            }
        }
        return config('app.media_url') . '/' . trim($path, '/');
    }
}
if ( !function_exists('getOrderKind') ) {
    /**
     * @param    integer    $kindNumber
     * @param    bool       $reverse
     * @return string
     */
    function getOrderKind($kindNumber, $reverse = false)
    {
        $kindArr = [
            0 => 'takeaway',
            1 => 'inplace',
            2 => 'online',
            3 => 'booking',
        ];
        if ($reverse) {
            $kindArr = array_flip($kindArr);
        }
        if (!isset($kindArr[$kindNumber])) {
            throw new \OutOfRangeException('Order kind is out of range');
        }
        return $kindArr[$kindNumber];
    }
}