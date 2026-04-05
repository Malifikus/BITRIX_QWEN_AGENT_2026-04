<?php
/**
 * @file unstep.php
 * Шаг мастера удаления модуля
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);

$step = intval($_REQUEST["step"] ?? 0);
?>

<h2><?=Loc::getMessage("CUSTOM_CALENDAR_UNINSTALL_TITLE")?></h2>

<?php
if ($step >= 2) {
    // Удаление уже выполнено через DoUninstall() в include.php
    ?>
    <div style="background: #fff3cd; padding: 15px; border-radius: 4px; margin: 15px 0;">
        <p style="margin: 0; color: #856404; font-weight: bold;">
            ⚠ Модуль <?=Loc::getMessage("CUSTOM_CALENDAR_MODULE_NAME")?> удален.
        </p>
    </div>
    
    <p><b>Выполненные действия:</b></p>
    <ul>
        <li>Обработчики событий календаря отключены</li>
        <li>Файлы компонента удалены из /local/components/custom/calendar.event.edit/</li>
        <li>JavaScript файлы удалены из /local/js/calendar/</li>
        <li>Модуль разрегистрирован из системы</li>
    </ul>
    
    <?php
    $savedata = ($_REQUEST["savedata"] ?? "") === "Y";
    if ($savedata) {
        ?>
        <p style="color: #2e8b57;">✓ Данные в описаниях событий календаря сохранены.</p>
        <?php
    } else {
        ?>
        <p style="color: #dc3545;">⚠ Данные в описаниях событий (JSON-маркеры) были удалены.</p>
        <?php
    }
    ?>
    
    <p style="margin-top: 20px;">
        <a href="/bitrix/admin/partner_modules.php?lang=<?=LANGUAGE_ID?>" class="adm-btn adm-btn-save">
            ← Вернуться к списку модулей
        </a>
    </p>
    <?php
} elseif ($step == 1) {
    ?>
    <div style="background: #f8d7da; padding: 15px; border-radius: 4px; margin: 15px 0;">
        <p style="margin: 0; color: #721c24; font-weight: bold;">
            ⚠ Вы уверены, что хотите удалить модуль?
        </p>
    </div>
    
    <p>После удаления:</p>
    <ul>
        <li>Исчезнет обязательный выбор типа события</li>
        <li>Поле выбора компании больше не будет отображаться</li>
        <li>Обработчики событий будут отключены</li>
    </ul>
    
    <form action="<?=$APPLICATION->GetCurPage()?>" method="post">
        <?=bitrix_sessid_post()?>
        <input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
        <input type="hidden" name="id" value="custom_calendar">
        <input type="hidden" name="uninstall" value="Y">
        <input type="hidden" name="step" value="2">
        
        <div style="margin: 20px 0; padding: 15px; background: #f9f9f9; border-radius: 4px;">
            <label style="display: flex; align-items: center; cursor: pointer;">
                <input type="checkbox" name="savedata" value="Y" style="margin-right: 10px; width: auto;">
                <span>Сохранить данные в описаниях событий календаря (JSON-маркеры останутся)</span>
            </label>
        </div>
        
        <div style="margin-top: 20px;">
            <button type="submit" class="adm-btn adm-btn-delete">Удалить модуль</button>
            <a href="/bitrix/admin/partner_modules.php?lang=<?=LANGUAGE_ID?>" class="adm-btn">Отмена</a>
        </div>
    </form>
    <?php
} else {
    ?>
    <p>Модуль <b><?=Loc::getMessage("CUSTOM_CALENDAR_MODULE_NAME")?></b> будет полностью удален из системы.</p>
    
    <p>На следующем шаге вы сможете выбрать опцию сохранения данных в описаниях событий календаря.</p>
    
    <form action="<?=$APPLICATION->GetCurPage()?>" method="post">
        <?=bitrix_sessid_post()?>
        <input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
        <input type="hidden" name="id" value="custom_calendar">
        <input type="hidden" name="uninstall" value="Y">
        <input type="hidden" name="step" value="1">
        
        <div style="margin-top: 20px;">
            <button type="submit" class="adm-btn adm-btn-delete">Продолжить удаление</button>
            <a href="/bitrix/admin/partner_modules.php?lang=<?=LANGUAGE_ID?>" class="adm-btn">Отмена</a>
        </div>
    </form>
    <?php
}
?>
