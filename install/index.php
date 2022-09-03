<?php

use Sl3w\Watermark\Settings;

use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class sl3w_watermark extends CModule
{
    var $MODULE_ID = 'sl3w.watermark';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $PARTNER_NAME;
    var $PARTNER_URI;
    var $MODULE_DIR;

    public function __construct()
    {
        if (file_exists(__DIR__ . '/version.php')) {

            $arModuleVersion = [];

            include_once(__DIR__ . '/version.php');

            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];

            $this->MODULE_NAME = Loc::getMessage('SL3W_WATERMARK_MODULE_NAME');
            $this->MODULE_DESCRIPTION = Loc::getMessage('SL3W_WATERMARK_MODULE_DESC');

            $this->PARTNER_NAME = Loc::getMessage('SL3W_WATERMARK_PARTNER_NAME');
            $this->PARTNER_URI = Loc::getMessage('SL3W_WATERMARK_PARTNER_URI');

            $this->MODULE_DIR = dirname(__FILE__) . '/../';
        }
    }

    public function DoInstall()
    {
        global $APPLICATION;

        self::IncludeServiceFiles();

        RegisterModule($this->MODULE_ID);

        $this->InstallEvents();
        $this->SetOptions();

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage('SL3W_WATERMARK_INSTALL_TITLE') . ' "' . Loc::getMessage('SL3W_WATERMARK_MODULE_NAME') . '"',
            __DIR__ . '/step.php'
        );
    }

    public function DoUninstall()
    {
        global $APPLICATION;

        self::IncludeServiceFiles();

        $this->UnInstallEvents();
        $this->ClearOptions();
        $this->ClearSession();

        UnRegisterModule($this->MODULE_ID);

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage('SL3W_WATERMARK_UNINSTALL_TITLE') . ' "' . Loc::getMessage('SL3W_WATERMARK_MODULE_NAME') . '"',
            __DIR__ . '/unstep.php'
        );
    }

    public function InstallEvents()
    {

        EventManager::getInstance()->registerEventHandler(
            'iblock',
            'OnAfterIBlockElementAdd',
            $this->MODULE_ID,
            'Sl3w\Watermark\Events',
            'OnAfterIBlockElementAdd'
        );

        EventManager::getInstance()->registerEventHandler(
            'iblock',
            'OnAfterIBlockElementUpdate',
            $this->MODULE_ID,
            'Sl3w\Watermark\Events',
            'OnAfterIBlockElementUpdate'
        );

        return true;
    }

    public function UnInstallEvents()
    {
        EventManager::getInstance()->unRegisterEventHandler(
            'iblock',
            'OnAfterIBlockElementAdd',
            $this->MODULE_ID,
            'Sl3w\Watermark\Events',
            'OnAfterIBlockElementAdd'
        );

        EventManager::getInstance()->unRegisterEventHandler(
            'iblock',
            'OnAfterIBlockElementUpdate',
            $this->MODULE_ID,
            'Sl3w\Watermark\Events',
            'OnAfterIBlockElementUpdate'
        );

        return true;
    }

    private function SetOptions()
    {
        Settings::set('switch_on', 'Y');
    }

    private function ClearOptions()
    {
        Settings::deleteAll();
    }

    private function ClearSession()
    {
        if (key_exists(SL3W_WATERMARK_SESSION_DATA_CONTAINER, $_SESSION)) {
            unset($_SESSION[SL3W_WATERMARK_SESSION_DATA_CONTAINER]);
        }
    }

    private static function IncludeServiceFiles()
    {
        include_once('service.php');
    }
}