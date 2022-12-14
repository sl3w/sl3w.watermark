<?php

namespace Sl3w\Watermark;

use Sl3w\Watermark\Orm\WatermarkedImagesTable;

class WatermarkedImages
{
    private static $watermarkedImagesStorage;
    private static $watermarkedImagesIds;

    public static function isImageWaterMarked($imageId)
    {
        return in_array($imageId, self::getWatermarkedImagesIds());
    }

    public static function addWatermarkedImage($imageId)
    {
        WatermarkedImagesTable::add(['IMAGE_ID' => $imageId]);

        self::getWatermarkedImagesIds()[] = $imageId;
    }

    private static function getWatermarkedImagesIds()
    {
        if (!self::$watermarkedImagesIds) {
            self::setWatermarkedImagesIds();
        }

        return self::$watermarkedImagesIds;
    }

    private static function setWatermarkedImagesIds()
    {
        self::$watermarkedImagesIds = array_column(self::getWatermarkedImagesStorage(), 'IMAGE_ID');
    }

    private static function getWatermarkedImagesStorage()
    {
        if (!self::$watermarkedImagesStorage) {
            self::setWatermarkedImagesStorage();
        }

        return self::$watermarkedImagesStorage;
    }

    private static function setWatermarkedImagesStorage()
    {
        self::$watermarkedImagesStorage = WatermarkedImagesTable::getList()->fetchAll();
    }
}