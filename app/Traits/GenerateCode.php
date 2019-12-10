<?php

namespace App\Traits;


trait GenerateCode
{
    /**
     * Auto generate code based on last inserted code
     *
     * @param string $codePrefix
     * @param integer $padLength
     * @return void
     */
    public function gencode($codePrefix, $padLength = 6)
    {
        $codeId = 0;
        // todo: dont cache this query
        if (!is_null($row = static::select('code')
        ->orderBy('id', 'desc')
        ->take(1)
        ->first())) {
            $codeId = (int) str_replace($codePrefix, '', $row->code);
        }

        return  $codePrefix . str_pad($codeId + 1, $padLength, "0",
        STR_PAD_LEFT);
    }
}