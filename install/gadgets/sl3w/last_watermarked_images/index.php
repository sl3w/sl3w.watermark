<?php

/** @var array $arGadgetParams */

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Sl3w\Watermark\WatermarkedImages;

Loader::includeModule('sl3w.watermark');

if (intval($arGadgetParams['ELEMENT_COUNT']) <= 0) {
    $arGadgetParams['ELEMENT_COUNT'] = 10;
}

if (intval($arGadgetParams['MAX_IMAGE_WIDTH']) <= 0) {
    $arGadgetParams['MAX_IMAGE_WIDTH'] = 100;
}

$lastWaterMarkedImages = WatermarkedImages::getLastWatermarkedImages($arGadgetParams['ELEMENT_COUNT']);
?>

<div class="sl3w_wm_gadget_last_wm_images">
    <?php if (empty($lastWaterMarkedImages)): ?>

        <span><?= Loc::getMessage('SL3W_WATERMARK_LAST_WATERMARKED_IMAGES_EMPTY') ?></span>

    <?php else: ?>
        <table>
            <tr>
                <th>
                    <?= Loc::getMessage('SL3W_WATERMARK_LAST_WATERMARKED_IMAGES_COLUMN_PREVIEW') ?>
                </th>
                <th>
                    <?= Loc::getMessage('SL3W_WATERMARK_LAST_WATERMARKED_IMAGES_COLUMN_ID') ?>
                </th>
            </tr>

            <?php foreach ($lastWaterMarkedImages as $lastWaterMarkedImage) : ?>
                <tr>
                    <td>
                        <a href="<?= $lastWaterMarkedImage['SRC'] ?>" target="_blank">
                            <img src="<?= $lastWaterMarkedImage['SRC'] ?>" style="max-width: <?= $arGadgetParams['MAX_IMAGE_WIDTH'] ?>px">
                        </a>
                    </td>
                    <td>
                        <a style="display: block" href="<?= $lastWaterMarkedImage['SRC'] ?>" target="_blank">
                            <?= $lastWaterMarkedImage['ID'] ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

<style>
    .sl3w_wm_gadget_last_wm_images table {
        border: 1px solid #eee;
        table-layout: fixed;
        width: 100%;
        margin-bottom: 20px;
        border-collapse: collapse;
    }

    .sl3w_wm_gadget_last_wm_images table th {
        font-weight: bold;
        padding: 5px;
        background: #dfdfdf;
        border: 1px solid #dddddd;
    }

    .sl3w_wm_gadget_last_wm_images table td {
        padding: 5px 10px;
        border: 1px solid #eee;
        text-align: center;
    }

    .sl3w_wm_gadget_last_wm_images table tbody tr:nth-child(odd) {
        background: #ffffff;
    }

    .sl3w_wm_gadget_last_wm_images table tbody tr:nth-child(even) {
        background: #f7f7f7;
    }
</style>