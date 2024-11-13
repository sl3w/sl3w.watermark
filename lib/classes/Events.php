<?php

namespace Sl3w\Watermark;

class Events
{
    public static function OnAfterIBlockElementAdd($arFields)
    {
        if (!Settings::yes('event_add_switch_on')) {
            return;
        }

        Watermark::startCheckProcessing($arFields['ID'], $arFields['IBLOCK_ID'], 'add');
    }

    public static function OnAfterIBlockElementUpdate($arFields)
    {
        if (!Settings::yes('event_update_switch_on')) {
            return;
        }

        Watermark::startCheckProcessing($arFields['ID'], $arFields['IBLOCK_ID'], 'update');
    }

    /* legacy module's old version */
    public static function IBlocksAddWatermarkButtonHandler(&$items)
    {
        AdminEvents::IBlocksAddWatermarkButtonHandler($items);
    }

    public static function AppendScriptsToPage()
    {
        AdminEvents::AppendScriptsToPage();
    }
    /* /legacy module's old version */
}