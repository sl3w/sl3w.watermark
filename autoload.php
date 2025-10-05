<?php

\Bitrix\Main\Loader::registerAutoLoadClasses(
    'sl3w.watermark',
    [
        'Sl3w\Watermark\Helpers' => 'lib/classes/Helpers.php',
        'Sl3w\Watermark\OptionsDrawer' => 'lib/classes/OptionsDrawer.php',
        'Sl3w\Watermark\Settings' => 'lib/classes/Settings.php',
        'Sl3w\Watermark\Events' => 'lib/classes/Events.php',
        'Sl3w\Watermark\Agents' => 'lib/classes/Agents.php',
        'Sl3w\Watermark\AdminEvents' => 'lib/classes/AdminEvents.php',
        'Sl3w\Watermark\EventsRegister' => 'lib/classes/EventsRegister.php',
        'Sl3w\Watermark\Iblock' => 'lib/classes/Iblock.php',
        'Sl3w\Watermark\Watermark' => 'lib/classes/Watermark.php',
        'Sl3w\Watermark\WatermarkedImages' => 'lib/classes/WatermarkedImages.php',
        'Sl3w\Watermark\OtherModules' => 'lib/classes/OtherModules.php',
        'Sl3w\Watermark\Orm\WatermarkedImagesTable' => 'lib/classes/orm/WatermarkedImagesTable.php',
    ]
);