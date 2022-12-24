<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

$request = HttpApplication::getInstance()->getContext()->getRequest();

$module_id = htmlspecialcharsbx($request['mid'] != '' ? $request['mid'] : $request['id']);

Loader::includeModule($module_id);

if (!CModule::IncludeModule('iblock')) {
    ShowMessage(GetMessage('IBLOCK_ERROR'));
    return false;
}

$dbIBlocks = CIBlock::GetList(['SORT' => 'ID'], ['ACTIVE' => 'Y']);

$selectIBlocks = [0 => Loc::getMessage('SL3W_WATERMARK_OPTION_EMPTY')];

while ($arIBlock = $dbIBlocks->GetNext()) {
    $selectIBlocks[$arIBlock['ID']] = '[' . $arIBlock['ID'] . '] ' . $arIBlock['NAME'];
}

$selectFields = [
    'PREVIEW_PICTURE' => Loc::getMessage('SL3W_WATERMARK_OPTIONS_PREVIEW_PICTURE'),
    'DETAIL_PICTURE' => Loc::getMessage('SL3W_WATERMARK_OPTIONS_DETAIL_PICTURE'),
];

$selectPositions = [
    'tl' => Loc::getMessage('SL3W_WATERMARK_OPTION_WM_POSITION_TL'),
    'tc' => Loc::getMessage('SL3W_WATERMARK_OPTION_WM_POSITION_TC'),
    'tr' => Loc::getMessage('SL3W_WATERMARK_OPTION_WM_POSITION_TR'),
    'ml' => Loc::getMessage('SL3W_WATERMARK_OPTION_WM_POSITION_ML'),
    'mc' => Loc::getMessage('SL3W_WATERMARK_OPTION_WM_POSITION_MC'),
    'mr' => Loc::getMessage('SL3W_WATERMARK_OPTION_WM_POSITION_MR'),
    'bl' => Loc::getMessage('SL3W_WATERMARK_OPTION_WM_POSITION_BL'),
    'bc' => Loc::getMessage('SL3W_WATERMARK_OPTION_WM_POSITION_BC'),
    'br' => Loc::getMessage('SL3W_WATERMARK_OPTION_WM_POSITION_BR'),
];

$options = [
    Loc::getMessage('SL3W_WATERMARK_BLOCK_COMMON'),
    [
        'switch_on',
        Loc::getMessage('SL3W_WATERMARK_OPTION_SWITCH_ON'),
        'Y',
        ['checkbox']
    ],
    [
        'add_watermark_btn_mass_switch_on',
        Loc::getMessage('SL3W_WATERMARK_OPTION_ADD_WATERMARK_BTN_MASS'),
        'Y',
        ['checkbox']
    ],
    [
        'add_watermark_btn_switch_on',
        Loc::getMessage('SL3W_WATERMARK_OPTION_ADD_WATERMARK_BTN'),
        'N',
        ['checkbox']
    ],
    Loc::getMessage('SL3W_WATERMARK_BLOCK_EVENTS'),
    [
        'event_add_switch_on',
        Loc::getMessage('SL3W_WATERMARK_OPTION_EVENT_ADD_SWITCH_ON'),
        'Y',
        ['checkbox']
    ],
    [
        'event_update_switch_on',
        Loc::getMessage('SL3W_WATERMARK_OPTION_EVENT_UPDATE_SWITCH_ON'),
        'Y',
        ['checkbox']
    ],
    Loc::getMessage('SL3W_WATERMARK_BLOCK_WM'),
    [
        'wm_position',
        Loc::getMessage('SL3W_WATERMARK_OPTION_WM_POSITION'),
        'br',
        ['selectbox', $selectPositions]
    ],
    [
        'wm_is_repeat',
        Loc::getMessage('SL3W_WATERMARK_OPTION_WM_IS_REPEAT'),
        'N',
        ['checkbox']
    ],
    [
        'wm_alpha',
        Loc::getMessage('SL3W_WATERMARK_OPTION_WM_ALPHA'),
        '50',
        ['text', 10],
        '',
        Loc::getMessage('SL3W_WATERMARK_OPTION_WM_ALPHA_NUMBER')
    ],
    [
        'wm_image_path',
        Loc::getMessage('SL3W_WATERMARK_OPTION_WM_IMAGE_PATH'),
        '',
        ['text', 30]
    ],
    Loc::getMessage('SL3W_WATERMARK_SET_DONT_ADD_IBLOCK'),
    [
        'set_dont_add_after_add',
        Loc::getMessage('SL3W_WATERMARK_OPTION_SET_DONT_ADD_AFTER_ADD'),
        '',
        ['checkbox']
    ],
    [
        'set_dont_add_after_update',
        Loc::getMessage('SL3W_WATERMARK_OPTION_SET_DONT_ADD_AFTER_UPDATE'),
        '',
        ['checkbox']
    ],
    ['note' => Loc::getMessage('SL3W_WATERMARK_SET_DONT_ADD_NOTE')],
    Loc::getMessage('SL3W_WATERMARK_BLOCK_IBLOCK'),
    [
        'iblock_ids',
        Loc::getMessage('SL3W_WATERMARK_OPTION_IBLOCK_IDS'),
        '',
        ['multiselectbox', $selectIBlocks]
    ],
    ['note' => Loc::getMessage('SL3W_WATERMARK_SAVE_AFTER_CHANGE_IBLOCK')],
    Loc::getMessage('SL3W_WATERMARK_BLOCK_EXCLUDE'),
    [
        'exclude_elements_ids',
        Loc::getMessage('SL3W_WATERMARK_OPTION_EXCLUDE_ELEMENTS'),
        '',
        ['textarea']
    ],
];

