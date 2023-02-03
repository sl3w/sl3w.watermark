<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Sl3w\Watermark\Helpers;
use Sl3w\Watermark\Iblock;
use Sl3w\Watermark\Settings;

Loc::loadMessages(__FILE__);

/** @global CMain $APPLICATION */

const LANGS_PREFIX = 'SL3W_WATERMARK_';

$request = HttpApplication::getInstance()->getContext()->getRequest();

$module_id = htmlspecialcharsbx($request['mid'] != '' ? $request['mid'] : $request['id']);

if (!Loader::includeModule($module_id)) {
    ShowMessage(Loc::getMessage(LANGS_PREFIX . 'MODULE_INCLUDE_ERROR'));

    return false;
}

if (!Loader::includeModule('iblock')) {
    ShowMessage(Loc::getMessage('IBLOCK_ERROR'));

    return false;
}

$selectIBlocks = [0 => Loc::getMessage(LANGS_PREFIX . 'OPTION_EMPTY')];

$dbIBlocks = CIBlock::GetList(['SORT' => 'ID'], ['ACTIVE' => 'Y']);

$allIbIds = [];

while ($arIBlock = $dbIBlocks->GetNext()) {
    $allIbIds[] = $arIBlock['ID'];
    $selectIBlocks[$arIBlock['ID']] = sprintf('[%s] %s', $arIBlock['ID'], $arIBlock['NAME']);
}

$selectFields = [
    'PREVIEW_PICTURE' => Loc::getMessage(LANGS_PREFIX . 'OPTIONS_PREVIEW_PICTURE'),
    'DETAIL_PICTURE' => Loc::getMessage(LANGS_PREFIX . 'OPTIONS_DETAIL_PICTURE'),
];

$positionsVars = ['tl', 'tc', 'tr', 'ml', 'mc', 'mr', 'bl', 'bc', 'br'];

foreach ($positionsVars as $positionsVar) {
    $selectPositions[$positionsVar] = Loc::getMessage(sprintf('%sOPTION_WM_POSITION_%s', LANGS_PREFIX, Helpers::toUpper($positionsVar)));
}

$options = [
    Loc::getMessage(LANGS_PREFIX . 'BLOCK_COMMON'),
    [
        'switch_on',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_SWITCH_ON'),
        'Y',
        ['checkbox']
    ],
    [
        'add_watermark_btn_mass_switch_on',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_ADD_WATERMARK_BTN_MASS'),
        'Y',
        ['checkbox']
    ],
    [
        'add_watermark_btn_switch_on',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_ADD_WATERMARK_BTN'),
        'N',
        ['checkbox']
    ],
    Loc::getMessage(LANGS_PREFIX . 'BLOCK_EVENTS'),
    [
        'event_add_switch_on',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_EVENT_ADD_SWITCH_ON'),
        'Y',
        ['checkbox']
    ],
    [
        'event_update_switch_on',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_EVENT_UPDATE_SWITCH_ON'),
        'Y',
        ['checkbox']
    ],
    Loc::getMessage(LANGS_PREFIX . 'BLOCK_WM'),
    [
        'wm_position',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_POSITION'),
        'br',
        ['selectbox', $selectPositions]
    ],
    [
        'wm_is_repeat',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_IS_REPEAT'),
        'N',
        ['checkbox']
    ],
    [
        'wm_alpha',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_ALPHA'),
        '50',
        ['text', 3],
        '',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_ALPHA_NUMBER')
    ],
    [
        'wm_image_path',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_IMAGE_PATH'),
        '',
        ['text', 30]
    ],
    Loc::getMessage(LANGS_PREFIX . 'SET_DONT_ADD_IBLOCK'),
    [
        'set_dont_add_after_add',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_SET_DONT_ADD_AFTER_ADD'),
        '',
        ['checkbox']
    ],
    [
        'set_dont_add_after_update',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_SET_DONT_ADD_AFTER_UPDATE'),
        '',
        ['checkbox']
    ],
    ['note' => Loc::getMessage(LANGS_PREFIX . 'SET_DONT_ADD_NOTE')],
    Loc::getMessage(LANGS_PREFIX . 'BLOCK_IBLOCK'),
    [
        'iblock_ids',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_IBLOCK_IDS'),
        '',
        ['multiselectbox', $selectIBlocks]
    ],
//    ['note' => Loc::getMessage(LANGS_PREFIX . 'SAVE_AFTER_CHANGE_IBLOCK')],
    Loc::getMessage(LANGS_PREFIX . 'BLOCK_EXCLUDE'),
    [
        'exclude_elements_ids',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_EXCLUDE_ELEMENTS'),
        '',
        ['textarea', 5, 50]
    ],
];

