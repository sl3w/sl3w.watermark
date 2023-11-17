<?php

namespace Sl3w\Watermark;

use CFile;
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
        if (!self::isImageWaterMarked($imageId)) {
            WatermarkedImagesTable::add(['IMAGE_ID' => $imageId]);

            self::getWatermarkedImagesIds()[] = $imageId;
        }
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
        self::$watermarkedImagesStorage = WatermarkedImagesTable::getList([
            'order' => ['ID' => 'DESC']
        ])->fetchAll();
    }

    public static function getLastWatermarkedImages($count = 10)
    {
        $lastWatermarkedImages = self::getWatermarkedImagesIds();

        $lastWatermarkedImagesRes = [];

        $ii = 0;

        foreach ($lastWatermarkedImages as $lastWatermarkedImage) {
            if ($ii >= $count) break;

            $src = CFile::GetFileArray($lastWatermarkedImage)['SRC'];

            if (!$src) continue;

            $lastWatermarkedImagesRes[] = [
                'ID' => $lastWatermarkedImage,
                'SRC' => $src,
            ];

            $ii++;
        }

        return $lastWatermarkedImagesRes;
    }
}