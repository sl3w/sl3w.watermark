<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Sl3w\Watermark\WatermarkedImages;

Loader::includeModule('sl3w.watermark');

$lastWaterMarkedImages = WatermarkedImages::getLastWatermarkedImages();

if (empty($lastWaterMarkedImages)) { ?>

    <span><?= Loc::getMessage('SL3W_WATERMARK_LAST_WATERMARKED_IMAGES_EMPTY') ?></span>

<?php } else {

    foreach ($lastWaterMarkedImages as $lastWaterMarkedImage) {
        ?>

        <a style="display: block" href="<?= $lastWaterMarkedImage['SRC'] ?>" target="_blank"><?= $lastWaterMarkedImage['SRC'] ?></a>

    <?php }
} ?>