$aTabs = [
    [
        'DIV' => 'edit',
        'TAB' => Loc::getMessage(LANGS_PREFIX . 'OPTIONS_TAB_NAME'),
        'TITLE' => Loc::getMessage(LANGS_PREFIX . 'OPTIONS_TAB_NAME'),
        'OPTIONS' => $options
    ],
    [
        'DIV' => 'support',
        'TAB' => Loc::getMessage(LANGS_PREFIX . 'SUPPORT_TAB_NAME'),
        'TITLE' => Loc::getMessage(LANGS_PREFIX . 'SUPPORT_TAB_TITLE'),
    ]
];

$optionsByBlock = [
    'common_block' => Loc::getMessage(LANGS_PREFIX . 'BLOCK_COMMON'),
    'common_list' => [
        [
            'switch_on',
            Loc::getMessage(LANGS_PREFIX . 'OPTION_SWITCH_ON'),
            'Y',
            ['checkbox']
        ],
        [
            'add_watermark_btn_mass_switch_on',
            Loc::getMessage(LANGS_PREFIX . 'OPTION_ADD_WATERMARK_BTN_MASS'),
            'Y',
            ['checkbox']
        ],
        [
            'add_watermark_btn_switch_on',
            Loc::getMessage(LANGS_PREFIX . 'OPTION_ADD_WATERMARK_BTN'),
            'N',
            ['checkbox']
        ]
    ],
    'events_block' => Loc::getMessage(LANGS_PREFIX . 'BLOCK_EVENTS'),
    'events_list' => [
        [
            'event_add_switch_on',
            Loc::getMessage(LANGS_PREFIX . 'OPTION_EVENT_ADD_SWITCH_ON'),
            'Y',
            ['checkbox']
        ],
        [
            'event_update_switch_on',
            Loc::getMessage(LANGS_PREFIX . 'OPTION_EVENT_UPDATE_SWITCH_ON'),
            'Y',
            ['checkbox']
        ]
    ],
    'wm_block' => Loc::getMessage(LANGS_PREFIX . 'BLOCK_WM'),
    'wm_list' => [
        [
            'wm_position',
            Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_POSITION'),
            'br',
            ['selectbox', $selectPositions]
        ],
        [
            'wm_is_repeat',
            Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_IS_REPEAT'),
            'N',
            ['checkbox']
        ],
    ],
    'wm_list_special' => [
        [
            'wm_alpha',
            Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_ALPHA'),
            '50',
            ['text', 3],
        ],
        [
            'wm_image_path',
            Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_IMAGE_PATH'),
            '',
            ['text', 50]
        ]
    ],
    'dont_add_block' => Loc::getMessage(LANGS_PREFIX . 'SET_DONT_ADD_IBLOCK'),
    'dont_add_list' => [
        [
            'set_dont_add_after_add',
            Loc::getMessage(LANGS_PREFIX . 'OPTION_SET_DONT_ADD_AFTER_ADD'),
            '',
            ['checkbox']
        ],
        [
            'set_dont_add_after_update',
            Loc::getMessage(LANGS_PREFIX . 'OPTION_SET_DONT_ADD_AFTER_UPDATE'),
            '',
            ['checkbox']
        ],
    ],
    'dont_add_note' => ['note' => Loc::getMessage(LANGS_PREFIX . 'SET_DONT_ADD_NOTE')],
    'iblock_block' => Loc::getMessage(LANGS_PREFIX . 'BLOCK_IBLOCK'),
    'iblock_list' => [
        [
            'iblock_ids',
            Loc::getMessage(LANGS_PREFIX . 'OPTION_IBLOCK_IDS'),
            '',
            ['multiselectbox', $selectIBlocks]
        ]
    ],
//    'iblock_note' => ['note' => Loc::getMessage(LANGS_PREFIX . 'SAVE_AFTER_CHANGE_IBLOCK')],
    'iblocks_list' => [],
    'exclude_list' => [
        Loc::getMessage(LANGS_PREFIX . 'BLOCK_EXCLUDE'),
        [
            'exclude_elements_ids',
            Loc::getMessage(LANGS_PREFIX . 'OPTION_EXCLUDE_ELEMENTS'),
            '',
            ['textarea', 5, 50]
        ],
    ],
];

