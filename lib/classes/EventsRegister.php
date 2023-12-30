<?php

namespace Sl3w\Watermark;

use Bitrix\Main\IO\File;

class EventsRegister
{
    /**
     * @param bool $reg 'true' if register, 'false' if unregister
     * @return void
     */
    public static function addWatermarkBtnEvents($reg)
    {
        if ($reg) {
            self::registerAddWatermarkBtnEvents();
        } else {
            self::unRegisterAddWatermarkBtnEvents();
        }
    }

    private static function registerAddWatermarkBtnEvents()
    {
        sl3w_event_manager()->registerEventHandler(
            'main',
            'OnAdminContextMenuShow',
            Settings::MODULE_ID,
            'Sl3w\Watermark\AdminEvents',
            'IBlocksAddWatermarkButtonHandler'
        );

        self::registerAppendScriptsToPage();

        self::copyAjaxFile();
    }

    private static function unRegisterAddWatermarkBtnEvents()
    {
        sl3w_event_manager()->unRegisterEventHandler(
            'main',
            'OnAdminContextMenuShow',
            Settings::MODULE_ID,
            'Sl3w\Watermark\AdminEvents',
            'IBlocksAddWatermarkButtonHandler'
        );

//        self::unRegisterAppendScriptsToPage();
    }

    /**
     * @param bool $reg 'true' if register, 'false' if unregister
     * @return void
     */
    public static function addWatermarkBtnSectionEvents($reg)
    {
        if ($reg) {
            self::registerAddWatermarkBtnSectionEvents();
        } else {
            self::unRegisterAddWatermarkBtnSectionEvents();
        }
    }

    private static function registerAddWatermarkBtnSectionEvents()
    {
        sl3w_event_manager()->registerEventHandler(
            'main',
            'OnAdminContextMenuShow',
            Settings::MODULE_ID,
            'Sl3w\Watermark\AdminEvents',
            'IBlocksAddWatermarkButtonSectionHandler'
        );

        self::registerAppendScriptsToPage();

        self::copyAjaxFile();
    }

    private static function unRegisterAddWatermarkBtnSectionEvents()
    {
        sl3w_event_manager()->unRegisterEventHandler(
            'main',
            'OnAdminContextMenuShow',
            Settings::MODULE_ID,
            'Sl3w\Watermark\AdminEvents',
            'IBlocksAddWatermarkButtonSectionHandler'
        );

//        self::unRegisterAppendScriptsToPage();
    }

    private static function registerAppendScriptsToPage()
    {
        sl3w_event_manager()->registerEventHandler(
            'main',
            'OnBeforeEndBufferContent',
            Settings::MODULE_ID,
            'Sl3w\Watermark\AdminEvents',
            'AppendScriptsToPage'
        );
    }

    private static function unRegisterAppendScriptsToPage()
    {
        sl3w_event_manager()->unRegisterEventHandler(
            'main',
            'OnBeforeEndBufferContent',
            Settings::MODULE_ID,
            'Sl3w\Watermark\AdminEvents',
            'AppendScriptsToPage'
        );
    }

    private static function copyAjaxFile()
    {
        if (!File::isFileExists($_SERVER['DOCUMENT_ROOT'] . '/ajax/' . Settings::MODULE_ID . '/add_watermark.php')) {
            CopyDirFiles(
                __DIR__ . '/../../install/files/ajax',
                $_SERVER['DOCUMENT_ROOT'] . '/ajax/' . Settings::MODULE_ID . '/',
                true,
                true
            );
        }
    }

    /**
     * @param bool $reg 'true' if register, 'false' if unregister
     * @return void
     */
    public static function addWatermarkMassEvents($reg)
    {
        if ($reg) {
            self::registerAddWatermarkMassEvents();
        } else {
            self::unRegisterAddWatermarkMassEvents();
        }
    }

    private static function registerAddWatermarkMassEvents()
    {
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
    }

    private static function unRegisterAddWatermarkMassEvents()
    {
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

    /**
     * @param bool $reg 'true' if register, 'false' if unregister
     * @return void
     */
    public static function elementsUpdate($reg)
    {
        if ($reg) {
            self::registerElementsUpdateEvents();
        } else {
            self::unRegisterElementsUpdateEvents();
        }
    }

    private static function registerElementsUpdateEvents()
    {
        sl3w_event_manager()->registerEventHandler(
            'iblock',
            'OnAfterIBlockElementAdd',
            Settings::MODULE_ID,
            'Sl3w\Watermark\Events',
            'OnAfterIBlockElementAdd'
        );

        sl3w_event_manager()->registerEventHandler(
            'iblock',
            'OnAfterIBlockElementUpdate',
            Settings::MODULE_ID,
            'Sl3w\Watermark\Events',
            'OnAfterIBlockElementUpdate'
        );
    }

    private static function unRegisterElementsUpdateEvents()
    {
        sl3w_event_manager()->unRegisterEventHandler(
            'iblock',
            'OnAfterIBlockElementAdd',
            Settings::MODULE_ID,
            'Sl3w\Watermark\Events',
            'OnAfterIBlockElementAdd'
        );

        sl3w_event_manager()->unRegisterEventHandler(
            'iblock',
            'OnAfterIBlockElementUpdate',
            Settings::MODULE_ID,
            'Sl3w\Watermark\Events',
            'OnAfterIBlockElementUpdate'
        );
    }
}