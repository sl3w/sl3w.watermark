<?php
use Bitrix\Main\Application;
use Bitrix\Main\Entity\Base;

/** @var CUpdater $updater */
$updater;

$moduleId = 'sl3w.watermark';

if (IsModuleInstalled($moduleId)) {

    if (is_dir(dirname(__FILE__) . '/install/files/assets/js')) {
        $updater->CopyFiles('install/files/assets/js', 'js/' . $moduleId . '/');
    }

    if ($updater->CanUpdateDatabase()) {
        include_once(__DIR__ . '/lib/classes/orm/WatermarkedImagesTable.php');

        if (!Application::getConnection()->isTableExists(Base::getInstance('\Sl3w\Watermark\Orm\WatermarkedImagesTable')->getDBTableName())) {
            Base::getInstance('\Sl3w\Watermark\Orm\WatermarkedImagesTable')->createDBTable();
        }
    }
}