<?php
/**
 * @file step.php
 * Шаг мастера установки модуля
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
?>

<h2><?=Loc::getMessage("CUSTOM_CALENDAR_INSTALL_TITLE")?></h2>
<p><?=Loc::getMessage("CUSTOM_CALENDAR_MODULE_DESC")?></p>

<div style="background: #f0f8ff; padding: 15px; border-radius: 4px; margin: 15px 0;">
    <p style="margin: 0; color: #2e8b57; font-weight: bold;">
        ✓ <?=Loc::getMessage("CUSTOM_CALENDAR_MODULE_NAME")?> успешно установлен.
    </p>
</div>

<p><b>Функционал модуля:</b></p>
<ul>
    <li>Обязательный выбор типа события: Встреча, Собеседование, Созвон, Другое</li>
    <li>При выборе типа "Встреча" — обязательный выбор компании из CRM</li>
    <li>Данные сохраняются в описании события в формате JSON</li>
    <li>Автоматическое восстановление данных при редактировании события</li>
    <li>Валидация на клиенте и сервере</li>
</ul>

<p><b>Использование:</b></p>
<ol>
    <li>Откройте календарь в Битрикс24</li>
    <li>Создайте или отредактируйте событие</li>
    <li>Выберите тип события и компанию (если требуется)</li>
    <li>Сохраните событие</li>
</ol>

<p style="color: #666; font-size: 12px;">
    Модуль готов к работе. Обработчики событий автоматически зарегистрированы.
</p>
