<?php

namespace Sl3w\Watermark;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class OtherModules
{
    public static function getOtherModulesList()
    {
        if (!function_exists('curl_version')) return [];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, 'https://sl3w.ru/modules.json');
        $result = curl_exec($curl);
        curl_close($curl);

        $jsonArray = json_decode($result, true);

        foreach ($jsonArray as $moduleId => &$moduleInfo) {
            if ($moduleId === Settings::MODULE_ID) {
                unset($jsonArray[$moduleId]);
            }

            $moduleInfo['id'] = $moduleId;
            $moduleInfo['marketplace_link'] ??= 'https://marketplace.1c-bitrix.ru/solutions/' . $moduleId . '/';
            $moduleInfo['install_link'] ??= '/bitrix/admin/update_system_partner.php?addmodule=' . $moduleId;
        }

        return $jsonArray;
    }

    public static function getOtherModulesHtml()
    {
        $modulesList = OtherModules::getOtherModulesList();
        $modulesListSep = array_chunk($modulesList, 2);

        ob_start();
        ?>

        <?php foreach ($modulesListSep as $modulesListRow): ?>
            <tr class="sl3w-option-modules-row">
                <?php foreach ($modulesListRow as $modulesItem): ?>
                    <td width="50%">
                        <div class="sl3w-option-modules-item">
                            <img src="<?= $modulesItem['image'] ?>">

                            <div class="sl3w-option-modules-item__info">
                                <p><?= $modulesItem['title'] ?></p>
                                <span><?= $modulesItem['description'] ?></span>

                                <div class="sl3w-option-modules-item__links">
                                    <?php if ($installLink = $modulesItem['install_link']): ?>
                                        <a href="<?= $installLink ?>" target="_blank">
                                            <?= Loc::getMessage('SL3W_OTHER_MODULES_INSTALL_LINK') ?>
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($marketPlaceLink = $modulesItem['marketplace_link']): ?>
                                        <a href="<?= $marketPlaceLink ?>" target="_blank">
                                            <?= Loc::getMessage('SL3W_OTHER_MODULES_MARKETPLACE_LINK') ?>

                                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="18" height="18" viewBox="0 0 30 30">
                                                <path d="M 25.980469 2.9902344 A 1.0001 1.0001 0 0 0 25.869141 3 L 20 3 A 1.0001 1.0001 0 1 0 20 5 L 23.585938 5 L 13.292969 15.292969 A 1.0001 1.0001 0 1 0 14.707031 16.707031 L 25 6.4140625 L 25 10 A 1.0001 1.0001 0 1 0 27 10 L 27 4.1269531 A 1.0001 1.0001 0 0 0 25.980469 2.9902344 z M 6 7 C 4.9069372 7 4 7.9069372 4 9 L 4 24 C 4 25.093063 4.9069372 26 6 26 L 21 26 C 22.093063 26 23 25.093063 23 24 L 23 14 L 23 11.421875 L 21 13.421875 L 21 16 L 21 24 L 6 24 L 6 9 L 14 9 L 16 9 L 16.578125 9 L 18.578125 7 L 16 7 L 14 7 L 6 7 z"></path>
                                            </svg>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach;

        return ob_get_clean();
    }
}