<?php

namespace Sl3w\Watermark;

class Events
{
    public function OnAfterIBlockElementAdd($arFields)
    {
        self::OnAfterIBlockElementAddUpdate($arFields);
    }

    public function OnAfterIBlockElementUpdate($arFields)
    {
        self::OnAfterIBlockElementAddUpdate($arFields);
    }

    public static function OnAfterIBlockElementAddUpdate($arFields)
    {
        if (\Sl3w\Watermark\Settings::get('switch_on') != 'Y') {
            return;
        }

        $idElement = $arFields['ID'];

        if (key_exists($idElement, session_get('watermark_elements'))) {
            session_delete_element_id($idElement);

            return;
        }

        $iblockId = $arFields['IBLOCK_ID'];

        $iblockIds = explode(',', \Sl3w\Watermark\Settings::get('iblock_ids', []));

        if (!in_array($iblockId, $iblockIds)) {
            return;
        }

        $iblockFieldsAndProps = explode(',', \Sl3w\Watermark\Settings::get('iblock' . $iblockId . '_fields', []));

        $elementInfo = \Sl3w\Watermark\Iblock::getElementFieldsAndPropsById($idElement);

        if (key_exists(SL3W_WATERMARK_DONT_ADD_PROP_NAME, $elementInfo['PROPS']) &&
            ($elementInfo['PROPS'][SL3W_WATERMARK_DONT_ADD_PROP_NAME]['VALUE'] == 'да' ||
                $elementInfo['PROPS'][SL3W_WATERMARK_DONT_ADD_PROP_NAME]['VALUE_XML_ID'] == 'yes')) {

            return;
        }

        foreach ($iblockFieldsAndProps as $field) {

            $isProp = str_contains($field, 'PROPERTY_');

            if ($isProp) {
                $propName = substr($field, 9);

                \Sl3w\Watermark\Watermark::addWaterMarkByPropName($propName, $elementInfo);
            } else {
                session_add_element_id($idElement);

                \Sl3w\Watermark\Watermark::addWaterMarkByFieldName($field, $elementInfo);
            }
        }
    }
}