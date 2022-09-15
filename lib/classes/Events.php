<?php

namespace Sl3w\Watermark;

class Events
{
    public function OnAfterIBlockElementAdd($arFields)
    {
        if (\Sl3w\Watermark\Settings::get('event_add_switch_on') != 'Y') {
            return;
        }

        self::OnAfterIBlockElementAddUpdate($arFields, 'add');
    }

    public function OnAfterIBlockElementUpdate($arFields)
    {
        if (\Sl3w\Watermark\Settings::get('event_update_switch_on') != 'Y') {
            return;
        }

        self::OnAfterIBlockElementAddUpdate($arFields, 'update');
    }

    public static function OnAfterIBlockElementAddUpdate($arFields, $operation)
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

        $isPropDontAddExist = key_exists(SL3W_WATERMARK_DONT_ADD_PROP_NAME, $elementInfo['PROPS']);

        if ($isPropDontAddExist &&
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

        if ($isPropDontAddExist) {
            if (\Sl3w\Watermark\Settings::get('set_dont_add_after_' . $operation) == 'Y') {
                $propYesOptionId = \CIBlockProperty::GetPropertyEnum(SL3W_WATERMARK_DONT_ADD_PROP_NAME, [], ['IBLOCK_ID' => $iblockId, 'XML_ID' => 'yes'])->Fetch()['ID'];

                if ($propYesOptionId) {
                    \CIBlockElement::SetPropertyValueCode($idElement, SL3W_WATERMARK_DONT_ADD_PROP_NAME, $propYesOptionId);
                }
            }
        }
    }
}