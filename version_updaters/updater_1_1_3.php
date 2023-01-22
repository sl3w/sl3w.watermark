<?php
use Bitrix\Main\Application;
use Bitrix\Main\Entity\Base;

/** @var CUpdater $updater */
$updater;

$moduleId = 'sl3w.watermark';

if (IsModuleInstalled($moduleId)) {

    if (is_dir(dirname(__FILE__) . '/install/gadgets')) {
        $updater->CopyFiles('install/gadgets', 'gadgets');
    }
}