$aTabs = [
    [
        'DIV' => 'edit',
        'TAB' => Loc::getMessage('SL3W_WATERMARK_OPTIONS_TAB_NAME'),
        'TITLE' => Loc::getMessage('SL3W_WATERMARK_OPTIONS_TAB_NAME'),
        'OPTIONS' => $options
    ],
    [
        'DIV' => 'support',
        'TAB' => Loc::getMessage('SL3W_WATERMARK_SUPPORT_TAB_NAME'),
        'TITLE' => Loc::getMessage('SL3W_WATERMARK_SUPPORT_TAB_TITLE'),
    ]
];

$optionsByBlock = [
    'common_block' => Loc::getMessage('SL3W_WATERMARK_BLOCK_COMMON'),
    'common_list' => [
        [
            'switch_on',
            Loc::getMessage('SL3W_WATERMARK_OPTION_SWITCH_ON'),
            'Y',
            ['checkbox']
        ],
        [
            'add_watermark_btn_mass_switch_on',
            Loc::getMessage('SL3W_WATERMARK_OPTION_ADD_WATERMARK_BTN_MASS'),
            'Y',
            ['checkbox']
        ],
        [
            'add_watermark_btn_switch_on',
            Loc::getMessage('SL3W_WATERMARK_OPTION_ADD_WATERMARK_BTN'),
            'N',
            ['checkbox']
        ]
    ],
    'events_block' => Loc::getMessage('SL3W_WATERMARK_BLOCK_EVENTS'),
    'events_list' => [
        [
            'event_add_switch_on',
            Loc::getMessage('SL3W_WATERMARK_OPTION_EVENT_ADD_SWITCH_ON'),
            'Y',
            ['checkbox']
        ],
        [
            'event_update_switch_on',
            Loc::getMessage('SL3W_WATERMARK_OPTION_EVENT_UPDATE_SWITCH_ON'),
            'Y',
            ['checkbox']
        ]
    ],
    'wm_block' => Loc::getMessage('SL3W_WATERMARK_BLOCK_WM'),
    'wm_list' => [
        [
            'wm_position',
            Loc::getMessage('SL3W_WATERMARK_OPTION_WM_POSITION'),
            'br',
            ['selectbox', $selectPositions]
        ],
        [
            'wm_is_repeat',
            Loc::getMessage('SL3W_WATERMARK_OPTION_WM_IS_REPEAT'),
            'N',
            ['checkbox']
        ],
    ],
    'wm_list_special' => [
        [
            'wm_alpha',
            Loc::getMessage('SL3W_WATERMARK_OPTION_WM_ALPHA'),
            '50',
            ['text', 10],
        ],
        [
            'wm_image_path',
            Loc::getMessage('SL3W_WATERMARK_OPTION_WM_IMAGE_PATH'),
            '',
            ['text', 50]
        ]
    ],
    'dont_add_block' => Loc::getMessage('SL3W_WATERMARK_SET_DONT_ADD_IBLOCK'),
    'dont_add_list' => [
        [
            'set_dont_add_after_add',
            Loc::getMessage('SL3W_WATERMARK_OPTION_SET_DONT_ADD_AFTER_ADD'),
            '',
            ['checkbox']
        ],
        [
            'set_dont_add_after_update',
            Loc::getMessage('SL3W_WATERMARK_OPTION_SET_DONT_ADD_AFTER_UPDATE'),
            '',
            ['checkbox']
        ],
    ],
    'dont_add_note' => ['note' => Loc::getMessage('SL3W_WATERMARK_SET_DONT_ADD_NOTE')],
    'iblock_block' => Loc::getMessage('SL3W_WATERMARK_BLOCK_IBLOCK'),
    'iblock_list' => [
        [
            'iblock_ids',
            Loc::getMessage('SL3W_WATERMARK_OPTION_IBLOCK_IDS'),
            '',
            ['multiselectbox', $selectIBlocks]
        ]
    ],
    'iblock_note' => ['note' => Loc::getMessage('SL3W_WATERMARK_SAVE_AFTER_CHANGE_IBLOCK')],
    'iblocks_list' => [],
    'exclude_list' => [
        Loc::getMessage('SL3W_WATERMARK_BLOCK_EXCLUDE'),
        [
            'exclude_elements_ids',
            Loc::getMessage('SL3W_WATERMARK_OPTION_EXCLUDE_ELEMENTS'),
            '',
            ['textarea']
        ],
    ],
];

