<?php

namespace Sl3w\Watermark;

use CIBlockElement;
use CIBlockProperty;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Events
{
    public static function OnAfterIBlockElementAdd($arFields)
    {
        if (!Settings::yes('event_add_switch_on')) {
            return;
        }

        self::OnAfterIBlockElementAddUpdate($arFields, 'add');
    }

    public static function OnAfterIBlockElementUpdate($arFields)
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

    public static function AddWatermarkToSectionByButtonAjax($sectionId, $iblockId)
    {
        if (!Settings::yes('switch_on') || (!Settings::yes('switch_on_image') && !Settings::yes('switch_on_text'))) {
            return false;
        }

        if (!in_array($iblockId, Settings::getProcessingIBlocks())) {
            return false;
        }

        Watermark::addWatermarkToSectionPicture($sectionId);

        return true;
    }

    public static function OnAfterIBlockElementAddUpdate($arFields, $operation)
    {
        Settings::checkModuleVersionUpdated();

        if (!Settings::yes('switch_on') || (!Settings::yes('switch_on_image') && !Settings::yes('switch_on_text'))) {
            return;
        }

        $elementId = $arFields['ID'];

        if (key_exists($elementId, Helpers::getSessionWatermarkElements())) {
            Helpers::sessionDeleteElementId($elementId);

            return;
        }

        if (in_array($elementId, Settings::getExcludedElements())) {
            return;
        }

        $iblockId = $arFields['IBLOCK_ID'];

        if (!in_array($iblockId, Settings::getProcessingIBlocks())) {
            return;
        }

        $isPending = false;

        //check for pending process via 1c exchange user
        if (Settings::yes('switch_on_1c_pending_exec')) {
            $currentUserId = Helpers::getUserId();
            $userIdFor1cExchange = Settings::get('exchange_1c_user_id');

            if ($currentUserId && $userIdFor1cExchange && $currentUserId == $userIdFor1cExchange) {
                $isPending = true;
            }
        }

        if ($isPending) {
            Agents::createAgentForPendingProcess($elementId, $iblockId, $operation);
        } else {
            self::mainProcessToAddWatermark($elementId, $iblockId, $operation);
        }
    }

    public static function mainProcessToAddWatermark($elementId, $iblockId, $operation)
    {
        $iblockFieldsAndProps = Settings::getProcessingFieldsByIBlock($iblockId);

        $elementInfo = Iblock::getElementFieldsAndPropsById($elementId);

        $isPropDontAddExist = key_exists(SL3W_WATERMARK_DONT_ADD_PROP_NAME, $elementInfo['PROPS']);

        if ($isPropDontAddExist &&
            (Helpers::toLower($elementInfo['PROPS'][SL3W_WATERMARK_DONT_ADD_PROP_NAME]['VALUE']) == Loc::getMessage('SL3W_WATERMARK_TEXT_YES') ||
                Helpers::yes(Helpers::toLower($elementInfo['PROPS'][SL3W_WATERMARK_DONT_ADD_PROP_NAME]['VALUE_XML_ID'])))) {

            return;
        }

        foreach ($iblockFieldsAndProps as $field) {
            if (Helpers::isProperty($field)) {
                $propName = substr($field, strlen(Helpers::PROP_PREFIX));

                Watermark::addWatermarkByPropName($propName, $elementInfo);
            } elseif ($elementInfo['FIELDS'][$field]) {
                Helpers::sessionAddElementId($elementId);

                Watermark::addWatermarkByFieldName($field, $elementInfo);
            }
        }

        if ($isPropDontAddExist) {
            if (Settings::yes('set_dont_add_after_' . $operation)) {
                $propYesOptionId = CIBlockProperty::GetPropertyEnum(SL3W_WATERMARK_DONT_ADD_PROP_NAME, [], ['IBLOCK_ID' => $iblockId, 'XML_ID' => 'yes'])->Fetch()['ID'];

                if ($propYesOptionId) {
                    CIBlockElement::SetPropertyValueCode($elementId, SL3W_WATERMARK_DONT_ADD_PROP_NAME, $propYesOptionId);
                }
            }
        }

        if (Settings::yes('process_sku') && ($skuIBlockId = Iblock::getSkuIBlockId($iblockId))) {
            $skuIds = Iblock::getSkuIdsByProductId($elementId);

            foreach ($skuIds as $skuId) {
                self::AddWatermarkByButtonAjax($skuId, $skuIBlockId);
            }
        }
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