<?php

use Bitrix\Main\Localization\Loc;

if (!defined('SL3W_WATERMARK')) {
    define('SL3W_WATERMARK', 'sl3w.watermark');
}

//if (!$GLOBALS['APPLICATION']->GetGroupRight(SL3W_WATERMARK) >= 'R') return;

if (!defined('SL3W_WATERMARK_GLOBAL_MENU_HIDE')) {
    define('SL3W_WATERMARK_GLOBAL_MENU_HIDE', COption::GetOptionString(SL3W_WATERMARK, 'global_menu_hide', 'N'));
}

if (SL3W_WATERMARK_GLOBAL_MENU_HIDE === 'Y') return;

if (!defined('SL3W_WATERMARK_GLOBAL_MENU_SECTION')) {
    define('SL3W_WATERMARK_GLOBAL_MENU_SECTION', COption::GetOptionString(SL3W_WATERMARK, 'global_menu_section', 'global_menu_sl3w'));
}

if (!defined('SL3W_WATERMARK_GLOBAL_MENU_SECTION_SORT')) {
    define('SL3W_WATERMARK_GLOBAL_MENU_SECTION_SORT', COption::GetOptionString(SL3W_WATERMARK, 'global_menu_section_sort', '500'));
}

if (!defined('SL3W_WATERMARK_GLOBAL_MENU_SELF_SECTION_SORT')) {
    define('SL3W_WATERMARK_GLOBAL_MENU_SELF_SECTION_SORT', COption::GetOptionString(SL3W_WATERMARK, 'global_menu_self_section_sort', '2000'));
}

Loc::loadMessages(__FILE__);

if (!defined('SL3W_WATERMARK_MENU_ITEMS')) {
    define('SL3W_WATERMARK_MENU_ITEMS', [
        'parent_menu' => SL3W_WATERMARK_GLOBAL_MENU_SECTION,
        'section' => 'sl3w_watermark',
        'text' => Loc::getMessage('SL3W_WATERMARK_MENU_MODULE_NAME_LONG'),
        'title' => Loc::getMessage('SL3W_WATERMARK_MENU_MODULE_FULL_NAME'),
        'sort' => SL3W_WATERMARK_GLOBAL_MENU_SECTION_SORT,
        'icon' => 'sl3w_watermark',
        'page_icon' => 'sl3w_watermark',
        'items_id' => 'menu_sl3w_watermark_items',
        'module_id' => SL3W_WATERMARK,
        'items' => [
            [
                'text' => Loc::getMessage('SL3W_WATERMARK_MENU_SETTINGS'),
                'title' => Loc::getMessage('SL3W_WATERMARK_MENU_SETTINGS'),
                'sort' => 10,
                'url' => sprintf('/bitrix/admin/settings.php?mid=%s&lang=%s&mid_menu=1&tabControl_active_tab=%s', SL3W_WATERMARK, urlencode(LANGUAGE_ID), 'settings'),
                'icon' => 'util_menu_icon',
                'page_icon' => 'util_menu_icon',
                'items_id' => 'settings',
                'more_url' => [
                    sprintf('settings.php?mid=%s&lang=%s&mid_menu=1&tabControl_active_tab=%s', SL3W_WATERMARK, urlencode(LANGUAGE_ID), 'settings'),
                ],
            ],
            [
                'text' => Loc::getMessage('SL3W_WATERMARK_MENU_SUPPORT'),
                'title' => Loc::getMessage('SL3W_WATERMARK_MENU_SUPPORT'),
                'sort' => 20,
                'url' => sprintf('/bitrix/admin/settings.php?mid=%s&lang=%s&mid_menu=1&tabControl_active_tab=%s', SL3W_WATERMARK, urlencode(LANGUAGE_ID), 'support'),
                'icon' => 'currency_menu_icon',
                'page_icon' => 'currency_menu_icon',
                'items_id' => 'support',
                'more_url' => [
                    sprintf('/bitrix/admin/settings.php?mid=%s&lang=%s&mid_menu=1&tabControl_active_tab=%s', SL3W_WATERMARK, urlencode(LANGUAGE_ID), 'support'),
                ],
            ],
        ],
    ]);
}

\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'main',
    'OnBuildGlobalMenu',
    function (&$arGlobalMenu, &$arModuleMenu) {
        if (!defined('SL3W_WATERMARK_MENU_INCLUDED') && SL3W_WATERMARK_GLOBAL_MENU_HIDE !== 'Y') {
            define('SL3W_WATERMARK_MENU_INCLUDED', true);

            $GLOBALS['APPLICATION']->SetAdditionalCss('/bitrix/css/' . SL3W_WATERMARK . '/menu.css');

//            $arMenu = SL3W_WATERMARK_MENU_ITEMS;

            if (!isset($arGlobalMenu['global_menu_sl3w'])) {
                $arGlobalMenu['global_menu_sl3w'] = [
                    'menu_id' => 'global_menu_sl3w',
                    'text' => Loc::getMessage('SL3W_WATERMARK_MENU_MODULE_NAME'),
                    'title' => Loc::getMessage('SL3W_WATERMARK_MENU_MODULE_FULL_NAME'),
                    'sort' => SL3W_WATERMARK_GLOBAL_MENU_SELF_SECTION_SORT,
                    'items_id' => 'global_menu_sl3w_watermark_items',
                ];
            } else {
                $arGlobalMenu['global_menu_sl3w'] = [
                    'text' => Loc::getMessage('SL3W_WATERMARK_MENU_SL3W'),
                    'title' => Loc::getMessage('SL3W_WATERMARK_MENU_SL3W'),
                ];
            }

            if (isset($arMenu)) {
                $arGlobalMenu['global_menu_sl3w']['items'][SL3W_WATERMARK] = $arMenu;
            }
        }
    }
);

$aMenu = [];

$aMenu[] = SL3W_WATERMARK_MENU_ITEMS;

return $aMenu;