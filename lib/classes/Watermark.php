<?php

namespace Sl3w\Watermark;

use CFile;
use CIBlockElement;
use CIBlockSection;

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

    /**
     * @param $itemProps
     * @return false|string Property code or false
     */
    private static function isSaveOriginals($itemProps)
    {
        $isSaveOriginalSwitchOn = Settings::isSaveOriginals();
        $propCodeToSaveOriginals = Settings::getPropCodeToSaveOriginals();

        if ($isSaveOriginalSwitchOn && $propCodeToSaveOriginals) {
            if (key_exists($propCodeToSaveOriginals, $itemProps)) {
                return $propCodeToSaveOriginals;
            }
        }

        return false;
    }

    public static function addWatermarkByPropName($propName, $allElementInfo)
    {
        Helpers::includeModules('iblock');

        $saveOriginal = self::isSaveOriginals($allElementInfo['PROPS']);

        if ($propName == $saveOriginal) return;

        $imgIdsValues = Helpers::arrayWrap($allElementInfo['PROPS'][$propName]['PROPERTY_VALUE_ID']);
        $imgIds = Helpers::arrayWrap($allElementInfo['PROPS'][$propName]['VALUE']);

        $imgsToUpdate = [];
        $imgsToOriginal = [];

        foreach ($imgIds as $key => $imgId) {
            if (WatermarkedImages::isImageWaterMarked($imgId)) {
                continue;
            }

            if ($src = self::getAddWatermarkedImageSrc($imgId)) {
                $newFile = CFile::MakeFileArray($src);

                $imgsToUpdate[$key] = ['VALUE' => $newFile];

                if ($saveOriginal) {
                    $imgsToOriginal[] = ['VALUE' => CFile::MakeFileArray($imgId)];
                }
            }

            if ($imgIdsValues[$key]) {
                $imgsToUpdate[$imgIdsValues[$key]] = [
                    'VALUE' => [
                        'del' => 'Y',
                    ]
                ];
            }
        }

        if ($saveOriginal && !empty($imgsToOriginal)) {
            Iblock::setElementPropertyValue($allElementInfo['FIELDS']['ID'], $saveOriginal, $imgsToOriginal);
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

        if ($saveOriginal = self::isSaveOriginals($allElementInfo['PROPS'])) {
            $imgToOriginal[] = ['VALUE' => CFile::MakeFileArray($imgId)];

            Iblock::setElementPropertyValue($elementId, $saveOriginal, $imgToOriginal);
        }

        Iblock::setElementFieldValue($elementId, $fieldName, $newFile);

        CFile::Delete($imgId);

        WatermarkedImages::addWatermarkedImage(Iblock::getElementFieldValue($elementId, $fieldName));
    }

    public static function addWatermarkToSectionPicture($sectionId)
    {
        Helpers::includeModules('iblock');

        $section = CIBlockSection::GetByID($sectionId)->Fetch();

        if (!$section) return;

        $imgId = $section['PICTURE'];

        if (!$imgId || WatermarkedImages::isImageWaterMarked($imgId)) return;

        $newFile = CFile::MakeFileArray(self::getAddWatermarkedImageSrc($imgId));

        Iblock::setSectionPicture($sectionId, $newFile);

        CFile::Delete($imgId);

        WatermarkedImages::addWatermarkedImage(Iblock::getSectionPicture($sectionId));
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