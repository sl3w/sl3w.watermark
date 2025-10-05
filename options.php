<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Sl3w\Watermark\Helpers;
use Sl3w\Watermark\OptionsDrawer;
use Sl3w\Watermark\OtherModules;
use Sl3w\Watermark\Settings;
use Sl3w\Watermark\EventsRegister;

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

//check module version updated
Settings::checkModuleVersionUpdated();

sl3w_asset()->addJs('/bitrix/js/' . $moduleId . '/options.min.js');
$APPLICATION->SetAdditionalCss('/bitrix/css/' . $moduleId . '/options.min.css');

//version 1.2.1 - change file path to file id
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
//\version 1.2.1 - change file path to file id

$selectIBlocks = [0 => Loc::getMessage(LANGS_PREFIX . 'OPTION_EMPTY')];

$dbIBlocks = CIBlock::GetList(['ID' => 'ASC'], ['ACTIVE' => 'Y']);

$allIBlocks = [];

while ($arIBlock = $dbIBlocks->GetNext()) {
    $allIBlocks[$arIBlock['ID']] = $arIBlock['NAME'];
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

$settingsTabOptions = [
    'instruction_note' => [
        'type' => 'note',
        'name' => Loc::getMessage(LANGS_PREFIX . 'INSTRUCTION_NOTE'),
    ],
    'main_block' => [
        'type' => 'block_title',
        'name' => Loc::getMessage(LANGS_PREFIX . 'BLOCK_MAIN'),
    ],
    'switch_on' => [
        'code' => 'switch_on',
        'name' => Loc::getMessage(LANGS_PREFIX . 'OPTION_SWITCH_ON'),
        'type' => 'checkbox',
        'default' => 'N',
    ],
    'add_watermark_btn_mass_switch_on' => [
        'code' => 'add_watermark_btn_mass_switch_on',
        'name' => Loc::getMessage(LANGS_PREFIX . 'OPTION_ADD_WATERMARK_BTN_MASS'),
        'type' => 'checkbox',
        'default' => 'N',
    ],
    'add_watermark_btn_switch_on' => [
        'code' => 'add_watermark_btn_switch_on',
        'name' => Loc::getMessage(LANGS_PREFIX . 'OPTION_ADD_WATERMARK_BTN'),
        'type' => 'checkbox',
        'default' => 'N',
    ],
    'add_watermark_btn_section_switch_on' => [
        'code' => 'add_watermark_btn_section_switch_on',
        'name' => Loc::getMessage(LANGS_PREFIX . 'OPTION_ADD_WATERMARK_BTN_SECTION'),
        'type' => 'checkbox',
        'default' => 'N',
    ],
    'events_block' => [
        'type' => 'block_title',
        'name' => Loc::getMessage(LANGS_PREFIX . 'BLOCK_EVENTS'),
    ],
    'event_add_switch_on' => [
        'code' => 'event_add_switch_on',
        'name' => Loc::getMessage(LANGS_PREFIX . 'OPTION_EVENT_ADD_SWITCH_ON'),
        'type' => 'checkbox',
        'default' => 'Y',
    ],
    'event_update_switch_on' => [
        'code' => 'event_update_switch_on',
        'name' => Loc::getMessage(LANGS_PREFIX . 'OPTION_EVENT_UPDATE_SWITCH_ON'),
        'type' => 'checkbox',
        'default' => 'Y',
    ],
    'process_sku' => [
        'code' => 'process_sku',
        'name' => Loc::getMessage(LANGS_PREFIX . 'OPTION_PROCESS_SKU'),
        'type' => 'checkbox',
        'default' => 'N',
        'hint_after' => Loc::getMessage(LANGS_PREFIX . 'OPTION_PROCESS_SKU_HINT_AFTER'),
    ],
    'wm_block_image' => [
        'type' => 'block_title',
        'name' => Loc::getMessage(LANGS_PREFIX . 'BLOCK_WM_IMAGE'),
    ],
    'switch_on_image' => [
        'code' => 'switch_on_image',
        'name' => Loc::getMessage(LANGS_PREFIX . 'OPTION_SWITCH_ON_IMAGE'),
        'type' => 'checkbox',
        'default' => 'Y',
    ],
    'wm_position' => [
        'code' => 'wm_position',
        'name' => Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_POSITION'),
        'type' => 'select',
        'options' => $selectPositions,
        'default' => 'br',
    ],
    'wm_is_repeat' => [
        'code' => 'wm_is_repeat',
        'name' => Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_IS_REPEAT'),
        'type' => 'checkbox',
        'default' => 'N',
    ],
    'wm_alpha' => [
        'code' => 'wm_alpha',
        'name' => Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_ALPHA'),
        'type' => 'number',
        'default' => 50,
        'size' => 3,
        'min_value' => 0,
        'max_value' => 100,
        'text_after' => Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_ALPHA_AFTER'),
        'hint_after' => Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_ALPHA_HINT_AFTER'),
    ],
    'wm_max_percent' => [
        'code' => 'wm_max_percent',
        'name' => Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_MAX_PERCENT'),
        'type' => 'number',
        'default' => 50,
        'size' => 3,
        'min_value' => 0,
        'max_value' => 100,
        'text_after' => Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_MAX_PERCENT_AFTER'),
        'hint_after' => Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_MAX_PERCENT_HINT_AFTER'),
    ],
    'wm_image_path' => [
        'code' => 'wm_image_path',
        'name' => Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_IMAGE_PATH'),
        'type' => 'image',
    ],
    'wm_block_text' => [
        'type' => 'block_title',
        'name' => Loc::getMessage(LANGS_PREFIX . 'BLOCK_WM_TEXT'),
    ],
    'switch_on_text' => [
        'code' => 'switch_on_text',
        'name' => Loc::getMessage(LANGS_PREFIX . 'OPTION_SWITCH_ON_TEXT'),
        'type' => 'checkbox',
        'default' => 'Y',
    ],
    'wm_position_text' => [
        'code' => 'wm_position_text',
        'name' => Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_POSITION'),
        'type' => 'select',
        'options' => $selectPositions,
        'default' => 'br',
    ],
    'wm_text' => [
        'code' => 'wm_text',
        'name' => Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_TEXT'),
        'type' => 'text',
        'default' => '',
        'size' => 50,
        'max_length' => 255,
    ],
    'wm_text_color' => [
        'code' => 'wm_text_color',
        'name' => Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_TEXT_COLOR'),
        'type' => 'color',
        'default' => 'ffffff',
    ],
    'wm_max_percent_text' => [
        'code' => 'wm_max_percent_text',
        'name' => Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_MAX_PERCENT'),
        'type' => 'number',
        'default' => 50,
        'size' => 3,
        'min_value' => 0,
        'max_value' => 100,
        'text_after' => Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_MAX_PERCENT_AFTER'),
        'hint_after' => Loc::getMessage(LANGS_PREFIX . 'OPTION_WM_MAX_PERCENT_HINT_AFTER'),
    ],
    'wm_text_font' => [
        'code' => 'wm_text_font',
        'name' => Loc::getMessage('SL3W_WATERMARK_OPTION_WM_TEXT_FONT'),
        'type' => 'file',
        'size' => 50,
        'default' => '/bitrix/fonts/pt_sans-regular.ttf',
        'file_filter' => 'ttf',
    ],
    'iblock_block' => [
        'type' => 'block_title',
        'name' => Loc::getMessage(LANGS_PREFIX . 'BLOCK_IBLOCK'),
    ],
    'iblock_ids' => [
        'code' => 'iblock_ids',
        'name' => Loc::getMessage(LANGS_PREFIX . 'OPTION_IBLOCK_IDS'),
        'type' => 'select',
        'multi' => 'Y',
        'options' => $selectIBlocks,
        'default' => '',
    ],
];

$selectedIbs = Settings::getProcessingIBlocks();
$iBlockIds = array_keys($allIBlocks);
$propCodeToSaveOriginals = Settings::getPropCodeToSaveOriginals();

foreach ($allIBlocks as $iBlockId => $iBlockName) {
    if (!intval($iBlockId)) {
        continue;
    }

    $selectFieldsAndProps = $selectFields;

    $propsRes = CIBlock::GetProperties($iBlockId);

    while ($prop = $propsRes->Fetch()) {
        if ($prop['PROPERTY_TYPE'] == 'F' && $prop['CODE'] != $propCodeToSaveOriginals) {
            $selectFieldsAndProps['PROPERTY_' . $prop['CODE']] = sprintf('[PROPERTY_%s] %s', $prop['CODE'], $prop['NAME']);
        }
    }

    $settingsTabOptions['iblock' . $iBlockId . '_fields'] = [
        'code' => 'iblock' . $iBlockId . '_fields',
        'name' => Loc::getMessage(LANGS_PREFIX . 'FIELDS_AND_PROPS', ['#ID#' => $iBlockId, '#NAME#' => $iBlockName]),
        'type' => 'select',
        'multi' => 'Y',
        'options' => $selectFieldsAndProps,
        'default' => '',
    ];
}

$settingsTabOptions = array_merge($settingsTabOptions, [
    'ctrl_info' => [
        'type' => 'note',
        'name' => Loc::getMessage(LANGS_PREFIX . 'CTRL_INFO'),
    ],
    'save_originals_block' => [
        'type' => 'block_title',
        'name' => Loc::getMessage(LANGS_PREFIX . 'BLOCK_SAVE_ORIGINALS'),
    ],
    'save_originals' => [
        'code' => 'save_originals',
        'name' => Loc::getMessage(LANGS_PREFIX . 'OPTION_SAVE_ORIGINALS'),
        'type' => 'checkbox',
        'default' => 'N',
    ],
    'save_originals_prop_code' => [
        'code' => 'save_originals_prop_code',
        'name' => Loc::getMessage(LANGS_PREFIX . 'OPTION_SAVE_ORIGINALS_PROP_CODE'),
        'type' => 'text',
        'default' => 'WM_ORIGINAL_IMAGES',
        'size' => 30,
        'max_length' => 255,
    ],
    'save_originals_note' => [
        'type' => 'note',
        'name' => Loc::getMessage(LANGS_PREFIX . 'SAVE_ORIGINALS_NOTE'),
    ],
    'exclude_block' => [
        'type' => 'block_title',
        'name' => Loc::getMessage(LANGS_PREFIX . 'BLOCK_EXCLUDE'),
    ],
    'exclude_elements_ids' => [
        'code' => 'exclude_elements_ids',
        'name' => Loc::getMessage(LANGS_PREFIX . 'OPTION_EXCLUDE_ELEMENTS'),
        'type' => 'textarea',
        'default' => '',
        'rows' => 5,
        'cols' => 50,
    ],
    'include_file_types' => [
        'code' => 'include_file_types',
        'name' => Loc::getMessage(LANGS_PREFIX . 'OPTION_INCLUDE_FILE_TYPES'),
        'type' => 'textarea',
        'default' => '',
        'rows' => 5,
        'cols' => 50,
        'hint_after' => Loc::getMessage(LANGS_PREFIX . 'OPTION_INCLUDE_FILE_TYPES_HINT_AFTER'),
    ],
    'dont_add_block' => [
        'type' => 'block_title',
        'name' => Loc::getMessage(LANGS_PREFIX . 'BLOCK_SET_DONT_ADD'),
    ],
    'set_dont_add_after_add' => [
        'code' => 'set_dont_add_after_add',
        'name' => Loc::getMessage(LANGS_PREFIX . 'OPTION_SET_DONT_ADD_AFTER_ADD'),
        'type' => 'checkbox',
        'default' => 'N',
    ],
    'set_dont_add_after_update' => [
        'code' => 'set_dont_add_after_update',
        'name' => Loc::getMessage(LANGS_PREFIX . 'OPTION_SET_DONT_ADD_AFTER_UPDATE'),
        'type' => 'checkbox',
        'default' => 'N',
    ],
    'dont_add_note' => [
        'type' => 'note',
        'name' => Loc::getMessage(LANGS_PREFIX . 'SET_DONT_ADD_NOTE'),
    ],
    '1c_exchange_block' => [
        'type' => 'block_title',
        'name' => Loc::getMessage(LANGS_PREFIX . 'BLOCK_1C_EXCHANGE'),
    ],
    'switch_on_1c_pending_exec' => [
        'code' => 'switch_on_1c_pending_exec',
        'name' => Loc::getMessage(LANGS_PREFIX . 'OPTION_1C_PENDING_EXEC'),
        'type' => 'checkbox',
        'default' => 'N',
    ],
    'switch_on_1c_pending_exec_note' => [
        'type' => 'note',
        'name' => Loc::getMessage(LANGS_PREFIX . '1C_PENDING_EXEC_NOTE'),
    ],
    'exchange_1c_user_id' => [
        'code' => 'exchange_1c_user_id',
        'name' => Loc::getMessage(LANGS_PREFIX . 'OPTION_1C_EXCHANGE_USER_ID'),
        'type' => 'number',
        'default' => 1,
        'min_value' => 1,
    ],
    'exchange_1c_user_id_note' => [
        'type' => 'note',
        'name' => Loc::getMessage(LANGS_PREFIX . '1C_EXCHANGE_USER_ID_NOTE'),
    ],
]);

$modulesTabOptions = $supportTabOptions = [
    'support_note' => [
        'type' => 'note',
        'name' => Loc::getMessage(LANGS_PREFIX . 'SUPPORT_NOTE'),
    ],
];

$allTabsOptions = array_merge($settingsTabOptions, $modulesTabOptions, $supportTabOptions);

$tabControl = new CAdminTabControl(
    'tabControl',
    [
        [
            'DIV' => 'settings',
            'TAB' => Loc::getMessage(LANGS_PREFIX . 'OPTIONS_TAB_NAME'),
            'TITLE' => Loc::getMessage(LANGS_PREFIX . 'OPTIONS_TAB_TITLE'),
        ],
        [
            'DIV' => 'modules',
            'TAB' => Loc::getMessage(LANGS_PREFIX . 'MODULES_TAB_NAME'),
            'TITLE' => Loc::getMessage(LANGS_PREFIX . 'MODULES_TAB_TITLE'),
        ],
        [
            'DIV' => 'support',
            'TAB' => Loc::getMessage(LANGS_PREFIX . 'SUPPORT_TAB_NAME'),
            'TITLE' => Loc::getMessage(LANGS_PREFIX . 'SUPPORT_TAB_TITLE'),
        ],
    ],
);

$tabControl->Begin();

$optionsDrawer = new OptionsDrawer('.sl3w_watermark');
?>

<form enctype="multipart/form-data" method="post" name="sl3w_watermark"
      action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= $moduleId ?>&lang=<?= LANG ?>">

    <?php
    $tabControl->BeginNextTab();

    $optionsDrawer->drawOptions($settingsTabOptions);

    $tabControl->BeginNextTab();

    echo OtherModules::getOtherModulesHtml();

    $optionsDrawer->drawOptions($modulesTabOptions);

    $tabControl->BeginNextTab();
    ?>

    <p>
        <?= Loc::getMessage(LANGS_PREFIX . 'SUPPORT_TAB_TEXT') ?>
    </p>
    <p>
        <?= Loc::getMessage(LANGS_PREFIX . 'SUPPORT_TAB_TEXT2') ?>
    </p>
    <p>
        <?= Loc::getMessage(LANGS_PREFIX . 'SUPPORT_TAB_TEXT2_QR') ?>
    </p>
    <p>
        <?= Loc::getMessage(LANGS_PREFIX . 'SUPPORT_TAB_TEXT3') ?>
    </p>
    <p>
        <?= Loc::getMessage(LANGS_PREFIX . 'SUPPORT_TAB_TEXT4') ?>
    </p>

    <iframe
            src="https://yoomoney.ru/quickpay/shop-widget?writer=seller&default-sum=1000&button-text=12&payment-type-choice=on&successURL=&quickpay=shop&account=410014134044507&targets=%D0%9F%D0%B5%D1%80%D0%B5%D0%B2%D0%BE%D0%B4%20%D0%BF%D0%BE%20%D0%BA%D0%BD%D0%BE%D0%BF%D0%BA%D0%B5&"
            width="423" height="222" frameborder="0" allowtransparency="true" scrolling="no"></iframe>

    <p>
        <?= Loc::getMessage(LANGS_PREFIX . 'SUPPORT_TAB_TEXT5') ?>
    </p>
    <p>
        <?= Loc::getMessage(LANGS_PREFIX . 'SUPPORT_TAB_TEXT6') ?>
    </p>

    <?php
    $optionsDrawer->drawOptions($supportTabOptions);

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

    foreach ($allTabsOptions as $option) {
        $type = $option['type'];
        $code = $option['code'];

        if (in_array($type, ['note', 'block_title'])) {
            continue;
        }

        if ($request['apply']) {
            $value = $request->getPost($code);
            $value = $type == 'checkbox' && $value == '' ? 'N' : $value;

            switch ($code) {
                case 'switch_on':
                    EventsRegister::elementsUpdate($value == 'Y');

                    break;

                case 'wm_alpha':
                case 'wm_max_percent':
                case 'wm_max_percent_text':
                case 'wm_text':
                case 'wm_text_font':
                    $value = !$value ? $option['default'] : $value;

                    break;

                case 'wm_text_color':
                    $value = Helpers::clearColorHex(!$value ? $option['default'] : $value);

                    break;

                case 'iblock_ids':
                    foreach ($value as $ibId) {
                        $optionName = 'iblock' . $ibId . '_fields';
                        $optionValueFields = $request->getPost($optionName);

                        Settings::set($optionName, is_array($optionValueFields) ? implode(',', $optionValueFields) : $optionValueFields);
                    }

                    break;

                case 'add_watermark_btn_switch_on':
                    EventsRegister::addWatermarkBtnEvents($value == 'Y' && $request->getPost('switch_on'));

                    break;

                case 'add_watermark_btn_section_switch_on':
                    EventsRegister::addWatermarkBtnSectionEvents($value == 'Y' && $request->getPost('switch_on'));

                    break;

                case 'add_watermark_btn_mass_switch_on':
                    EventsRegister::addWatermarkMassEvents($value == 'Y' && $request->getPost('switch_on'));

                    break;

                case 'wm_image_path':
                    $currentValue = Settings::get($code, 0);

                    $filesByOption = $_FILES[$code];

                    //в $value (в $_REQUEST) будет путь к файлу, если файл залит через медиабиблиотеку или структуру,
                    //но нам в конечном итоге нужен ID файла

                    //если файл удален или залит новый, удаляем старый файл
                    if (
                        $request->getPost($code . '_del') || $value ||
                        (isset($filesByOption) && strlen($filesByOption['tmp_name']))
                    ) {
                        CFile::Delete($currentValue);
                    }

                    if ((isset($filesByOption) && strlen($filesByOption['tmp_name'])) || $value) {
                        $value = 0;

                        if (isset($filesByOption)) {
                            $absFilePath = $filesByOption['tmp_name'];
                            $arOriginalName = $filesByOption['name'];
                        } else {
                            $filePath = $_REQUEST[$code];
                            $absFilePath = $_SERVER['DOCUMENT_ROOT'] . htmlspecialcharsbx($filePath);
                            $filePath = explode('/', $filePath);
                            $arOriginalName = array_pop($filePath);
                        }

                        if (file_exists($absFilePath)) {
                            $arFile = CFile::MakeFileArray($absFilePath);
                            $arFile['name'] = $arOriginalName;

                            if ($fileId = CFile::SaveFile($arFile, str_replace('.', '_', $moduleId))) {
                                $value = $fileId;
                            }
                        }
                    } elseif ($request->getPost($code . '_del')) {
                        $value = '';
                    } else {
                        $value = $currentValue;
                    }

                    break;
            }

            $value = is_array($value) ? implode(',', $value) : $value;

            Settings::set($code, $value);
        } elseif ($request['default']) {
            Settings::set($code, $option['default']);

            switch ($code) {
                case 'switch_on':
                    EventsRegister::elementsUpdate(false);

                    break;

                case 'add_watermark_btn_switch_on':
                    EventsRegister::addWatermarkBtnEvents(false);

                    break;

                case 'add_watermark_btn_section_switch_on':
                    EventsRegister::addWatermarkBtnSectionEvents(false);

                    break;

                case 'add_watermark_btn_mass_switch_on':
                    EventsRegister::addWatermarkMassEvents(false);

                    break;
            }
        }
    }

    LocalRedirect($APPLICATION->GetCurPage() . '?mid=' . $moduleId . '&lang=' . urlencode(LANGUAGE_ID) . '&mid_menu=1');
}

$optionsDrawer->drawExtensions();
?>

<script>
    const selectedIbs = <?= CUtil::PhpToJSObject($selectedIbs, false, true) ?>;
    const allIbs = <?= CUtil::PhpToJSObject($iBlockIds, false, true) ?>;

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