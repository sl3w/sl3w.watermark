<?php
/** @var CUpdater $updater */
$updater;

$moduleId = 'sl3w.watermark';

if (IsModuleInstalled($moduleId)) {
    if (is_dir(dirname(__FILE__) . '/install/files/ajax')) {
        $updater->CopyFiles('install/files/ajax', '/ajax/' . $moduleId . '/');
    }
}