$selectedIbs = Settings::getProcessingIBlocks();
$iblockIds = $allIbIds;

foreach ($iblockIds as $iblockId) {
    if (!intval($iblockId)) {
        continue;
    }

    $selectFieldsAndProps = $selectFields;

    $propsRes = CIBlock::GetProperties($iblockId);

    while ($prop = $propsRes->Fetch()) {
        if ($prop['PROPERTY_TYPE'] == 'F') {
            $selectFieldsAndProps['PROPERTY_' . $prop['CODE']] = sprintf('[PROPERTY_%s] %s', $prop['CODE'], $prop['NAME']);
        }
    }

    $optionsByBlock['iblocks_list'][] = [
        'iblock' . $iblockId . '_fields',
        sprintf('%s [%s] "%s":', Loc::getMessage(LANGS_PREFIX . 'FIELDS_AND_PROPS'), $iblockId, Iblock::getIBlockNameById($iblockId)),
        '',
        ['multiselectbox', $selectFieldsAndProps]
    ];
}

$optionsByBlock2 = [
    'support_note' => ['note' => Loc::getMessage(LANGS_PREFIX . 'SUPPORT_NOTE')],
];

$tabControl = new CAdminTabControl(
    'tabControl',
    $aTabs
);

$tabControl->Begin();
?>

