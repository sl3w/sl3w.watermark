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

        $imgIDs = Helpers::arrayWrap($allElementInfo['PROPS'][$propName]['VALUE']);

        foreach ($imgIDs as $imgId) {
            if (WatermarkedImages::isImageWaterMarked($imgId)) {
                continue;
            }

            $imageWithMark = self::getWatermarkArray($imgId);

            $newFile = CFile::MakeFileArray($imageWithMark['src']);
            $newID = CFile::SaveFile($newFile, 'iblock');
            $newFileArray = CFile::GetFileArray($newID);

            CIBlockElement::SetPropertyValueCode($allElementInfo['FIELDS']['ID'], $propName, $newFileArray);

            CFile::Delete($imgId);

            WatermarkedImages::addWatermarkedImage($newID);
        }
    }

    public static function addWatermarkByFieldName($fieldName, $allElementInfo)
    {
        Helpers::includeModules('iblock');

        $elementId = $allElementInfo['FIELDS']['ID'];
        $imgID = $allElementInfo['FIELDS'][$fieldName];

        if (!$imgID || WatermarkedImages::isImageWaterMarked($imgID)) {
            if (key_exists($elementId, Helpers::getSessionWatermarkElements())) {
                Helpers::sessionDeleteElementId($elementId);
            }

            return;
        }

        $imageWithMark = self::getWatermarkArray($imgID);

        $newFile = CFile::MakeFileArray($imageWithMark['src']);

        $el = new CIBlockElement;

        $el->Update($elementId, [
            $fieldName => $newFile
        ]);

        CFile::Delete($imgID);

        WatermarkedImages::addWatermarkedImage(Iblock::getElementFieldValue($elementId, $fieldName));
    }

    public static function getWatermarkArray($imgId)
    {
        $maxWmSize = Settings::getWmMaxPercent() / 100;
        $wmImage = CFile::GetFileArray(Settings::getWatermark());
        $imgToWm = CFile::GetFileArray($imgId);

        if (($imgToWm['WIDTH'] > 0 && $wmImage['WIDTH'] / $imgToWm['WIDTH'] > $maxWmSize) || ($imgToWm['HEIGHT'] > 0 && $wmImage['HEIGHT'] / $imgToWm['HEIGHT'] > $maxWmSize)) {
            $wmImage = CFile::ResizeImageGet($wmImage, ['width' => $imgToWm['WIDTH'] * $maxWmSize, 'height' => $imgToWm['HEIGHT'] * $maxWmSize]);
        }

        $arWaterMark = [
            [
                'name' => 'watermark',
                'position' => Settings::get('wm_position'),
                'type' => 'image',
                'size' => 'real',
                'file' => $_SERVER['DOCUMENT_ROOT'] . array_change_key_case($wmImage)['src'],
                'fill' => Settings::yes('wm_is_repeat') ? 'repeat' : 'exact',
                'alpha_level' => Settings::getWmAlpha(),
            ]
        ];

        return CFile::ResizeImageGet($imgToWm, ['width' => $imgToWm['WIDTH'], 'height' => $imgToWm['HEIGHT']], BX_RESIZE_IMAGE_PROPORTIONAL, true, $arWaterMark);
    }
}