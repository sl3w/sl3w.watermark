<?php
/** @var CUpdater $updater */
$updater;

$moduleId = 'sl3w.watermark';

if (IsModuleInstalled($moduleId)) {
    if (is_dir(dirname(__FILE__) . '/install/gadgets/last_watermarked_images')) {
        $updater->CopyFiles('install/gadgets/last_watermarked_images', 'gadgets/last_watermarked_images');
    }
}