<form action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= $module_id ?>&lang=<?= LANG ?>" method="post"
      name="sl3w_watermark">

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
            $optionName = $wm_list_special_option[0];
            $optionValue = Settings::get($optionName);
            ?>
            <tr>
                <td><?= $wm_list_special_option[1] ?></td>
                <td>
                    <input type="<?= $wm_list_special_option[3][0] ?: 'text' ?>"
                           name="<?= $wm_list_special_option[0] ?>"
                           size="<?= $wm_list_special_option[3][1] ?: 10 ?>"
                           value="<?= $optionValue ?>"/>

                    <?php switch ($optionName) {
                        case 'wm_alpha':
                            echo Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_ALPHA_AFTER');

                            break;

                        case 'wm_image_path':
                            ?>

                            <input type="button" value="..." onclick="Sl3wWmOpenFileDialog()">

                            <?php
                            CAdminFileDialog::ShowScript(
                                [
                                    'event' => 'Sl3wWmOpenFileDialog',
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
            <?php if ($optionName == 'wm_image_path' && $optionValue): ?>
                <tr>
                    <td><?= Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_IMAGE') ?></td>
                    <td>
                        <img id="sl3w_watermark_selected_image" src="<?= $optionValue ?>"
                             style="max-width: 200px; height: 100px; display: block; margin-bottom: 20px; border: 1px solid #000;">
                    </td>
                </tr>
            <?php endif;
        }

        __AdmSettingsDrawRow($module_id, $optionsByBlock['iblock_block']);
        __AdmSettingsDrawList($module_id, $optionsByBlock['iblock_list']);

        if (empty($optionsByBlock['iblocks_list'])) {
            __AdmSettingsDrawRow($module_id, $optionsByBlock['iblock_note']);
        }

        __AdmSettingsDrawList($module_id, $optionsByBlock['iblocks_list']);

        __AdmSettingsDrawList($module_id, $optionsByBlock['exclude_list']);

        __AdmSettingsDrawRow($module_id, $optionsByBlock['dont_add_block']);
        __AdmSettingsDrawList($module_id, $optionsByBlock['dont_add_list']);
        __AdmSettingsDrawRow($module_id, $optionsByBlock['dont_add_note']);
    }

    $tabControl->BeginNextTab();
    ?>

    <iframe src="https://yoomoney.ru/quickpay/shop-widget?writer=seller&default-sum=50&button-text=12&payment-type-choice=on&successURL=&quickpay=shop&account=410014134044507&targets=%D0%9F%D0%B5%D1%80%D0%B5%D0%B2%D0%BE%D0%B4%20%D0%BF%D0%BE%20%D0%BA%D0%BD%D0%BE%D0%BF%D0%BA%D0%B5&"
            width="423" height="222" frameborder="0" allowtransparency="true" scrolling="no"></iframe>

    <?php
    __AdmSettingsDrawRow($module_id, $optionsByBlock2['support_note']);

    $tabControl->Buttons();
    ?>

    <input type="submit" name="apply" value="<?= Loc::getMessage(LANGS_PREFIX . 'BUTTON_APPLY') ?>"
           class="adm-btn-save"/>
    <input type="submit" name="default" value="<?= Loc::getMessage(LANGS_PREFIX . 'BUTTON_DEFAULT') ?>"/>

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

                Settings::set($optionCode, is_array($optionValue) ? implode(',', $optionValue) : $optionValue);

                if ($optionCode == 'iblock_ids') {
                    foreach ($optionValue as $value) {
                        $optionName = 'iblock' . $value . '_fields';
                        $optionValueFields = $request->getPost($optionName);

                        Settings::set($optionName, is_array($optionValueFields) ? implode(',', $optionValueFields) : $optionValueFields);
                    }
                }

                if ($optionCode == 'add_watermark_btn_switch_on') {
                    register_add_watermark_btn_events($optionValue == 'Y' && $request->getPost('switch_on'));
                }

                if ($optionCode == 'add_watermark_btn_mass_switch_on') {
                    register_add_watermark_mass_events($optionValue == 'Y' && $request->getPost('switch_on'));
                }

            } elseif ($request['default']) {
                Settings::set($optionCode, $arOption[2]);

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
?>

<script>
    const selectedIbs = <?= CUtil::PhpToJSObject($selectedIbs, false, true) ?>;
    const allIbs = <?= CUtil::PhpToJSObject($allIbIds, false, true) ?>;
    const input = document.querySelector('input[name="wm_image_path"]');

    function setImgFromSelected() {
        document.getElementById('sl3w_watermark_selected_image').src = input.value;
    }

    input.onchange = setImgFromSelected;
    input.oninput = setImgFromSelected;

    function getSelectorById(iBId) {
        return document.querySelector('select[name="iblock' + iBId + '_fields[]"]');
    }

    for (const allIb of allIbs) {
        const selectorByIb = getSelectorById(allIb);

        selectorByIb.size = selectorByIb.length > 10 ? 10 : selectorByIb.length;

        if (!selectedIbs.includes(allIb)) {
            selectorByIb.closest('tr').style.display = 'none';
        }
    }

    const iBsSelector = document.querySelector('select[name="iblock_ids[]"]');

    iBsSelector.size = iBsSelector.length > 10 ? 10 : (iBsSelector.length < 5 ? 5 : iBsSelector.length);

    iBsSelector.addEventListener('change', function (e) {
        const selectedIbsNow = e.target.selectedOptions;

        for (const allIb of allIbs) {
            getSelectorById(allIb).closest('tr').style.display = 'none';
        }

        for (const selectedIbNow of selectedIbsNow) {
            getSelectorById(selectedIbNow.value).closest('tr').style.display = 'table-row';
        }
    });
</script>