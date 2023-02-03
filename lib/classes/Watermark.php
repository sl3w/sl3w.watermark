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

    public static function addWaterMarkByPropName($propName, $allElementInfo)
    {
        Helpers::includeModules('iblock');

        $imgIDs = Helpers::arrayWrap($allElementInfo['PROPS'][$propName]['VALUE']);

        foreach ($imgIDs as $imgID) {
            if (WatermarkedImages::isImageWaterMarked($imgID)) {
                continue;
            }

            $imageWithMark = self::getWaterMarkArray($imgID);

            $newFile = CFile::MakeFileArray($imageWithMark['src']);
            $newID = CFile::SaveFile($newFile, 'iblock');
            $newFileArray = CFile::GetFileArray($newID);

            CIBlockElement::SetPropertyValueCode($allElementInfo['FIELDS']['ID'], $propName, $newFileArray);

            CFile::Delete($imgID);

            WatermarkedImages::addWatermarkedImage($newID);
        }
    }

    public static function addWaterMarkByFieldName($fieldName, $allElementInfo)
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

        $imageWithMark = self::getWaterMarkArray($imgID);

        $newFile = CFile::MakeFileArray($imageWithMark['src']);

        $el = new CIBlockElement;

        $el->Update($elementId, [
            $fieldName => $newFile
        ]);

        CFile::Delete($imgID);

        WatermarkedImages::addWatermarkedImage(Iblock::getElementFieldValue($elementId, $fieldName));
    }

    public static function getWaterMarkArray($imgID)
    {
        $arWaterMark = [
            [
                'name' => 'watermark',
                'position' => Settings::get('wm_position'),
                'type' => 'image',
                'size' => 'real',
                'file' => $_SERVER['DOCUMENT_ROOT'] . Settings::get('wm_image_path'),
                'fill' => Settings::yes('wm_is_repeat') ? 'repeat' : 'exact',
                'alpha_level' => (int)Settings::get('wm_alpha') ?: 50
            ]
        ];

        $img = CFile::GetFileArray($imgID);

        return CFile::ResizeImageGet($img, ['width' => $img['WIDTH'], 'height' => $img['HEIGHT']], BX_RESIZE_IMAGE_PROPORTIONAL, true, $arWaterMark);
    }
}