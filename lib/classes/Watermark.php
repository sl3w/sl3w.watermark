<?php

namespace Sl3w\Watermark;

use CFile;
use CIBlockElement;
use Sl3w\Watermark\Settings as Settings;
use Sl3w\Watermark\WatermarkedImages as WatermarkedImages;
use Sl3w\Watermark\Iblock as Iblock;

class Watermark
{
    public static function addWaterMarkByPropName($propName, $allElementInfo)
    {
        $imgIDs = array_wrap($allElementInfo['PROPS'][$propName]['VALUE']);

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
        $elementId = $allElementInfo['FIELDS']['ID'];
        $imgID = $allElementInfo['FIELDS'][$fieldName];

        if (!$imgID || WatermarkedImages::isImageWaterMarked($imgID)) {
            if (key_exists($elementId, session_watermark_elements())) {
                session_delete_element_id($elementId);
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
                'fill' => Settings::get('wm_is_repeat') == 'Y' ? 'repeat' : 'exact',
                'alpha_level' => (int)Settings::get('wm_alpha') ?: 50
            ]
        ];

        $img = CFile::GetFileArray($imgID);

        list($width, $height, $type, $attr) = getimagesize($img);

        return CFile::ResizeImageGet($img, array('width' => $width, 'height' => $height), BX_RESIZE_PROPORTIONAL, true, $arWaterMark);
    }
}