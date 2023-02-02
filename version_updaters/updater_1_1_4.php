<?php
use Bitrix\Main\EventManager;

/** @var CUpdater $updater */
$updater;

$moduleId = 'sl3w.watermark';

if (IsModuleInstalled($moduleId)) {
    //отвязка старых событий
    EventManager::getInstance()->unRegisterEventHandler(
        'main',
        'OnAdminContextMenuShow',
        $moduleId,
        'Sl3w\Watermark\Events',
        'IBlocksAddWatermarkButtonHandler'
    );

    EventManager::getInstance()->unRegisterEventHandler(
        'main',
        'OnBeforeEndBufferContent',
        $moduleId,
        'Sl3w\Watermark\Events',
        'AppendScriptsToPage'
    );
}