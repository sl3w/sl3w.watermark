<?php

namespace Sl3w\Watermark;

use CIBlock;
use CIBlockElement;

class Iblock
{
    public static function getElementFieldsAndPropsById($elementID, $skipEmptyValue = false, $propsNeedFields = ['ID', 'NAME', 'VALUE', 'VALUE_XML_ID'])
    {
        Helpers::includeModules('iblock');

        $res = CIBlockElement::GetList([], ['ID' => $elementID]);

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

        $resEl = CIBlockElement::GetList([], ['ID' => $elementID], false, false, [$fieldName])->GetNext();

        return $resEl ? $resEl[$fieldName] : false;
    }

    public static function getIBlockById($iBlockId): array
    {
        return CIBlock::GetByID($iBlockId)->GetNext();
    }

    public static function getIBlockNameById($iBlockId)
    {
        return self::getIBlockById($iBlockId)['NAME'] ?? false;
    }
}