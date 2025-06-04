<?php

namespace Sl3w\Watermark;

use CFile;
use CIBlockElement;
use CIBlockSection;
use CIBlockProperty;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Watermark
{
    public static function startCheckProcessing($elementId, $iblockId, $operation)
    {
        $elementId = (int)$elementId;
        $iblockId = (int)$iblockId;

        if (!$elementId || !$iblockId) {
            return;
        }

        Settings::checkModuleVersionUpdated();

        if (!Settings::yes('switch_on') || (!Settings::yes('switch_on_image') && !Settings::yes('switch_on_text'))) {
            return;
        }

        if (key_exists($elementId, Helpers::getSessionWatermarkElements())) {
            Helpers::sessionDeleteElementId($elementId);

            return;
        }

        if (in_array($elementId, Settings::getExcludedElements())) {
            return;
        }

        if (!in_array($iblockId, Settings::getProcessingIBlocks())) {
            return;
        }

        $isPending = false;

        //check for pending process via 1c exchange user
        if (Settings::yes('switch_on_1c_pending_exec')) {
            $currentUserId = Helpers::getUserId();
            $userIdFor1cExchange = Settings::get('exchange_1c_user_id');

            if ($currentUserId && $userIdFor1cExchange && $currentUserId == $userIdFor1cExchange) {
                $isPending = true;
            }
        }

        if ($isPending) {
            Agents::createAgentForPendingProcess($elementId, $iblockId, $operation);
        } else {
            self::mainProcessToAddWatermark($elementId, $iblockId, $operation);
        }
    }

    public static function mainProcessToAddWatermark($elementId, $iblockId, $operation)
    {
        $iblockFieldsAndProps = Settings::getProcessingFieldsByIBlock($iblockId);

        $elementInfo = Iblock::getElementFieldsAndPropsById($elementId);

        $isPropDontAddExist = key_exists(SL3W_WATERMARK_DONT_ADD_PROP_NAME, $elementInfo['PROPS']);

        if ($isPropDontAddExist &&
            (Helpers::toLower($elementInfo['PROPS'][SL3W_WATERMARK_DONT_ADD_PROP_NAME]['VALUE']) == Loc::getMessage('SL3W_WATERMARK_TEXT_YES') ||
                Helpers::yes(Helpers::toLower($elementInfo['PROPS'][SL3W_WATERMARK_DONT_ADD_PROP_NAME]['VALUE_XML_ID'])))) {

            return;
        }

        foreach ($iblockFieldsAndProps as $field) {
            if (Helpers::isProperty($field)) {
                $propName = substr($field, strlen(Helpers::PROP_PREFIX));

                self::addWatermarkByPropName($propName, $elementInfo);
            } elseif ($elementInfo['FIELDS'][$field]) {
                Helpers::sessionAddElementId($elementId);

                self::addWatermarkByFieldName($field, $elementInfo);
            }
        }

        if ($isPropDontAddExist) {
            if (Settings::yes('set_dont_add_after_' . $operation)) {
                $propYesOptionId = CIBlockProperty::GetPropertyEnum(SL3W_WATERMARK_DONT_ADD_PROP_NAME, [], ['IBLOCK_ID' => $iblockId, 'XML_ID' => 'yes'])->Fetch()['ID'];

                if ($propYesOptionId) {
                    CIBlockElement::SetPropertyValueCode($elementId, SL3W_WATERMARK_DONT_ADD_PROP_NAME, $propYesOptionId);
                }
            }
        }

        if (Settings::yes('process_sku') && ($skuIBlockId = Iblock::getSkuIBlockId($iblockId))) {
            $skuIds = Iblock::getSkuIdsByProductId($elementId);

            foreach ($skuIds as $skuId) {
                self::startCheckProcessing($skuId, $skuIBlockId, 'update');
            }
        }
    }

    public static function addAllWatermarks()
    {
        Helpers::includeModules('iblock');

        $res = CIBlockElement::GetList([], ['IBLOCK_ID' => Settings::getProcessingIBlocks()], false, false, ['ID', 'IBLOCK_ID']);

        while ($el = $res->GetNext()) {
            self::startCheckProcessing($el['ID'], $el['IBLOCK_ID'], 'update');
        }
    }

    public static function startCheckProcessingSection($sectionId, $iblockId)
    {
        if (!Settings::yes('switch_on') || (!Settings::yes('switch_on_image') && !Settings::yes('switch_on_text'))) {
            return;
        }

        if (!in_array($iblockId, Settings::getProcessingIBlocks())) {
            return;
        }

        self::addWatermarkToSectionPicture($sectionId);
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

    private static function checkGetImageToWm($imgId)
    {
        $imgToWm = CFile::GetFileArray($imgId);
        $fileTypes = Settings::getIncludedFileTypes();

        if (empty($fileTypes)) {
            return $imgToWm;
        }

        foreach ($fileTypes as $fileType) {
            if (Helpers::strContains($imgToWm['CONTENT_TYPE'], $fileType)) {
                return $imgToWm;
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

        $imagesToUpdate = [];
        $imagesToOriginal = [];

        foreach ($imgIds as $key => $imgId) {
            if (WatermarkedImages::isImageWaterMarked($imgId)) {
                continue;
            }

            $imgToWm = self::checkGetImageToWm($imgId);

            if ($imgToWm && ($srcWithWm = self::getAddWatermarkedImageSrc($imgToWm))) {
                $newFileWithWm = CFile::MakeFileArray($srcWithWm);

                $imagesToUpdate[$key] = ['VALUE' => $newFileWithWm];

                if ($saveOriginal) {
                    $imagesToOriginal[] = ['VALUE' => CFile::MakeFileArray($imgId)];
                }

                if ($imgIdsValues[$key]) {
                    $imagesToUpdate[$imgIdsValues[$key]] = ['VALUE' => ['del' => 'Y']];
                }
            }
        }

        if ($saveOriginal && !empty($imagesToOriginal)) {
            Iblock::setElementPropertyValue($allElementInfo['FIELDS']['ID'], $saveOriginal, $imagesToOriginal);
        }

        if (!empty($imagesToUpdate)) {
            Iblock::setElementPropertyValue($allElementInfo['FIELDS']['ID'], $propName, $imagesToUpdate);
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

        $imgToWm = self::checkGetImageToWm($imgId);

        if (!$imgToWm) return;

        $newFileWithWm = CFile::MakeFileArray(self::getAddWatermarkedImageSrc($imgToWm));

        if ($saveOriginal = self::isSaveOriginals($allElementInfo['PROPS'])) {
            $imgToOriginal[] = ['VALUE' => CFile::MakeFileArray($imgId)];

            Iblock::setElementPropertyValue($elementId, $saveOriginal, $imgToOriginal);
        }

        Iblock::setElementFieldValue($elementId, $fieldName, $newFileWithWm);

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

        $imgToWm = self::checkGetImageToWm($imgId);

        if (!$imgToWm) return;

        $newFileWithWm = CFile::MakeFileArray(self::getAddWatermarkedImageSrc($imgToWm));

        Iblock::setSectionPicture($sectionId, $newFileWithWm);

        CFile::Delete($imgId);

        WatermarkedImages::addWatermarkedImage(Iblock::getSectionPicture($sectionId));
    }

    private static function getAddWatermarkedImageSrc($imgToWm)
    {
        return self::getWatermarkArray($imgToWm)['src'];
    }

    private static function getWatermarkArray($imgToWm)
    {
        $arWaterMark = [];

        $imgToWm = is_numeric($imgToWm) ? CFile::GetFileArray($imgToWm) : $imgToWm;

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
                'coefficient' => round(Settings::getWmMaxPercentText() / 100 * 7), //by kernel: 1-7 for text
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