<?php

namespace Sl3w\Watermark\Orm;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\IntegerField;

class WatermarkedImagesTable extends DataManager
{
    public static function getTableName()
    {
        return 'sl3w_watermarked_images';
    }

    public static function getMap()
    {
        return [
            new IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
            ]),
            new IntegerField('IMAGE_ID', [
                'required' => true,
            ]),
        ];
    }
}