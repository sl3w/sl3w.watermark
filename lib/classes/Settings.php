<?php

namespace Sl3w\Watermark;

use Bitrix\Main\Config\Option;

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
}