$iblockIds = explode(',', Option::get($module_id, 'iblock_ids', ''));

foreach ($iblockIds as $iblockId) {
    if (!intval($iblockId)) {
        continue;
    }

    $selectFieldsAndProps = $selectFields;

    $propsRes = CIBlock::GetProperties($iblockId);

    while ($prop = $propsRes->Fetch()) {
        if ($prop['PROPERTY_TYPE'] == 'F') {
            $selectFieldsAndProps['PROPERTY_' . $prop['CODE']] = '[PROPERTY_' . $prop['CODE'] . '] ' . $prop['NAME'];
        }
    }

    $optionsByBlock['iblocks_list'][] = [
        'iblock' . $iblockId . '_fields',
        Loc::getMessage('SL3W_WATERMARK_FIELDS_AND_PROPS') . ' [' . $iblockId . '] "' . CIBlock::GetByID($iblockId)->GetNext()['NAME'] . '":',
        '',
        ['multiselectbox', $selectFieldsAndProps]
    ];
}

$optionsByBlock2 = [
    'support_note' => ['note' => Loc::getMessage('SL3W_WATERMARK_SUPPORT_NOTE')],
];

$tabControl = new CAdminTabControl(
    'tabControl',
    $aTabs
);

$tabControl->Begin();
?>

    <form action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= $module_id ?>&lang=<?= LANG ?>"
          method="post" name="sl3w_watermark">

        <?php

        $tabControl->BeginNextTab();

        if ($optionsByBlock) {
            __AdmSettingsDrawRow($module_id, $optionsByBlock['common_block']);
            __AdmSettingsDrawList($module_id, $optionsByBlock['common_list']);
            __AdmSettingsDrawRow($module_id, $optionsByBlock['events_block']);
            __AdmSettingsDrawList($module_id, $optionsByBlock['events_list']);
            __AdmSettingsDrawRow($module_id, $optionsByBlock['wm_block']);
            __AdmSettingsDrawList($module_id, $optionsByBlock['wm_list']);

            foreach ($optionsByBlock['wm_list_special'] as $wm_list_special_option) {
                ?>
                <tr>
                    <td><?= $wm_list_special_option[1] ?></td>
                    <td>
                        <input type="<?= $wm_list_special_option[3][0] ?: 'text' ?>"
                               name="<?= $wm_list_special_option[0] ?>"
                               size="<?= $wm_list_special_option[3][1] ?: 10 ?>"
                               value="<?= Option::get($module_id, $wm_list_special_option[0], '') ?>"/>

                        <?php switch ($wm_list_special_option[0]) {
                            case 'wm_alpha':
                                echo Loc::getMessage('SL3W_WATERMARK_OPTION_WM_ALPHA_AFTER');

                                break;

                            case 'wm_image_path':
                                ?>
                                <input type="button" value="..." onclick="Sl3wWmOpenFileDialog()">
                                <?php
                                CAdminFileDialog::ShowScript(
                                    [
                                        'event' => "Sl3wWmOpenFileDialog",
                                        'arResultDest' => ['FORM_NAME' => 'sl3w_watermark', 'FORM_ELEMENT_NAME' => 'wm_image_path'],
                                        'arPath' => ['PATH' => ''],
                                        'select' => 'F',
                                        'operation' => 'O',
                                        'showUploadTab' => true,
                                        'showAddToMenuTab' => false,
                                        'fileFilter' => 'image',
                                        'allowAllFiles' => false,
                                        'SaveConfig' => true,
                                    ]
                                );

                                break;
                        } ?>
                    </td>
                </tr>
                <?php
            }

            __AdmSettingsDrawRow($module_id, $optionsByBlock['dont_add_block']);
            __AdmSettingsDrawList($module_id, $optionsByBlock['dont_add_list']);
            __AdmSettingsDrawRow($module_id, $optionsByBlock['dont_add_note']);

            __AdmSettingsDrawRow($module_id, $optionsByBlock['iblock_block']);
            __AdmSettingsDrawList($module_id, $optionsByBlock['iblock_list']);

            if (empty($optionsByBlock['iblocks_list'])) {
                __AdmSettingsDrawRow($module_id, $optionsByBlock['iblock_note']);
            }

            __AdmSettingsDrawList($module_id, $optionsByBlock['iblocks_list']);

            __AdmSettingsDrawList($module_id, $optionsByBlock['exclude_list']);
        }

        $tabControl->BeginNextTab();
        ?>
        <iframe src="https://yoomoney.ru/quickpay/shop-widget?writer=seller&default-sum=50&button-text=12&payment-type-choice=on&successURL=&quickpay=shop&account=410014134044507&targets=%D0%9F%D0%B5%D1%80%D0%B5%D0%B2%D0%BE%D0%B4%20%D0%BF%D0%BE%20%D0%BA%D0%BD%D0%BE%D0%BF%D0%BA%D0%B5&"
                width="423" height="222" frameborder="0" allowtransparency="true" scrolling="no"></iframe>
        <?
        __AdmSettingsDrawRow($module_id, $optionsByBlock2['support_note']);

        $tabControl->Buttons();
        ?>

        <input type="submit" name="apply" value="<?= Loc::GetMessage('SL3W_WATERMARK_BUTTON_APPLY') ?>"
               class="adm-btn-save"/>
        <input type="submit" name="default" value="<?= Loc::GetMessage('SL3W_WATERMARK_BUTTON_DEFAULT') ?>"/>

        <?= bitrix_sessid_post() ?>

    </form>

