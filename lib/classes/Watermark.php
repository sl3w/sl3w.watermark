<?php

namespace Sl3w\Watermark;

use CFile;
use CIBlockElement;
use Sl3w\Watermark\Settings as Settings;

class Watermark
{
    public static function addWaterMarkByPropName($propName, $allElementInfo)
    {
        $imgIDs = $allElementInfo['PROPS'][$propName]['VALUE'];

        if (!is_array($imgIDs)) {
            $imgIDs = [$imgIDs];
        }

        foreach ($imgIDs as $imgID) {
            $imageWithMark = self::getWaterMarkArray($imgID);

            $newFile = CFile::MakeFileArray($imageWithMark['src']);
            $newID = CFile::SaveFile($newFile, 'iblock');
            $newFileArray = CFile::GetFileArray($newID);

            CIBlockElement::SetPropertyValueCode($allElementInfo['FIELDS']['ID'], $propName, $newFileArray);

            CFile::Delete($imgID);
        }
    }

    public static function addWaterMarkByFieldName($fieldName, $allElementInfo)
    {
        $imgID = $allElementInfo['FIELDS'][$fieldName];

        $imageWithMark = self::getWaterMarkArray($imgID);

        $newFile = CFile::MakeFileArray($imageWithMark['src']);

        $el = new CIBlockElement;

        $el->Update($allElementInfo['FIELDS']['ID'], [
            $fieldName => $newFile
        ]);

        CFile::Delete($imgID);
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