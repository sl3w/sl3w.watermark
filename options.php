<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Sl3w\Watermark\Helpers;
use Sl3w\Watermark\Iblock;
use Sl3w\Watermark\Settings;

Loc::loadMessages(__FILE__);

/** @global CMain $APPLICATION */

const LANGS_PREFIX = 'SL3W_WATERMARK_';

$request = HttpApplication::getInstance()->getContext()->getRequest();

$moduleId = htmlspecialcharsbx($request['mid'] != '' ? $request['mid'] : $request['id']);

if (!Loader::includeModule($moduleId)) {
    ShowMessage(Loc::getMessage(LANGS_PREFIX . 'MODULE_INCLUDE_ERROR'));

    return false;
}

if (!Loader::includeModule('iblock')) {
    ShowMessage(Loc::getMessage(LANGS_PREFIX . 'MODULE_IBLOCK_ERROR'));

    return false;
}

if (!Loader::includeModule('fileman')) {
    ShowMessage(Loc::getMessage(LANGS_PREFIX . 'MODULE_FILEMAN_ERROR'));

    return false;
}

//заменяем путь файла на ID файла
$wmImagePath = Settings::getWatermark();

if (!is_numeric($wmImagePath)) {
    $absFilePath = $_SERVER['DOCUMENT_ROOT'] . htmlspecialcharsbx($wmImagePath);
    $wmImagePath = explode('/', $wmImagePath);
    $arOriginalName = array_pop($wmImagePath);

    if (file_exists($absFilePath)) {
        $arFile = CFile::MakeFileArray($absFilePath);
        $arFile['name'] = $arOriginalName;

        if ($fileId = CFile::SaveFile($arFile, str_replace('.', '_', $moduleId))) {
            Settings::set('wm_image_path', $fileId);
        }
    }
}
//\заменяем путь файла на ID файла

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
    'main_block' => Loc::getMessage(LANGS_PREFIX . 'BLOCK_MAIN'),
    'switch_on' => [
        'switch_on',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_SWITCH_ON'),
        'Y',
        ['checkbox']
    ],
    'add_watermark_btn_mass_switch_on' => [
        'add_watermark_btn_mass_switch_on',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_ADD_WATERMARK_BTN_MASS'),
        'Y',
        ['checkbox']
    ],
    'add_watermark_btn_switch_on' => [
        'add_watermark_btn_switch_on',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_ADD_WATERMARK_BTN'),
        'N',
        ['checkbox']
    ],
    'events_block' => Loc::getMessage(LANGS_PREFIX . 'BLOCK_EVENTS'),
    'event_add_switch_on' => [
        'event_add_switch_on',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_EVENT_ADD_SWITCH_ON'),
        'Y',
        ['checkbox']
    ],
    'event_update_switch_on' => [
        'event_update_switch_on',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_EVENT_UPDATE_SWITCH_ON'),
        'Y',
        ['checkbox']
    ],
    'process_sku' => [
        'process_sku',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_PROCESS_SKU'),
        'N',
        ['checkbox']
    ],
    'wm_block_image' => Loc::getMessage(LANGS_PREFIX . 'BLOCK_WM_IMAGE'),
    'switch_on_image' => [
        'switch_on_image',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_SWITCH_ON_IMAGE'),
        'Y',
        ['checkbox']
    ],
    'wm_position' => [
        'wm_position',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_POSITION'),
        'br',
        ['selectbox', $selectPositions]
    ],
    'wm_is_repeat' => [
        'wm_is_repeat',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_IS_REPEAT'),
        'N',
        ['checkbox']
    ],
    'wm_alpha' => [
        'wm_alpha',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_ALPHA'),
        '50',
        ['number', 3, 0, 100],
        '',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_ALPHA_NUMBER')
    ],
    'wm_max_percent' => [
        'wm_max_percent',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_MAX_PERCENT'),
        '50',
        ['number', 3, 0, 100],
        '',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_MAX_PERCENT_NUMBER')
    ],
    'wm_image_path' => [
        'wm_image_path',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_IMAGE_PATH'),
        '',
        ['text', 30]
    ],
    'wm_block_text' => Loc::getMessage(LANGS_PREFIX . 'BLOCK_WM_TEXT'),
    'switch_on_text' => [
        'switch_on_text',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_SWITCH_ON_TEXT'),
        'Y',
        ['checkbox']
    ],
    'wm_position_text' => [
        'wm_position_text',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_POSITION'),
        'br',
        ['selectbox', $selectPositions]
    ],
    'wm_text' => [
        'wm_text',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_TEXT'),
        '',
        ['text', 50],
    ],
    'wm_text_color' => [
        'wm_text_color',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_TEXT_COLOR'),
        'ffffff',
        ['color'],
    ],
    'wm_max_percent_text' => [
        'wm_max_percent_text',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_MAX_PERCENT'),
        '50',
        ['number', 3, 0, 100],
        '',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_MAX_PERCENT_NUMBER')
    ],
    'wm_text_font' => [
        'wm_text_font',
        Loc::getMessage('SL3W_WATERMARK_OPTION_WM_TEXT_FONT'),
        '/bitrix/fonts/pt_sans-regular.ttf',
        ['text', 50]
    ],
    'dont_add_block' => Loc::getMessage(LANGS_PREFIX . 'SET_DONT_ADD_IBLOCK'),
    'set_dont_add_after_add' => [
        'set_dont_add_after_add',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_SET_DONT_ADD_AFTER_ADD'),
        '',
        ['checkbox']
    ],
    'set_dont_add_after_update' => [
        'set_dont_add_after_update',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_SET_DONT_ADD_AFTER_UPDATE'),
        '',
        ['checkbox']
    ],
    'dont_add_note' => ['note' => Loc::getMessage(LANGS_PREFIX . 'SET_DONT_ADD_NOTE')],
    'iblock_block' => Loc::getMessage(LANGS_PREFIX . 'BLOCK_IBLOCK'),
    'iblock_ids' => [
        'iblock_ids',
        Loc::getMessage(LANGS_PREFIX . 'OPTION_IBLOCK_IDS'),
        '',
        ['multiselectbox', $selectIBlocks]
    ],
    'exclude_block' => Loc::getMessage(LANGS_PREFIX . 'BLOCK_EXCLUDE'),
    'exclude_elements_ids' => [
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
        'TITLE' => Loc::getMessage(LANGS_PREFIX . 'SUPPORT_TAB_NAME'),
    ]
];

