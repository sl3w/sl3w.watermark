<?php

namespace Sl3w\Watermark;

use Sl3w\Watermark\Settings as Settings;
use Sl3w\Watermark\Events as Events;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class AdminEvents
{
    private static $adminListItemName = 'add_watermarks';

    public static function IBlocksAddWatermarkButtonHandler(&$items)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET' &&
            sl3w_application()->GetCurPage() == '/bitrix/admin/iblock_element_edit.php' &&
            in_array(sl3w_request()->get('IBLOCK_ID'), Settings::getProcessingIBlocks()) &&
            sl3w_request()->get('ID') > 0) {

            $items[] = [
                'TEXT' => Loc::getMessage('SL3W_WATERMARK_ADMIN_BUTTON_TEXT_CAPITAL'),
                'LINK' => 'javascript:addWatermarkByItemId(' . sl3w_request()->get('ID') . ',' . sl3w_request()->get('IBLOCK_ID') . ')',
                'TITLE' => Loc::getMessage('SL3W_WATERMARK_ADMIN_BUTTON_TEXT_CAPITAL'),
                'ICON' => 'sl3w-add-watermark-btn'
            ];
        }
    }

    public static function AppendScriptsToPage()
    {
        if (defined('ADMIN_SECTION')) {
            sl3w_asset()->addJs('/bitrix/js/' . Settings::getModuleId() . '/script.min.js');
        }
    }

    public static function IBlocksListAddWatermarkOptionHandler(&$list)
    {
        $iblockId = sl3w_request()->get('IBLOCK_ID');

        if ($_SERVER['REQUEST_METHOD'] == 'GET' &&
            sl3w_application()->GetCurPage() == '/bitrix/admin/iblock_list_admin.php' &&
            in_array($iblockId, Settings::getProcessingIBlocks())) {

            $iblockType = sl3w_request()->get('type');

            if ($list->table_id == 'tbl_iblock_list_' . md5($iblockType . '.' . $iblockId)) {

                $list->arActions = array_merge($list->arActions, [
                    self::$adminListItemName => Loc::getMessage('SL3W_WATERMARK_ADMIN_BUTTON_TEXT')
                ]);
            }
        }

        if ($_SERVER['REQUEST_METHOD'] == 'GET' &&
            sl3w_application()->GetCurPage() == '/bitrix/admin/iblock_element_admin.php' &&
            in_array($iblockId, Settings::getProcessingIBlocks())) {

            $iblockType = sl3w_request()->get('type');

            if ($list->table_id == 'tbl_iblock_element_' . md5($iblockType . '.' . $iblockId)) {

                $list->arActions = array_merge($list->arActions, [
                    self::$adminListItemName => Loc::getMessage('SL3W_WATERMARK_ADMIN_BUTTON_TEXT')
                ]);
            }
        }
    }

    public static function OnAfterEpilogProcessWatermarks()
    {
        $request = sl3w_request();

        $action = $request->getPost('action');

        $addWatermarkAction = (is_array($action) && in_array(self::$adminListItemName, $action)) || (!is_array($action) && $action == self::$adminListItemName);

        $iblockId = $request->get('IBLOCK_ID');

        $isProcessingIblock = in_array($iblockId, Settings::getProcessingIBlocks());

        if ($request->isAdminSection() && $isProcessingIblock && $addWatermarkAction) {
            $arID = $request->getPost('ID');

            if (is_array($arID) && !empty($arID)) {
                foreach ($arID as $strID) {
                    $str = $strID;
                    preg_match('/(E|S)(.+)/', $str, $matches);
                    list($strID, $type, $id) = $matches;

                    switch ($type) {
                        case 'E':
                            Events::AddWatermarkByButtonAjax($id, $iblockId);
                            break;

                        case '':
                        case ' ':
                            if (intval($str)) {
                                Events::AddWatermarkByButtonAjax($str, $iblockId);
                            }

                            break;

                        /*case 'S':
                            break;*/
                    }
                }
            }
        }
    }
}