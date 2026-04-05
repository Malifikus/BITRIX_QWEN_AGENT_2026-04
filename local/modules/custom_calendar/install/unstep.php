<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
use Bitrix\Main\Loader;

Loader::includeModule('custom_calendar');

$step = intval($_REQUEST["step"]);
?>
<h2><?=GetMessage("CUSTOM_CALENDAR_UNINSTALL_TITLE")?></h2>

<?
if ($step >= 2) {
    $savedata = ($_REQUEST["savedata"] == "Y");
    // Удаление модуля вызывается из админки до отображения этого файла
    ?>
    <p>Модуль <?=GetMessage("CUSTOM_CALENDAR_MODULE_NAME")?> удален.</p>
    <p>Все обработчики событий отключены, файлы компонента и JavaScript удалены.</p>
    <?
    if ($savedata) {
        ?><p>Данные в базе данных (описания событий) сохранены.</p><?
    } else {
        ?><p>Примечание: Данные в описаниях событий были очищены.</p><?
    }
    ?>
    <p><a href="/bitrix/admin/partner_modules.php?lang=<?=LANGUAGE_ID?>">Вернуться к списку модулей</a></p>
    <?
} elseif ($step == 1) {
    ?>
    <p>Вы уверены, что хотите удалить модуль?</p>
    <form action="<?=$APPLICATION->GetCurPage()?>">
        <?=bitrix_sessid_post()?>
        <input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
        <input type="hidden" name="id" value="custom_calendar">
        <input type="hidden" name="uninstall" value="Y">
        <input type="hidden" name="step" value="2">
        <p><label><input type="checkbox" name="savedata" value="Y"> Сохранить данные в базе данных (описания событий)</label></p>
        <input type="submit" value="Удалить">
    </form>
    <?
} else {
    ?>
    <p>Модуль будет удален из системы.</p>
    <form action="<?=$APPLICATION->GetCurPage()?>">
        <?=bitrix_sessid_post()?>
        <input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
        <input type="hidden" name="id" value="custom_calendar">
        <input type="hidden" name="uninstall" value="Y">
        <input type="hidden" name="step" value="1">
        <input type="submit" value="Продолжить удаление">
    </form>
    <?
}
?>
