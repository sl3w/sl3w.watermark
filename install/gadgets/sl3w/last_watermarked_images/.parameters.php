<?php

use Bitrix\Main\Localization\Loc;

$arParameters = [
    'PARAMETERS' => [

    ],
    'USER_PARAMETERS' => [
        'ELEMENT_COUNT' => [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('SL3W_WATERMARK_LAST_WATERMARKED_IMAGES_ELEMENT_COUNT'),
            'TYPE' => 'STRING',
            'DEFAULT' => '10',
        ],
        'MAX_IMAGE_WIDTH' => [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('SL3W_WATERMARK_LAST_WATERMARKED_IMAGES_MAX_IMAGE_WIDTH'),
            'TYPE' => 'STRING',
            'DEFAULT' => '100',
        ],
    ]
];