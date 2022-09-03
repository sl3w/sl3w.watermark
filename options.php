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
    Loc::getMessage('SL3W_WATERMARK_BLOCK_IBLOCK'),
    [
        'iblock_ids',
        Loc::getMessage('SL3W_WATERMARK_OPTION_IBLOCK_IDS'),
        '',
        ['multiselectbox', $selectIBlocks]
    ],
    ['note' => Loc::getMessage('SL3W_WATERMARK_SAVE_AFTER_CHANGE_IBLOCK')],
];

$aTabs = array(
    [
        'DIV' => 'edit',
        'TAB' => Loc::getMessage('SL3W_WATERMARK_OPTIONS_TAB_NAME'),
        'TITLE' => Loc::getMessage('SL3W_WATERMARK_OPTIONS_TAB_NAME'),
        'OPTIONS' => $options
    ]
);

$optionsByBlock = [
    'common_block' => Loc::getMessage('SL3W_WATERMARK_BLOCK_COMMON'),
    'common_list' => [
        [
            'switch_on',
            Loc::getMessage('SL3W_WATERMARK_OPTION_SWITCH_ON'),
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
    'iblocks_list' => []
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
            __AdmSettingsDrawRow($module_id, $optionsByBlock['wm_block']);
            __AdmSettingsDrawList($module_id, $optionsByBlock['wm_list']);

            foreach ($optionsByBlock['wm_list_special'] as $wm_list_special_option) {
                ?>
                <tr>
                    <td><?= $wm_list_special_option[1] ?></td>
                    <td>
                        <input type="<?= $wm_list_special_option[3][0] ?: 'text' ?>" name="<?= $wm_list_special_option[0] ?>"
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

            __AdmSettingsDrawRow($module_id, $optionsByBlock['iblock_block']);
            __AdmSettingsDrawList($module_id, $optionsByBlock['iblock_list']);
            if (empty($optionsByBlock['iblocks_list'])) {
                __AdmSettingsDrawRow($module_id, $optionsByBlock['iblock_note']);
            }
            __AdmSettingsDrawList($module_id, $optionsByBlock['iblocks_list']);
        }

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

            if ($request['apply']) {

                $optionValue = $request->getPost($arOption[0]);

                if ($arOption[0] == 'switch_on' && $optionValue == '') {
                    $optionValue = 'N';
                }

                Option::set($module_id, $arOption[0], is_array($optionValue) ? implode(',', $optionValue) : $optionValue);

                if ($arOption[0] == 'iblock_ids') {
                    foreach ($optionValue as $value) {
                        $optionName = 'iblock' . $value . '_fields';
                        $optionValueFields = $request->getPost($optionName);

                        Option::set($module_id, $optionName, is_array($optionValueFields) ? implode(',', $optionValueFields) : $optionValueFields);
                    }
                }

            } elseif ($request['default']) {

                Option::set($module_id, $arOption[0], $arOption[2]);
            }
        }
    }

    LocalRedirect($APPLICATION->GetCurPage() . '?mid=' . $module_id . '&lang=' . LANG);
}