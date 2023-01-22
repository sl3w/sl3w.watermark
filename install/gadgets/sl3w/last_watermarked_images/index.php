<?php

use Bitrix\Main\Loader;

Loader::includeModule('sl3w.watermark');

$lastWaterMarkedImages = \Sl3w\Watermark\WatermarkedImages::getLastWatermarkedImages();

if (empty($lastWaterMarkedImages)) { ?>

    <span><?= GetMessage('SL3W_WATERMARK_LAST_WATERMARKED_IMAGES_EMPTY') ?></span>

<?php } else {

    foreach ($lastWaterMarkedImages as $lastWaterMarkedImage) {
        ?>

        <a style="display: block" href="<?= $lastWaterMarkedImage['SRC'] ?>" target="_blank"><?= $lastWaterMarkedImage['SRC'] ?></a>

    <?php }
} ?>