<?php

namespace Sl3w\Watermark;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use CAdminNotify;
use CFile;

class Settings
{
    const MODULE_ID = 'sl3w.watermark';

    public static function get($name, $default = ''): string
    {
        return Option::get(self::MODULE_ID, $name, $default);
    }

    public static function set($name, $value)
    {
        Option::set(self::MODULE_ID, $name, $value);
    }

    public static function deleteAll()
    {
        Option::delete(self::MODULE_ID);
    }

    public static function yes($name): bool
    {
        return self::get($name) == 'Y';
    }

    public static function getModuleVersion()
    {
        return ModuleManager::getVersion(self::MODULE_ID);
    }

    public static function checkModuleVersionUpdated()
    {
        $savedVersion = self::get('module_version');
        $realVersion = self::getModuleVersion();

        if (!$savedVersion || $savedVersion != $realVersion) {
            CAdminNotify::Add([
                'MESSAGE' => Loc::getMessage('SL3W_WATERMARK_SUPPORT_NOTIFY_TEXT'),
                'TAG' => 'sl3w_watermark_support_notify',
                'MODULE_ID' => Settings::MODULE_ID,
                'ENABLE_CLOSE' => 'Y',
            ]);

            self::set('module_version', $realVersion);
        }
    }

    public static function getProcessingIBlocks(): array
    {
        $iBlockIds = self::get('iblock_ids');

        return $iBlockIds ? Helpers::arrayTrimExplode($iBlockIds) : [];
    }

    public static function getProcessingFieldsByIBlock($iBlockId): array
    {
        return Helpers::arrayTrimExplode(self::get('iblock' . $iBlockId . '_fields', []));
    }

    public static function getExcludedElements(): array
    {
        $elementsIds = self::get('exclude_elements_ids');

        return $elementsIds ? Helpers::arrayTrimExplode($elementsIds) : [];
    }

    public static function getWatermark()
    {
        return self::get('wm_image_path');
    }

    public static function getWatermarkPath(): ?string
    {
        $wmPath = self::getWatermark();

        if (is_numeric($wmPath)) {
            $wmPath = CFile::GetPath($wmPath);
        }

        return $wmPath;
    }

    public static function getWmPositionImage()
    {
        return self::get('wm_position');
    }

    public static function getWmPositionText()
    {
        return self::get('wm_position_text');
    }

    public static function getWmText()
    {
        return self::get('wm_text');
    }

    public static function getWmTextColor()
    {
        return self::get('wm_text_color');
    }

    public static function getWmTextFont()
    {
        return $_SERVER['DOCUMENT_ROOT'] . self::get('wm_text_font');
    }

    public static function getWmAlpha()
    {
        return self::getCheckPercentValue((int)Settings::get('wm_alpha'));
    }

    public static function getWmMaxPercentImage()
    {
        return self::getCheckPercentValue((int)Settings::get('wm_max_percent'));
    }

    public static function getWmMaxPercentText()
    {
        return self::getCheckPercentValue((int)Settings::get('wm_max_percent_text'));
    }

    private static function getCheckPercentValue($val)
    {
        if (!$val) return 50;

        if ($val < 0) $val = 0;
        if ($val > 100) $val = 100;

        return $val;
    }
}