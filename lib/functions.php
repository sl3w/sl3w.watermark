<?php

use Bitrix\Main\Application;
use Bitrix\Main\EventManager;
use Bitrix\Main\IO\File;
use Bitrix\Main\Page\Asset;
use Sl3w\Watermark\Settings;

if (!function_exists('sl3w_application')) {
    /**
     * @return CMain
     */
    function sl3w_application(): CMain
    {
        global $APPLICATION;

        return $APPLICATION;
    }
}

if (!function_exists('sl3w_request')) {
    /**
     * @return \Bitrix\Main\HttpRequest|\Bitrix\Main\Request
     */
    function sl3w_request()
    {
        return Application::getInstance()->getContext()->getRequest();
    }
}

if (!function_exists('sl3w_event_manager')) {
    /**
     * @return EventManager
     */
    function sl3w_event_manager(): EventManager
    {
        return EventManager::getInstance();
    }
}

if (!function_exists('sl3w_asset')) {
    /**
     * @return Asset
     */
    function sl3w_asset(): Asset
    {
        return Asset::getInstance();
    }
}

if (!function_exists('register_add_watermark_btn_events')) {
    /**
     * @param bool $reg 'true' if register, 'false' if unregister
     * @return void
     */
    function register_add_watermark_btn_events(bool $reg = true)
    {
        if ($reg) {
            sl3w_event_manager()->registerEventHandler(
                'main',
                'OnAdminContextMenuShow',
                Settings::MODULE_ID,
                'Sl3w\Watermark\AdminEvents',
                'IBlocksAddWatermarkButtonHandler'
            );

            sl3w_event_manager()->registerEventHandler(
                'main',
                'OnBeforeEndBufferContent',
                Settings::MODULE_ID,
                'Sl3w\Watermark\AdminEvents',
                'AppendScriptsToPage'
            );

            if (!File::isFileExists($_SERVER['DOCUMENT_ROOT'] . '/ajax/' . Settings::MODULE_ID . '/add_watermark.php')) {
                CopyDirFiles(
                    __DIR__ . '/../install/files/ajax',
                    $_SERVER['DOCUMENT_ROOT'] . '/ajax/' . Settings::MODULE_ID . '/',
                    true,
                    true
                );
            }
        } else {
            sl3w_event_manager()->unRegisterEventHandler(
                'main',
                'OnAdminContextMenuShow',
                Settings::MODULE_ID,
                'Sl3w\Watermark\AdminEvents',
                'IBlocksAddWatermarkButtonHandler'
            );

            sl3w_event_manager()->unRegisterEventHandler(
                'main',
                'OnBeforeEndBufferContent',
                Settings::MODULE_ID,
                'Sl3w\Watermark\AdminEvents',
                'AppendScriptsToPage'
            );
        }
    }
}

if (!function_exists('register_add_watermark_mass_events')) {
    /**
     * @param bool $reg 'true' if register, 'false' if unregister
     * @return void
     */
    function register_add_watermark_mass_events(bool $reg = true)
    {
        if ($reg) {
            sl3w_event_manager()->registerEventHandler(
                'main',
                'OnAdminListDisplay',
                Settings::MODULE_ID,
                'Sl3w\Watermark\AdminEvents',
                'IBlocksListAddWatermarkOptionHandler'
            );

            sl3w_event_manager()->registerEventHandler(
                'main',
                'OnAfterEpilog',
                Settings::MODULE_ID,
                'Sl3w\Watermark\AdminEvents',
                'OnAfterEpilogProcessWatermarks'
            );
        } else {
            sl3w_event_manager()->unRegisterEventHandler(
                'main',
                'OnAdminListDisplay',
                Settings::MODULE_ID,
                'Sl3w\Watermark\AdminEvents',
                'IBlocksListAddWatermarkOptionHandler'
            );

            sl3w_event_manager()->unRegisterEventHandler(
                'main',
                'OnAfterEpilog',
                Settings::MODULE_ID,
                'Sl3w\Watermark\AdminEvents',
                'OnAfterEpilogProcessWatermarks'
            );
        }
    }
}