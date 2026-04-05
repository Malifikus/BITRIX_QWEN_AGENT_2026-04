<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

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

    public function __construct()
    {
        $arModuleVersion = [];
        include(__DIR__ . "/version.php");

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = GetMessage("CUSTOM_CALENDAR_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("CUSTOM_CALENDAR_MODULE_DESC");
        $this->PARTNER_NAME = GetMessage("CUSTOM_CALENDAR_PARTNER_NAME");
        $this->PARTNER_URI = GetMessage("CUSTOM_CALENDAR_PARTNER_URI");

        $this->GetPath();
    }

    public function GetPath($notDocumentRoot = false)
    {
        if ($notDocumentRoot) {
            return str_ireplace($_SERVER["DOCUMENT_ROOT"], "", __DIR__);
        } else {
            return __DIR__;
        }
    }

    public function DoInstall()
    {
        global $APPLICATION;
        $this->InstallFiles();
        $this->InstallDB();
        $APPLICATION->IncludeAdminFile(GetMessage("CUSTOM_CALENDAR_INSTALL_TITLE"), $this->GetPath() . "/install/step.php");
    }

    public function DoUninstall()
    {
        global $APPLICATION;
        $this->UnInstallFiles();
        $this->UnInstallDB();
        $APPLICATION->IncludeAdminFile(GetMessage("CUSTOM_CALENDAR_UNINSTALL_TITLE"), $this->GetPath() . "/install/unstep.php");
    }

    public function InstallFiles()
    {
        CopyDirFiles(__DIR__ . "/install/components", $_SERVER["DOCUMENT_ROOT"] . "/local/components", true, true);
        CopyDirFiles(__DIR__ . "/install/js", $_SERVER["DOCUMENT_ROOT"] . "/local/js", true, true);
        return true;
    }

    public function UnInstallFiles()
    {
        DeleteDirFilesEx("/local/components/custom/calendar.event.edit");
        DeleteDirFilesEx("/local/js/custom_calendar");
        return true;
    }

    public function InstallDB()
    {
        RegisterModule($this->MODULE_ID);
        \Bitrix\Main\EventManager::getInstance()->registerEventHandler(
            'calendar',
            'OnBeforeCalendarEventSave',
            $this->MODULE_ID,
            '\Custom\Calendar\EventHandler',
            'onBeforeCalendarEventSave'
        );
        \Bitrix\Main\EventManager::getInstance()->registerEventHandler(
            'calendar',
            'OnAfterCalendarEventSave',
            $this->MODULE_ID,
            '\Custom\Calendar\EventHandler',
            'onAfterCalendarEventSave'
        );
        return true;
    }

    public function UnInstallDB()
    {
        UnRegisterModule($this->MODULE_ID);
        \Bitrix\Main\EventManager::getInstance()->unRegisterEventHandler(
            'calendar',
            'OnBeforeCalendarEventSave',
            $this->MODULE_ID,
            '\Custom\Calendar\EventHandler',
            'onBeforeCalendarEventSave'
        );
        \Bitrix\Main\EventManager::getInstance()->unRegisterEventHandler(
            'calendar',
            'OnAfterCalendarEventSave',
            $this->MODULE_ID,
            '\Custom\Calendar\EventHandler',
            'onAfterCalendarEventSave'
        );
        return true;
    }
}