<?php
$tabControl->End();

if ($request->isPost() && check_bitrix_sessid()) {

    foreach ($aTabs as $aTab) {

        foreach ($aTab['OPTIONS'] as $arOption) {

            if (!is_array($arOption) || $arOption['note']) {
                continue;
            }

            $optionCode = $arOption[0];

            if ($request['apply']) {

                $optionValue = $request->getPost($optionCode);

                if ($arOption[3][0] == 'checkbox' && $optionValue == '') {
                    $optionValue = 'N';
                }

                Option::set($module_id, $optionCode, is_array($optionValue) ? implode(',', $optionValue) : $optionValue);

                if ($optionCode == 'iblock_ids') {
                    foreach ($optionValue as $value) {
                        $optionName = 'iblock' . $value . '_fields';
                        $optionValueFields = $request->getPost($optionName);

                        Option::set($module_id, $optionName, is_array($optionValueFields) ? implode(',', $optionValueFields) : $optionValueFields);
                    }
                }

                if ($optionCode == 'add_watermark_btn_switch_on') {
                    register_add_watermark_btn_events($optionValue == 'Y' && $request->getPost('switch_on'));
                }

                if ($optionCode == 'add_watermark_btn_mass_switch_on') {
                    register_add_watermark_mass_events($optionValue == 'Y' && $request->getPost('switch_on'));
                }

            } elseif ($request['default']) {

                Option::set($module_id, $optionCode, $arOption[2]);

                if ($optionCode == 'add_watermark_btn_switch_on') {
                    register_add_watermark_btn_events(false);
                }

                if ($optionCode == 'add_watermark_btn_mass_switch_on') {
                    register_add_watermark_mass_events(false);
                }
            }
        }
    }

    LocalRedirect($APPLICATION->GetCurPage() . '?mid=' . $module_id . '&lang=' . LANG . '&mid_menu=1');
}