$optionsByBlock = [
    'main_list' => [
        $options['main_block'],
        $options['switch_on'],
        $options['add_watermark_btn_mass_switch_on'],
        $options['add_watermark_btn_switch_on'],
    ],
    'events_list' => [
        $options['events_block'],
        $options['event_add_switch_on'],
        $options['event_update_switch_on'],
        $options['process_sku'],
    ],
    'wm_list_image' => [
        $options['wm_block_image'],
        $options['switch_on_image'],
        $options['wm_position'],
        $options['wm_is_repeat'],
    ],
    'wm_list_special_image' => [
        $options['wm_alpha'],
        $options['wm_max_percent'],
        $options['wm_image_path'],
    ],
    'wm_list_text' => [
        $options['wm_block_text'],
        $options['switch_on_text'],
        $options['wm_position_text'],
        $options['wm_text'],
    ],
    'wm_list_special_text' => [
        $options['wm_text_color'],
        $options['wm_max_percent_text'],
        $options['wm_text_font'],
    ],
    'dont_add_list' => [
        $options['dont_add_block'],
        $options['set_dont_add_after_add'],
        $options['set_dont_add_after_update'],
        $options['dont_add_note']
    ],
    'iblock_list' => [
        $options['iblock_block'],
        $options['iblock_ids'],
    ],
    'iblocks_list' => [],
    'exclude_list' => [
        $options['exclude_block'],
        $options['exclude_elements_ids'],
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

$dontShowInputOptions = ['wm_image_path', 'wm_text_color'];

$tabControl->Begin();
?>

<form enctype="multipart/form-data" method="post" name="sl3w_watermark"
      action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= $moduleId ?>&lang=<?= LANG ?>">

    <?php
    $tabControl->BeginNextTab();

    if ($optionsByBlock) {
        __AdmSettingsDrawList($moduleId, $optionsByBlock['main_list']);
        
        __AdmSettingsDrawList($moduleId, $optionsByBlock['events_list']);
        
        __AdmSettingsDrawList($moduleId, $optionsByBlock['wm_list_image']);

        foreach ($optionsByBlock['wm_list_special_image'] as $wm_list_special_option) {
            $optionName = $wm_list_special_option[0];
            $optionValue = Settings::get($optionName);
            ?>
            <tr>
                <td><?= $wm_list_special_option[1] ?></td>
                <td>
                    <?php if (!in_array($optionName, $dontShowInputOptions)) : ?>
                        <input class="adm-input"
                               type="<?= $wm_list_special_option[3][0] ?: 'text' ?>"
                               name="<?= $optionName ?>"
                               size="<?= $wm_list_special_option[3][1] ?: 10 ?>"
                            <?= isset($wm_list_special_option[3][2]) && $wm_list_special_option[3][0] == 'number' ? sprintf('min="%s"', $wm_list_special_option[3][2]) : '' ?>
                            <?= isset($wm_list_special_option[3][3]) && $wm_list_special_option[3][0] == 'number' ? sprintf('max="%s"', $wm_list_special_option[3][3]) : '' ?>
                               value="<?= $optionValue ?>"/>
                    <?php endif; ?>

                    <?php switch ($optionName) {
                        case 'wm_alpha':
                            echo Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_ALPHA_AFTER');

                            break;

                        case 'wm_max_percent':
                            echo Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_MAX_PERCENT_AFTER');

                            break;

                        case 'wm_image_path':
                            $optionValue = $optionValue ?? 0;

                            echo CFileInput::Show(
                                $wm_list_special_option[0],
                                $optionValue,
                                [
                                    'IMAGE' => '',
                                    'PATH' => 'Y',
                                    'FILE_SIZE' => 'Y',
                                    'DIMENSIONS' => 'Y',
                                    'IMAGE_POPUP' => 'Y',
                                    'MAX_SIZE' => [
                                        'W' => 300,
                                        'H' => 150,
                                    ],
                                ],
                                [
                                    'upload' => true,
                                    'medialib' => true,
                                    'file_dialog' => true,
                                    'cloud' => true,
                                    'del' => true,
                                    'description' => false,
                                ]
                            );

                            break;
                    } ?>
                </td>
            </tr>
            <?php
        }

        __AdmSettingsDrawList($moduleId, $optionsByBlock['wm_list_text']);

        foreach ($optionsByBlock['wm_list_special_text'] as $wm_list_special_option) {
            $optionName = $wm_list_special_option[0];
            $optionValue = Settings::get($optionName);
            ?>
            <tr>
                <td><?= $wm_list_special_option[1] ?></td>
                <td>
                    <?php if (!in_array($optionName, $dontShowInputOptions)) : ?>
                        <input class="adm-input"
                               type="<?= $wm_list_special_option[3][0] ?: 'text' ?>"
                               name="<?= $optionName ?>"
                               size="<?= $wm_list_special_option[3][1] ?: 10 ?>"
                            <?= isset($wm_list_special_option[3][2]) && $wm_list_special_option[3][0] == 'number' ? sprintf('min="%s"', $wm_list_special_option[3][2]) : '' ?>
                            <?= isset($wm_list_special_option[3][3]) && $wm_list_special_option[3][0] == 'number' ? sprintf('max="%s"', $wm_list_special_option[3][3]) : '' ?>
                               value="<?= $optionValue ?>"/>
                    <?php endif; ?>

                    <?php switch ($optionName) {
                        case 'wm_text_color':
                            $optionValue = Helpers::clearColorHex($optionValue);
                            ?>

                            <input type="<?= $wm_list_special_option[3][0] ?>"
                                   name="<?= $optionName ?>"
                                   value="#<?= $optionValue ?>"/>

                            <?php
                            break;

                        case 'wm_max_percent_text':
                            echo Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_MAX_PERCENT_AFTER');

                            break;

                        case 'wm_text_font':
                            ?>
                            <input type="button" value="..." onclick="Sl3wWmOpenFileDialogFont()">
                            <?php
                            CAdminFileDialog::ShowScript(
                                [
                                    'event' => "Sl3wWmOpenFileDialogFont",
                                    'arResultDest' => ['FORM_NAME' => 'sl3w_watermark', 'FORM_ELEMENT_NAME' => 'wm_text_font'],
                                    'arPath' => ['PATH' => ''],
                                    'select' => 'F',
                                    'operation' => 'O',
                                    'showUploadTab' => true,
                                    'showAddToMenuTab' => false,
                                    'fileFilter' => 'ttf',
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

        __AdmSettingsDrawList($moduleId, $optionsByBlock['iblock_list']);

        __AdmSettingsDrawList($moduleId, $optionsByBlock['iblocks_list']);

        __AdmSettingsDrawList($moduleId, $optionsByBlock['exclude_list']);

        __AdmSettingsDrawList($moduleId, $optionsByBlock['dont_add_list']);
    }

    $tabControl->BeginNextTab();
    ?>

    <p>
        <?= Loc::getMessage(LANGS_PREFIX . 'SUPPORT_TAB_TEXT') ?>
    </p>
    <p>
        <?= Loc::getMessage(LANGS_PREFIX . 'SUPPORT_TAB_TEXT2') ?>
    </p>

    <iframe
        src="https://yoomoney.ru/quickpay/shop-widget?writer=seller&default-sum=500&button-text=12&payment-type-choice=on&successURL=&quickpay=shop&account=410014134044507&targets=%D0%9F%D0%B5%D1%80%D0%B5%D0%B2%D0%BE%D0%B4%20%D0%BF%D0%BE%20%D0%BA%D0%BD%D0%BE%D0%BF%D0%BA%D0%B5&"
        width="423" height="222" frameborder="0" allowtransparency="true" scrolling="no"></iframe>

    <p>
        <?= Loc::getMessage(LANGS_PREFIX . 'SUPPORT_TAB_TEXT3') ?>
    </p>
    <p>
        <?= Loc::getMessage(LANGS_PREFIX . 'SUPPORT_TAB_TEXT4') ?>
    </p>

    <iframe width="728" height="200"
            src="https://widget.qiwi.com/widgets/big-widget-728x200?publicKey=48e7qUxn9T7RyYE1MVZswX1FRSbE6iyCj2gCRwwF3Dnh5XrasNTx3BGPiMsyXQFNKQhvukniQG8RTVhYm3iPstc1jW1Xqr2MRaWGf1QqrwLmYzwpyMrob7toJfNfeZzWQZfXGbDGx5g6BqdbjdyGAYrnkNpyu8b9RHEcDXP6idAksvLs3grZ2TYnyACuD"
            allowtransparency="true" scrolling="no" frameborder="0"></iframe>

    <p>
        <?= Loc::getMessage(LANGS_PREFIX . 'SUPPORT_TAB_TEXT5') ?>
    </p>
    <p>
        <?= Loc::getMessage(LANGS_PREFIX . 'SUPPORT_TAB_TEXT6') ?>
    </p>

    <?php
    __AdmSettingsDrawRow($moduleId, $optionsByBlock2['support_note']);

    $tabControl->Buttons();
    ?>

    <input type="submit" name="apply" value="<?= Loc::getMessage(LANGS_PREFIX . 'BUTTON_APPLY') ?>"
           class="adm-btn-save">
    <input type="submit" name="default" value="<?= Loc::getMessage(LANGS_PREFIX . 'BUTTON_DEFAULT') ?>" style="float: right">

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

                switch ($optionCode) {
                    case 'wm_alpha':
                    case 'wm_max_percent':
                    case 'wm_max_percent_text':
                    case 'wm_text':
                    case 'wm_text_font':
                        $optionValue = !$optionValue ? $arOption[2] : $optionValue;

                        break;

                    case 'wm_text_color':
                        $optionValue = Helpers::clearColorHex(!$optionValue ? $arOption[2] : $optionValue);

                        break;

                    case 'iblock_ids':
                        foreach ($optionValue as $value) {
                            $optionName = 'iblock' . $value . '_fields';
                            $optionValueFields = $request->getPost($optionName);

                            Settings::set($optionName, is_array($optionValueFields) ? implode(',', $optionValueFields) : $optionValueFields);
                        }

                        break;

                    case 'add_watermark_btn_switch_on':
                        register_add_watermark_btn_events($optionValue == 'Y' && $request->getPost('switch_on'));

                        break;

                    case 'add_watermark_btn_mass_switch_on':
                        register_add_watermark_mass_events($optionValue == 'Y' && $request->getPost('switch_on'));

                        break;

                    case 'wm_image_path':
                        $currentValue = Settings::get($optionCode, 0);

                        $filesByOption = $_FILES[$optionCode];

                        //в $optionValue (в $_REQUEST) будет путь к файлу, если файл залит через медиабиблиотеку или структуру,
                        //но нам в конечном итоге нужен ID файла

                        //если файл удален или залит новый, удаляем старый файл
                        if (
                            $request->getPost($optionCode . '_del') || $optionValue ||
                            (isset($filesByOption) && strlen($filesByOption['tmp_name']))
                        ) {
                            CFile::Delete($currentValue);
                        }

                        if ((isset($filesByOption) && strlen($filesByOption['tmp_name'])) || $optionValue) {
                            $optionValue = 0;

                            if (isset($filesByOption)) {
                                $absFilePath = $filesByOption['tmp_name'];
                                $arOriginalName = $filesByOption['name'];
                            } else {
                                $filePath = $_REQUEST[$optionCode];
                                $absFilePath = $_SERVER['DOCUMENT_ROOT'] . htmlspecialcharsbx($filePath);
                                $filePath = explode('/', $filePath);
                                $arOriginalName = array_pop($filePath);
                            }

                            if (file_exists($absFilePath)) {
                                $arFile = CFile::MakeFileArray($absFilePath);
                                $arFile['name'] = $arOriginalName;

                                if ($fileId = CFile::SaveFile($arFile, str_replace('.', '_', $moduleId))) {
                                    $optionValue = $fileId;
                                }
                            }
                        } else {
                            $optionValue = $currentValue;
                        }

                        break;
                }

                $optionValue = is_array($optionValue) ? implode(',', $optionValue) : $optionValue;

                Settings::set($optionCode, $optionValue);

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

    LocalRedirect($APPLICATION->GetCurPage() . '?mid=' . $moduleId . '&lang=' . LANG . '&mid_menu=1');
}
?>

<script>
    const selectedIbs = <?= CUtil::PhpToJSObject($selectedIbs, false, true) ?>;
    const allIbs = <?= CUtil::PhpToJSObject($allIbIds, false, true) ?>;

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