<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

AddEventHandler('calendar', 'OnBeforeCalendarEventSave', 'CustomCalendar_OnBeforeCalendarEventSave');
AddEventHandler('calendar', 'OnAfterCalendarEventSave', 'CustomCalendar_OnAfterCalendarEventSave');

/**
 * Обработчик перед сохранением события календаря
 * Проверяет обязательность типа события и компании
 */
function CustomCalendar_OnBeforeCalendarEventSave(&$arFields, $arParams = [])
{
    // Получаем тип события из запроса
    $eventType = $_REQUEST['custom_event_type'] ?? '';
    
    // Если тип не указан, возвращаем ошибку
    if (empty($eventType)) {
        global $APPLICATION;
        $APPLICATION->ThrowException('Необходимо выбрать тип события (встреча/собеседование/созвон/другое)');
        return false;
    }
    
    // Если выбрана встреча, проверяем наличие компании
    if ($eventType === 'meeting' && empty($_REQUEST['custom_company_id'])) {
        global $APPLICATION;
        $APPLICATION->ThrowException('Для типа события "Встреча" необходимо выбрать компанию');
        return false;
    }
    
    // Сохраняем данные в сессию для последующего использования
    $_SESSION['custom_calendar_event_data'] = [
        'event_type' => $eventType,
        'company_id' => $eventType === 'meeting' ? (int)$_REQUEST['custom_company_id'] : 0
    ];
    
    return true;
}

/**
 * Обработчик после сохранения события календаря
 * Сохраняет дополнительные данные в свойства события
 */
function CustomCalendar_OnAfterCalendarEventSave($eventId, $arFields, $arParams = [])
{
    if (!$eventId) {
        return;
    }
    
    // Получаем сохраненные данные из сессии
    $customData = $_SESSION['custom_calendar_event_data'] ?? [];
    
    if (empty($customData)) {
        return;
    }
    
    // Сохраняем данные в описание события (в формате JSON в конце описания)
    // или можно использовать пользовательские поля если они настроены
    
    $eventType = $customData['event_type'] ?? '';
    $companyId = $customData['company_id'] ?? 0;
    
    if (!empty($eventType)) {
        // Получаем текущее событие
        $event = \Bitrix\Calendar\EventTable::getByPrimary($eventId)->fetch();
        
        if ($event) {
            $description = $event['DESCRIPTION'] ?? '';
            
            // Формируем метку с данными
            $customMarker = "\n\n[CUSTOM_DATA]\n";
            $customMarker .= "EVENT_TYPE:" . $eventType . "\n";
            if ($companyId > 0) {
                $customMarker .= "COMPANY_ID:" . $companyId . "\n";
            }
            $customMarker .= "[/CUSTOM_DATA]\n";
            
            // Удаляем старые данные если есть
            $description = preg_replace('/\n\n\[CUSTOM_DATA\].*?\[\/CUSTOM_DATA\]\n/s', '', $description);
            
            // Добавляем новые данные
            $description .= $customMarker;
            
            // Обновляем событие
            \Bitrix\Calendar\EventTable::update($eventId, [
                'DESCRIPTION' => $description
            ]);
        }
        
        // Очищаем сессию
        unset($_SESSION['custom_calendar_event_data']);
    }
}

/**
 * Функция для получения данных о типе события и компании из описания
 */
function CustomCalendar_GetEventData($description)
{
    $result = [
        'event_type' => '',
        'company_id' => 0,
        'company_name' => ''
    ];
    
    if (preg_match('/\[CUSTOM_DATA\](.*?)\[\/CUSTOM_DATA\]/s', $description, $matches)) {
        $data = $matches[1];
        
        if (preg_match('/EVENT_TYPE:(\w+)/', $data, $typeMatch)) {
            $result['event_type'] = $typeMatch[1];
        }
        
        if (preg_match('/COMPANY_ID:(\d+)/', $data, $companyMatch)) {
            $result['company_id'] = (int)$companyMatch[1];
            
            // Получаем название компании
            if ($result['company_id'] > 0 && \Bitrix\Main\Loader::includeModule('crm')) {
                $company = \Bitrix\Crm\CompanyTable::getById($result['company_id'])->fetch();
                if ($company) {
                    $result['company_name'] = $company['TITLE'];
                }
            }
        }
    }
    
    return $result;
}
