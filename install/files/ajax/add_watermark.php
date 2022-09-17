<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;

Loader::includeModule('sl3w.watermark');

$elementId = (int)sl3w_request()->get('element_id');
$iblockId = (int)sl3w_request()->get('iblock_id');

$result = ['watermarked' => \Sl3w\Watermark\Events::AddWatermarkByButtonAjax($elementId, $iblockId)];

echo json_encode($result);