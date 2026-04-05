<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

$arResult = [];

// Получение списка компаний
$arResult['COMPANIES'] = $this->getCompaniesList();

// Если редактируем существующее событие, получаем сохраненные данные
if (!empty($arParams['EVENT_ID']) || !empty($_REQUEST['id'])) {
    $eventId = $arParams['EVENT_ID'] ?? $_REQUEST['id'];
    
    if (\Bitrix\Main\Loader::includeModule('calendar')) {
        $event = \Bitrix\Calendar\EventTable::getByPrimary($eventId)->fetch();
        
        if ($event && !empty($event['DESCRIPTION'])) {
            $eventData = $this->getEventDataFromDescription($event['DESCRIPTION']);
            
            if (!empty($eventData['event_type'])) {
                $arResult['EVENT_TYPE'] = $eventData['event_type'];
            }
            
            if (!empty($eventData['company_id'])) {
                $arResult['COMPANY_ID'] = $eventData['company_id'];
            }
        }
    }
}

$this->IncludeComponentTemplate();
