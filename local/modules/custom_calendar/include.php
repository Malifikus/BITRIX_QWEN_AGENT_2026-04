<?php
/**
 * @file include.php
 * Основной файл модуля custom_calendar
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class custom_calendar extends CModule
{
    var $MODULE_ID = "custom_calendar";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_GROUP_RIGHTS = "Y";
    var $PARTNER_NAME;
    var $PARTNER_URI;
    var $errors = [];

    public function __construct()
    {
        $arModuleVersion = [];
        include(__DIR__ . "/version.php");

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = Loc::getMessage("CUSTOM_CALENDAR_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("CUSTOM_CALENDAR_MODULE_DESC");
        $this->PARTNER_NAME = Loc::getMessage("CUSTOM_CALENDAR_PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage("CUSTOM_CALENDAR_PARTNER_URI");

        $this->eventHandlers = [
            "calendar" => [
                "OnBeforeCalendarEventAdd" => ["\\Custom\\Calendar\\EventHandler", "onBeforeCalendarEventAdd"],
                "OnAfterCalendarEventAdd" => ["\\Custom\\Calendar\\EventHandler", "onAfterCalendarEventAdd"],
                "OnBeforeCalendarEventUpdate" => ["\\Custom\\Calendar\\EventHandler", "onBeforeCalendarEventUpdate"],
            ]
        ];
    }

    public function DoInstall()
    {
        global $APPLICATION;
        
        $this->InstallFiles();
        $this->InstallComponents();
        $this->RegisterModule();
        $this->InstallEvents();
        
        return true;
    }

    public function DoUninstall()
    {
        global $APPLICATION;
        
        $this->UnInstallEvents();
        $this->UnRegisterModule();
        $this->UnInstallComponents();
        $this->UnInstallFiles();
        
        return true;
    }

    public function InstallEvents()
    {
        foreach ($this->eventHandlers as $eventModule => $handlers) {
            foreach ($handlers as $eventName => $callback) {
                \Bitrix\Main\EventManager::getInstance()->registerEventHandler(
                    $eventModule,
                    $eventName,
                    $this->MODULE_ID,
                    $callback[0],
                    $callback[1]
                );
            }
        }
        
        return true;
    }

    public function UnInstallEvents()
    {
        foreach ($this->eventHandlers as $eventModule => $handlers) {
            foreach ($handlers as $eventName => $callback) {
                \Bitrix\Main\EventManager::getInstance()->unRegisterEventHandler(
                    $eventModule,
                    $eventName,
                    $this->MODULE_ID,
                    $callback[0],
                    $callback[1]
                );
            }
        }
        
        return true;
    }

    public function InstallFiles($arParams = [])
    {
        // Копирование JS файлов
        if ($dir = opendir(__DIR__ . "/install/js")) {
            while (false !== ($item = readdir($dir))) {
                if (in_array($item, [".", ".."])) {
                    continue;
                }
                
                $source = __DIR__ . "/install/js/" . $item;
                $target = $_SERVER["DOCUMENT_ROOT"] . "/local/js/calendar/" . $item;
                
                if (is_dir($source)) {
                    CopyDirFiles($source, $target, true, true);
                } else {
                    CopyFileWithMerge($source, $target);
                }
            }
            closedir($dir);
        }
        
        return true;
    }

    public function UnInstallFiles($arParams = [])
    {
        // Удаление JS файлов
        $jsPath = $_SERVER["DOCUMENT_ROOT"] . "/local/js/calendar/";
        if (file_exists($jsPath)) {
            DeleteDirFilesEx("/local/js/calendar/");
        }
        
        return true;
    }

    public function InstallComponents($arParams = [])
    {
        // Установка компонентов
        if ($dir = opendir(__DIR__ . "/install/components/custom")) {
            while (false !== ($item = readdir($dir))) {
                if (in_array($item, [".", ".."])) {
                    continue;
                }
                
                $source = __DIR__ . "/install/components/custom/" . $item;
                $target = $_SERVER["DOCUMENT_ROOT"] . "/local/components/custom/" . $item;
                
                if (is_dir($source)) {
                    CopyDirFiles($source, $target, true, true);
                }
            }
            closedir($dir);
        }
        
        return true;
    }

    public function UnInstallComponents($arParams = [])
    {
        // Удаление компонентов
        DeleteDirFilesEx("/local/components/custom/calendar.event.edit/");
        
        return true;
    }

    public function GetModuleRightList()
    {
        $arr = [
            "reference_id" => ["D", "K", "F", "W"],
            "reference" => [
                "[D] ".Loc::getMessage("CUSTOM_CALENDAR_RIGHT_D"),
                "[K] ".Loc::getMessage("CUSTOM_CALENDAR_RIGHT_K"),
                "[F] ".Loc::getMessage("CUSTOM_CALENDAR_RIGHT_F"),
                "[W] ".Loc::getMessage("CUSTOM_CALENDAR_RIGHT_W"),
            ],
        ];
        return $arr;
    }
}
