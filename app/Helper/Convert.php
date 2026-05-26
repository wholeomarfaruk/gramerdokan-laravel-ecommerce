<?php

namespace App\Helper;

use Carbon\Carbon;
use Illuminate\Support\Facades\Request;

class Convert
{
    public static function normalizeAndHash($value)
    {
        if (empty($value))
            return null;               // skip null/empty
        $normalized = strtolower(trim($value));       // lowercase + trim
        return hash('sha256', $normalized);           // hash with SHA-256
    }
    public static function cleanArray($array)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = self::cleanArray($value);
                if (empty($array[$key])) {
                    unset($array[$key]);
                }
            } elseif (is_null($value)) {
                unset($array[$key]);
            }
        }
        return $array;
    }


}
