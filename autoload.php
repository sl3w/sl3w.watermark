<?php

CModule::AddAutoloadClasses(
    'sl3w.watermark',
    [
        'Sl3w\Watermark\Settings' => 'lib/classes/Settings.php',
        'Sl3w\Watermark\Events' => 'lib/classes/Events.php',
        'Sl3w\Watermark\Iblock' => 'lib/classes/Iblock.php',
        'Sl3w\Watermark\Watermark' => 'lib/classes/Watermark.php',
    ]
);