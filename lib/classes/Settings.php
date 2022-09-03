<?php

namespace Sl3w\Watermark;

use Bitrix\Main\Config\Option;

class Settings
{
    public static function get($name, $default = '')
    {
        return Option::get('sl3w.watermark', $name, $default);
    }

    public static function set($name, $value)
    {
        Option::set('sl3w.watermark', $name, $value);
    }

    public static function deleteAll()
    {
        Option::delete('sl3w.watermark');
    }
}
