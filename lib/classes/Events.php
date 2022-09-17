<?php

namespace Sl3w\Watermark;

use Sl3w\Watermark\Iblock as Iblock;
use Sl3w\Watermark\Settings as Settings;
use Sl3w\Watermark\Watermark as Watermark;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Events
{
    public function OnAfterIBlockElementAdd($arFields)
    {
        if (!Settings::yes('event_add_switch_on')) {
            return;
        }

        self::OnAfterIBlockElementAddUpdate($arFields, 'add');
    }

    public function OnAfterIBlockElementUpdate($arFields)
    {
        if (!Settings::yes('event_update_switch_on')) {
            return;
        }

        self::OnAfterIBlockElementAddUpdate($arFields, 'update');
    }

    public static function AddWatermarkByButtonAjax($elementId, $iblockId)
    {
        self::OnAfterIBlockElementAddUpdate(['ID' => $elementId, 'IBLOCK_ID' => $iblockId], 'update');

        return true;
    }

    public static function OnAfterIBlockElementAddUpdate($arFields, $operation)
    {
        if (!Settings::yes('switch_on') || !Settings::get('wm_image_path')) {
            return;
        }

        $elementId = $arFields['ID'];

        if (key_exists($elementId, session_get('watermark_elements'))) {
            session_delete_element_id($elementId);

            return;
        }

        $iblockId = $arFields['IBLOCK_ID'];

        $iblockIds = Settings::getProcessingIBlocks();

        if (!in_array($iblockId, $iblockIds)) {
            return;
        }

        $iblockFieldsAndProps = explode(',', Settings::get('iblock' . $iblockId . '_fields', []));

        $elementInfo = Iblock::getElementFieldsAndPropsById($elementId);

        $isPropDontAddExist = key_exists(SL3W_WATERMARK_DONT_ADD_PROP_NAME, $elementInfo['PROPS']);

        if ($isPropDontAddExist &&
            (to_lower($elementInfo['PROPS'][SL3W_WATERMARK_DONT_ADD_PROP_NAME]['VALUE']) == Loc::getMessage('SL3W_WATERMARK_TEXT_YES') ||
                to_lower($elementInfo['PROPS'][SL3W_WATERMARK_DONT_ADD_PROP_NAME]['VALUE_XML_ID']) == 'yes')) {

            return;
        }

        foreach ($iblockFieldsAndProps as $field) {

            $propPrefix = 'PROPERTY_';

            $isProp = str_contains($field, $propPrefix);

            if ($isProp) {
                $propName = substr($field, strlen($propPrefix));

                Watermark::addWaterMarkByPropName($propName, $elementInfo);
            } else {
                session_add_element_id($elementId);

                Watermark::addWaterMarkByFieldName($field, $elementInfo);
            }
        }

        if ($isPropDontAddExist) {
            if (Settings::yes('set_dont_add_after_' . $operation)) {
                $propYesOptionId = \CIBlockProperty::GetPropertyEnum(SL3W_WATERMARK_DONT_ADD_PROP_NAME, [], ['IBLOCK_ID' => $iblockId, 'XML_ID' => 'yes'])->Fetch()['ID'];

                if ($propYesOptionId) {
                    \CIBlockElement::SetPropertyValueCode($elementId, SL3W_WATERMARK_DONT_ADD_PROP_NAME, $propYesOptionId);
                }
            }
        }
    }

    public function IBlocksAddWatermarkButtonHandler(&$items)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET' &&
            sl3w_application()->GetCurPage() == '/bitrix/admin/iblock_element_edit.php' &&
            in_array(sl3w_request()->get('IBLOCK_ID'), Settings::getProcessingIBlocks()) &&
            sl3w_request()->get('ID') > 0) {
            $items[] = [
                'TEXT' => Loc::getMessage('SL3W_WATERMARK_ADMIN_BUTTON_TEXT'),
                'LINK' => 'javascript:addWatermarkByItemId(' . sl3w_request()->get('ID') . ',' . sl3w_request()->get('IBLOCK_ID') . ')',
                'TITLE' => Loc::getMessage('SL3W_WATERMARK_ADMIN_BUTTON_TEXT'),
                'ICON' => 'sl3w-add-watermark-btn'
            ];
        }
    }

    public function AppendScriptsToPage()
    {
        if (defined('ADMIN_SECTION')) {
            sl3w_asset()->addJs('/bitrix/js/' . Settings::getModuleId() . '/script.min.js');
        }
    }
}