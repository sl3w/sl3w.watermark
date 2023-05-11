<?php

namespace Sl3w\Watermark;

use CFile;
use CIBlockElement;

class Watermark
{
    public static function addAllWatermarks()
    {
        Helpers::includeModules('iblock');

        $res = CIBlockElement::GetList([], ['IBLOCK_ID' => Settings::getProcessingIBlocks()], false, false, ['ID', 'IBLOCK_ID']);

        while ($el = $res->GetNext()) {
            Events::AddWatermarkByButtonAjax($el['ID'], $el['IBLOCK_ID']);
        }
    }

    public static function addWatermarkByPropName($propName, $allElementInfo)
    {
        Helpers::includeModules('iblock');

        $imgIdsValues = Helpers::arrayWrap($allElementInfo['PROPS'][$propName]['PROPERTY_VALUE_ID']);
        $imgIds = Helpers::arrayWrap($allElementInfo['PROPS'][$propName]['VALUE']);

        $imgsToUpdate = [];

        foreach ($imgIds as $key => $imgId) {
            if (WatermarkedImages::isImageWaterMarked($imgId)) {
                continue;
            }

            if ($src = self::getAddWatermarkedImageSrc($imgId)) {
                $newFile = CFile::MakeFileArray($src);

//            $newId = CFile::SaveFile($newFile, 'iblock');
//            $newFileArray = CFile::GetFileArray($newId);

                $imgsToUpdate[$key] = ['VALUE' => $newFile];
            }

            if ($imgIdsValues[$key]) {
                $imgsToUpdate[$imgIdsValues[$key]] = [
                    'VALUE' => [
                        'del' => 'Y',
                    ]
                ];
            }

//            CFile::Delete($imgId);
        }

        if (!empty($imgsToUpdate)) {
            Iblock::setElementPropertyValue($allElementInfo['FIELDS']['ID'], $propName, $imgsToUpdate);
        }

        $elementInfoAfterUpdate = Iblock::getElementFieldsAndPropsById($allElementInfo['FIELDS']['ID']);
        $imgIdsAfterWm = Helpers::arrayWrap($elementInfoAfterUpdate['PROPS'][$propName]['VALUE']);

        foreach ($imgIdsAfterWm as $imgId) {
            WatermarkedImages::addWatermarkedImage($imgId);
        }
    }

    public static function addWatermarkByFieldName($fieldName, $allElementInfo)
    {
        Helpers::includeModules('iblock');

        $elementId = $allElementInfo['FIELDS']['ID'];
        $imgId = $allElementInfo['FIELDS'][$fieldName];

        if (!$imgId || WatermarkedImages::isImageWaterMarked($imgId)) {
            if (key_exists($elementId, Helpers::getSessionWatermarkElements())) {
                Helpers::sessionDeleteElementId($elementId);
            }

            return;
        }

        $newFile = CFile::MakeFileArray(self::getAddWatermarkedImageSrc($imgId));

        Iblock::setElementFieldValue($elementId, $fieldName, $newFile);

        CFile::Delete($imgId);

        WatermarkedImages::addWatermarkedImage(Iblock::getElementFieldValue($elementId, $fieldName));
    }

    private static function getAddWatermarkedImageSrc($imgId)
    {
        return self::getWatermarkArray($imgId)['src'];
    }

    private static function getWatermarkArray($imgId)
    {
        $arWaterMark = [];

        $imgToWm = CFile::GetFileArray($imgId);

        if (Settings::yes('switch_on_image') && ($wm = Settings::getWatermark())) {
            $maxWmSize = Settings::getWmMaxPercentImage() / 100;
            $wmImage = CFile::GetFileArray($wm);

            if (($imgToWm['WIDTH'] > 0 && $wmImage['WIDTH'] / $imgToWm['WIDTH'] > $maxWmSize) || ($imgToWm['HEIGHT'] > 0 && $wmImage['HEIGHT'] / $imgToWm['HEIGHT'] > $maxWmSize)) {
                $wmImage = CFile::ResizeImageGet($wmImage, ['width' => $imgToWm['WIDTH'] * $maxWmSize, 'height' => $imgToWm['HEIGHT'] * $maxWmSize]);
            }

            $arWaterMark[] = [
                'name' => 'watermark',
                'position' => Settings::getWmPositionImage(),
                'type' => 'image',
                'size' => 'real',
                'file' => $_SERVER['DOCUMENT_ROOT'] . array_change_key_case($wmImage)['src'],
                'fill' => Settings::yes('wm_is_repeat') ? 'repeat' : 'exact',
                'alpha_level' => Settings::getWmAlpha(),
            ];
        }

        if (Settings::yes('switch_on_text') && ($wmText = Settings::getWmText())) {
            $arWaterMark[] = [
                'name' => 'watermark',
                'position' => Settings::getWmPositionText(),
                'type' => 'text',
                'coefficient' => round(Settings::getWmMaxPercentText() / 100 * 7), //по коду ядра: 1-7 для текста
                'fill' => 'resize',
                'text' => $wmText,
                'color' => Settings::getWmTextColor(),
                'font' => Settings::getWmTextFont(),
                'use_copyright' => 'N',
            ];
        }

        return CFile::ResizeImageGet($imgToWm, ['width' => $imgToWm['WIDTH'], 'height' => $imgToWm['HEIGHT']], BX_RESIZE_IMAGE_PROPORTIONAL, true, $arWaterMark);
    }
}