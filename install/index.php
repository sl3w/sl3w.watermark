<?php

use Bitrix\Main\EventManager;
use Bitrix\Main\Application;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\Localization\Loc;
use Sl3w\Watermark\Settings;

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

            include(__DIR__ . '/version.php');

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

        $this->IncludeServiceFiles();

        RegisterModule($this->MODULE_ID);

        $this->InstallEvents();
        $this->InstallFiles();
        $this->InstallDB();
        $this->SetOptions();

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage('SL3W_WATERMARK_INSTALL_TITLE') . ' "' . Loc::getMessage('SL3W_WATERMARK_MODULE_NAME') . '"',
            __DIR__ . '/step.php'
        );
    }

    public function DoUninstall()
    {
        global $APPLICATION;

        $this->IncludeServiceFiles();

        $this->UnInstallEvents();
        $this->UnInstallFiles();
        $this->UnInstallDB();
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

        register_add_watermark_btn_events(false);

        register_add_watermark_mass_events(false);

        return true;
    }

    public function InstallFiles()
    {
        CopyDirFiles(
            __DIR__ . '/files/ajax',
            $_SERVER['DOCUMENT_ROOT'] . '/ajax/' . $this->MODULE_ID . '/',
            true,
            true
        );

        CopyDirFiles(
            __DIR__ . '/files/assets/js',
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/js/' . $this->MODULE_ID . '/',
            true,
            true
        );

        CopyDirFiles(
            __DIR__ . '/files/gadgets',
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/gadgets/',
            true,
            true
        );

        return false;
    }

    public function UnInstallFiles()
    {
        DeleteDirFilesEx('/bitrix/js/' . $this->MODULE_ID);

        DeleteDirFilesEx('/ajax/' . $this->MODULE_ID);

        DeleteDirFilesEx('/bitrix/gadgets/sl3w/button_start_watermarking');

        return false;
    }

    public function InstallDB()
    {
        if (!Application::getConnection()->isTableExists(Base::getInstance('\Sl3w\Watermark\Orm\WatermarkedImagesTable')->getDBTableName())) {
            Base::getInstance('\Sl3w\Watermark\Orm\WatermarkedImagesTable')->createDBTable();
        }

        return true;
    }

    public function UnInstallDB()
    {
        if (Application::getConnection()->isTableExists(Base::getInstance('\Sl3w\Watermark\Orm\WatermarkedImagesTable')->getDBTableName())) {
            Application::getConnection()->dropTable(Base::getInstance('\Sl3w\Watermark\Orm\WatermarkedImagesTable')->getDBTableName());
        }

        return true;
    }

    private function SetOptions()
    {
        Settings::set('switch_on', 'Y');
        Settings::set('add_watermark_btn_mass_switch_on', 'Y');

        Settings::set('wm_alpha', 50);

        Settings::set('wm_max_percent', 50);
        Settings::set('wm_max_percent_text', 50);

        Settings::set('wm_text', 'TEXT');
        Settings::set('wm_text_color', 'ffffff');
        Settings::set('wm_text_color', '/bitrix/fonts/pt_sans-regular.ttf');

        Settings::set('event_add_switch_on', 'Y');
        Settings::set('event_update_switch_on', 'Y');
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

    private function IncludeServiceFiles()
    {
        include_once('service.php');
    }
}