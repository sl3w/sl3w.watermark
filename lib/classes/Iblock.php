<?php

namespace Sl3w\Watermark;

use CIBlockElement;
use Sl3w\Watermark\Helpers as Helpers;

class Iblock
{
    public static function getElementFieldsAndPropsById($elementID, $skipEmptyValue = false, $propsNeedFields = ['ID', 'NAME', 'VALUE', 'VALUE_XML_ID'])
    {
        Helpers::includeModules('iblock');

        $arFilter = ['ID' => $elementID];
        $res = CIBlockElement::GetList([], $arFilter, false, false, []);

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
        Helpers::includeModules('iblock');

        $arFilter = ['ID' => $elementID];
        $resEl = CIBlockElement::GetList([], $arFilter, false, false, [$fieldName])->GetNext();

        return $resEl ? $resEl[$fieldName] : false;
    }
}