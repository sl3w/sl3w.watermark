<?php

namespace Sl3w\Watermark;

use Bitrix\Main\Loader;

class Helpers
{
    private static $sessionWatermarkElements = 'watermark_elements';

    public static function strContains($haystack, $needle)
    {
        return stripos($haystack, $needle) !== false;
    }

    public static function toUpper($string)
    {
        $function = function_exists('mb_strtoupper') ? 'mb_strtoupper' : 'strtoupper';

        return call_user_func($function, $string);
    }

    public static function toLower($string)
    {
        $function = function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower';

        return call_user_func($function, $string);
    }

    public static function yes($value)
    {
        return $value == 'yes';
    }

    public static function arrayWrap($value)
    {
        return is_array($value) ? $value : [$value];
    }

    public static function arrayTrimExplode($array, $separator = ',')
    {
        return array_map('trim', explode($separator, $array));
    }

    public static function includeModules($modulesName)
    {
        $modulesName = self::arrayWrap($modulesName);

        foreach ($modulesName as $moduleName) {
            self::includeModule($moduleName);
        }
    }

    public static function includeModule($moduleName)
    {
        return Loader::includeModule($moduleName);
    }

    public static function clearColorHex($hex)
    {
        return preg_replace('/[^a-f0-9]/is', '', $hex);
    }

    public static function sessionGet($code)
    {
        return $_SESSION[SL3W_WATERMARK_SESSION_DATA_CONTAINER][$code];
    }

    public static function sessionSet($code, $value)
    {
        if (!array_key_exists(SL3W_WATERMARK_SESSION_DATA_CONTAINER, $_SESSION)) {
            $_SESSION[SL3W_WATERMARK_SESSION_DATA_CONTAINER] = [];
        }

        $_SESSION[SL3W_WATERMARK_SESSION_DATA_CONTAINER][$code] = $value;
    }

    public static function getSessionWatermarkElements()
    {
        return self::sessionGet(self::$sessionWatermarkElements) ?: [];
    }

    public static function sessionAddElementId($elementId)
    {
        $elementIds = self::getSessionWatermarkElements();

        if (!$elementIds) {
            $elementIds = [];
        }

        $elementIds[$elementId] = $elementId;

        self::sessionSet(self::$sessionWatermarkElements, $elementIds);
    }

    public static function sessionDeleteElementId($elementId)
    {
        $elementIds = self::getSessionWatermarkElements();

        if (!$elementIds) {
            $elementIds = [];
        } elseif (key_exists($elementId, $elementIds)) {
            unset($elementIds[$elementId]);
        }

        self::sessionSet(self::$sessionWatermarkElements, $elementIds);
    }
}