<?php

namespace Sl3w\Watermark;

use CCatalogSku;
use CIBlock;
use CIBlockElement;
use CIBlockSection;

class Iblock
{
    public static function getElementFieldsAndPropsById($elementID, $skipEmptyValue = false, $propsNeedFields = ['ID', 'NAME', 'VALUE', 'VALUE_XML_ID', 'PROPERTY_VALUE_ID'])
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
                    if (key_exists($propNeedNameField, $propertyFields)) {
                        $props[$propName][$propNeedNameField] = $propertyFields[$propNeedNameField];
                    }
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
        Helpers::includeModules('iblock');

        return CIBlock::GetByID($iBlockId)->GetNext();
    }

    public static function getIBlockNameById($iBlockId)
    {
        Helpers::includeModules('iblock');

        return self::getIBlockById($iBlockId)['NAME'] ?? false;
    }

    public static function setElementPropertyValue($elementId, $propName, $value)
    {
        Helpers::includeModules('iblock');

        CIBlockElement::SetPropertyValueCode($elementId, $propName, $value);
    }

    public static function setElementFieldValue($elementId, $fieldName, $value)
    {
        Helpers::includeModules('iblock');

        (new CIBlockElement)->Update($elementId, [
            $fieldName => $value,
        ]);
    }

    public static function getSkuIBlockId($iBlockId)
    {
        Helpers::includeModules('iblock');

        if (!Helpers::includeModule('catalog')) return false;

        $sku = CCatalogSku::GetInfoByProductIBlock($iBlockId);

        return $sku['IBLOCK_ID'] ?? false;
    }

    public static function getSkuIdsByProductId($productId)
    {
        Helpers::includeModules('iblock');

        $res = CCatalogSKU::getOffersList($productId);

        $result = [];

        foreach ($res[$productId] as $sku) {
            $result[] = $sku['ID'];
        }

        return $result;
    }

    public static function setSectionPicture($sectionId, $pictureValue)
    {
        Helpers::includeModules('iblock');

        (new CIBlockSection)->Update($sectionId, [
            'PICTURE' => $pictureValue,
        ]);
    }

    public static function getSectionPicture($sectionId)
    {
        Helpers::includeModules('iblock');

        $section = CIBlockSection::GetByID($sectionId)->Fetch();

        return $section ? $section['PICTURE'] : false;
    }
}