<?php

namespace Sl3w\Watermark;

use CIBlockElement;

class Iblock
{
    public static function getElementFieldsAndPropsById($elementID, $skipEmptyValue = false, $propsNeedFields = ['ID', 'NAME', 'VALUE', 'VALUE_XML_ID'])
    {
        include_modules('iblock');

        $arFilter = ['ID' => $elementID];
        $res = CIBlockElement::GetList([], $arFilter, false, [], []);

        $resElement = false;

        if ($arElement = $res->GetNextElement()) {
            $props = [];
            $propertiesOfElement = $arElement->GetProperties();

            foreach ($propertiesOfElement as $propName => $propertyFields) {
                if ($skipEmptyValue && empty($propertyFields['VALUE'])) {
                    continue;
                }

                foreach ($propsNeedFields as $propNeedNameField) {
                    $props[$propName][$propNeedNameField] = $propertyFields[$propNeedNameField];
                }
            }

            $resElement = ['FIELDS' => $arElement->GetFields(), 'PROPS' => $props];
        }

        return $resElement;
    }

    public static function getElementFieldValue($elementID, $fieldName)
    {
        include_modules('iblock');

        $arFilter = ['ID' => $elementID];
        $resEl = CIBlockElement::GetList([], $arFilter, false, false, [$fieldName])->GetNext();

        if ($resEl) {
            return $resEl[$fieldName];
        }

        return false;
    }
}