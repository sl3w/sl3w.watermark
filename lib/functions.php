<?php
use Bitrix\Main\Loader;

if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle)
    {
        return stripos($haystack, $needle) !== false;
    }
}

if (!function_exists('include_modules')) {
    function include_modules($modulesName)
    {
        if (is_array($modulesName)) {
            foreach ($modulesName as $moduleName) {
                Loader::includeModule($moduleName);
            }
        } else {
            Loader::includeModule($modulesName);
        }
    }
}

if (!function_exists('session_get')) {
    function session_get($code)
    {
        return $_SESSION[SL3W_WATERMARK_SESSION_DATA_CONTAINER][$code];
    }
}

if (!function_exists('session_set')) {
    function session_set($code, $value)
    {
        if (!array_key_exists(SL3W_WATERMARK_SESSION_DATA_CONTAINER, $_SESSION)) {
            $_SESSION[SL3W_WATERMARK_SESSION_DATA_CONTAINER] = [];
        }

        $_SESSION[SL3W_WATERMARK_SESSION_DATA_CONTAINER][$code] = $value;
    }
}

if (!function_exists('session_add_element_id')) {
    function session_add_element_id($elementId)
    {
        $elementIds = session_get('watermark_elements');

        if (!$elementIds) {
            $elementIds = [];
        }

        $elementIds[$elementId] = $elementId;

        session_set('watermark_elements', $elementIds);
    }
}

if (!function_exists('session_delete_element_id')) {
    function session_delete_element_id($elementId)
    {
        $elementIds = session_get('watermark_elements');

        if (!$elementIds) {
            $elementIds = [];
        } elseif (key_exists($elementId, $elementIds)) {
            unset($elementIds[$elementId]);
        }

        session_set('watermark_elements', $elementIds);
    }
}