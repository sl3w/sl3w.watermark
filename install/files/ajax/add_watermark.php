<?php require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Loader;
use Sl3w\Watermark\Watermark;

Loader::includeModule('sl3w.watermark');

$iblockId = (int)sl3w_request()->get('iblock_id');

$watermarked = false;

if ($elementId = (int)sl3w_request()->get('element_id')) {
    Watermark::startCheckProcessing($elementId, $iblockId, 'update');
    $watermarked = true;
}

if ($sectionId = (int)sl3w_request()->get('section_id')) {
    Watermark::startCheckProcessingSection($sectionId, $iblockId);
    $watermarked = true;
}

echo json_encode(['watermarked' => $watermarked]);