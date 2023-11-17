<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

if ($_POST['start_watermarking'] == 'Y') {
    set_time_limit(0);

    Loader::includeModule('sl3w.watermark');

    \Sl3w\Watermark\Watermark::addAllWatermarks();

    LocalRedirect('/bitrix/admin/?SL3W_WATERMARK_GADGET_AFTER=Y');
}

if ($_GET['SL3W_WATERMARK_GADGET_AFTER'] == 'Y'): ?>

    <span><?= Loc::getMessage('SL3W_WATERMARK_BUTTON_START_WATERMARKING_AFTER') ?></span>

<?php else: ?>

    <form method="POST">
        <input class="adm-btn-save" type="submit" name="" value="<?= Loc::getMessage('SL3W_WATERMARK_BUTTON_START_WATERMARKING') ?>">

        <input type="hidden" name="start_watermarking" value="Y">
    </form>

    <span style="margin: 15px 0; display: block; cursor: default;"><?= Loc::getMessage('SL3W_WATERMARK_BUTTON_START_WATERMARKING_SPAN') ?></span>

    <a href="/bitrix/admin/settings.php?lang=ru&mid=sl3w.watermark" target="_blank">
        <input type="button" name="" value="<?= Loc::getMessage('SL3W_WATERMARK_BUTTON_START_WATERMARKING_MODULE_LINK') ?>">
    </a>

    <span style="margin: 15px 0; display: block; cursor: default;"><?= Loc::getMessage('SL3W_WATERMARK_BUTTON_START_WATERMARKING_PS') ?></span>

<?php endif; ?>