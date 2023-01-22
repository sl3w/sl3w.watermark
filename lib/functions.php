<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\EventManager;
use Bitrix\Main\Page\Asset;
use Sl3w\Watermark\Settings as Settings;

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