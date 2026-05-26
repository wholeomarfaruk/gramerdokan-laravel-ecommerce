<?php

if (!function_exists('getSectionValue')) {
    function getSectionValue($data, string $key, $default = '')
    {
        $keys = explode('->', $key);

        // foreach ($keys as $k) {
        //     if (is_object($data) && isset($data->$k)) {
        //         $data = $data->$k;
        //     } else {
        //         return $default;
        //     }
        // }
        //seo->meta_title
        $data = data_get($data, $key, $default);
        return $data;
    }
}

if (!function_exists('keyNotation')) {
    function keyNotation(string $key, $i = null, $default = '')
    {


        $data = str_replace('->', '.', $key);

        return $data;
    }
}
