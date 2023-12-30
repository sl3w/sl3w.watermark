<?php

use Bitrix\Main\Application;
use Bitrix\Main\EventManager;
use Bitrix\Main\Page\Asset;

if (!function_exists('sl3w_application')) {
    /**
     * @return CMain
     */
    function sl3w_application(): CMain
    {
        global $APPLICATION;

        return $APPLICATION;
    }
}

if (!function_exists('sl3w_request')) {
    /**
     * @return \Bitrix\Main\HttpRequest|\Bitrix\Main\Request
     */
    function sl3w_request()
    {
        return Application::getInstance()->getContext()->getRequest();
    }
}

if (!function_exists('sl3w_event_manager')) {
    /**
     * @return EventManager
     */
    function sl3w_event_manager(): EventManager
    {
        return EventManager::getInstance();
    }
}

if (!function_exists('sl3w_asset')) {
    /**
     * @return Asset
     */
    function sl3w_asset(): Asset
    {
        return Asset::getInstance();
    }
}