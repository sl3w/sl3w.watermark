<?php require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Loader;
use Sl3w\Watermark\Events;

Loader::includeModule('sl3w.watermark');

$iblockId = (int)sl3w_request()->get('iblock_id');

$result = ['watermarked' => false];

if ($elementId = (int)sl3w_request()->get('element_id')) {
    $result = ['watermarked' => Events::AddWatermarkByButtonAjax($elementId, $iblockId)];
}

if ($sectionId = (int)sl3w_request()->get('section_id')) {
    $result = ['watermarked' => Events::AddWatermarkToSectionByButtonAjax($sectionId, $iblockId)];
}

echo json_encode($result);