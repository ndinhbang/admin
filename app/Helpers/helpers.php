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
        if ( app()->offsetExists('_currentPlace') ) {
            return app('_currentPlace');
        }
        if ( getBindVal('_requirePlace') ) {
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
    function uploadImage(\Illuminate\Http\UploadedFile $file = null, $targetPath = 'medias/')
    {
        if ( !is_null($file) ) {
            $extension = $file->getClientOriginalExtension();
            $filename  = uniqid();
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
            $img->fit(500, 320);
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
            'disable_comments' => true,
        ]);
        return $minifier->minify($html);
    }
}
