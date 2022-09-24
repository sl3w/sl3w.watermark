<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\EventManager;
use Bitrix\Main\Page\Asset;
use Sl3w\Watermark\Settings as Settings;

if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle)
    {
        return stripos($haystack, $needle) !== false;
    }
}

if (!function_exists('to_upper')) {
    function to_upper($string)
    {
        $function = function_exists('mb_strtoupper') ? 'mb_strtoupper' : 'strtoupper';

        return call_user_func($function, $string);
    }
}

if (!function_exists('to_lower')) {
    function to_lower($string)
    {
        $function = function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower';

        return call_user_func($function, $string);
    }
}

if (!function_exists('array_wrap')) {
    function array_wrap($value)
    {
        return is_array($value) ? $value : [$value];
    }
}

if (!function_exists('include_modules')) {
    function include_modules($modulesName)
    {
        $modulesName = array_wrap($modulesName);

        foreach ($modulesName as $moduleName) {
            Loader::includeModule($moduleName);
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

if (!function_exists('session_watermark_elements')) {
    function session_watermark_elements()
    {
        return session_get('watermark_elements');
    }
}

if (!function_exists('session_add_element_id')) {
    function session_add_element_id($elementId)
    {
        $elementIds = session_watermark_elements();

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

if (!function_exists('sl3w_application')) {
    function sl3w_application()
    {
        global $APPLICATION;

        return $APPLICATION;
    }
}

if (!function_exists('sl3w_request')) {
    function sl3w_request()
    {
        return Application::getInstance()->getContext()->getRequest();
    }
}

if (!function_exists('sl3w_event_manager')) {
    function sl3w_event_manager()
    {
        return EventManager::getInstance();
    }
}

if (!function_exists('sl3w_asset')) {
    function sl3w_asset()
    {
        return Asset::getInstance();
    }
}

if (!function_exists('register_add_watermark_btn_events')) {
    function register_add_watermark_btn_events($reg = true)
    {
        if ($reg) {
            sl3w_event_manager()->registerEventHandler(
                'main',
                'OnAdminContextMenuShow',
                Settings::getModuleId(),
                'Sl3w\Watermark\AdminEvents',
                'IBlocksAddWatermarkButtonHandler'
            );

            sl3w_event_manager()->registerEventHandler(
                'main',
                'OnBeforeEndBufferContent',
                Settings::getModuleId(),
                'Sl3w\Watermark\AdminEvents',
                'AppendScriptsToPage'
            );

            if (!\Bitrix\Main\IO\File::isFileExists($_SERVER['DOCUMENT_ROOT'] . '/ajax/' . Settings::getModuleId() . '/add_watermark.php')) {
                CopyDirFiles(
                    __DIR__ . '/../install/files/ajax',
                    $_SERVER['DOCUMENT_ROOT'] . '/ajax/' . Settings::getModuleId() . '/',
                    true,
                    true
                );
            }
        } else {
            sl3w_event_manager()->unRegisterEventHandler(
                'main',
                'OnAdminContextMenuShow',
                Settings::getModuleId(),
                'Sl3w\Watermark\AdminEvents',
                'IBlocksAddWatermarkButtonHandler'
            );

            sl3w_event_manager()->unRegisterEventHandler(
                'main',
                'OnBeforeEndBufferContent',
                Settings::getModuleId(),
                'Sl3w\Watermark\AdminEvents',
                'AppendScriptsToPage'
            );
        }
    }
}

if (!function_exists('register_add_watermark_mass_events')) {
    function register_add_watermark_mass_events($reg = true)
    {
        if ($reg) {
            sl3w_event_manager()->registerEventHandler(
                'main',
                'OnAdminListDisplay',
                Settings::getModuleId(),
                'Sl3w\Watermark\AdminEvents',
                'IBlocksListAddWatermarkOptionHandler'
            );

            sl3w_event_manager()->registerEventHandler(
                'main',
                'OnAfterEpilog',
                Settings::getModuleId(),
                'Sl3w\Watermark\AdminEvents',
                'OnAfterEpilogProcessWatermarks'
            );
        } else {
            sl3w_event_manager()->unRegisterEventHandler(
                'main',
                'OnAdminListDisplay',
                Settings::getModuleId(),
                'Sl3w\Watermark\AdminEvents',
                'IBlocksListAddWatermarkOptionHandler'
            );

            sl3w_event_manager()->unRegisterEventHandler(
                'main',
                'OnAfterEpilog',
                Settings::getModuleId(),
                'Sl3w\Watermark\AdminEvents',
                'OnAfterEpilogProcessWatermarks'
            );
        }
    }
}