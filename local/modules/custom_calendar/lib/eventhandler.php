<?php

namespace Custom\Calendar;

use Bitrix\Main\Event;
use Bitrix\Main\EventResult;

class EventHandler
{
    public static function onBeforeCalendarEventAdd(Event $event)
    {
        return self::validateAndSave($event);
    }
    
    public static function onAfterCalendarEventAdd(Event $event)
    {
        return new EventResult();
    }
    
    public static function onBeforeCalendarEventUpdate(Event $event)
    {
        return self::validateAndSave($event);
    }
    
    private static function validateAndSave(Event $event)
    {
        $fields = $event->getParameter('fields');
        $request = \Bitrix\Main\Context::getCurrent()->getRequest();
        
        $eventType = $request->getPost('CUSTOM_EVENT_TYPE');
        $companyId = $request->getPost('CUSTOM_COMPANY_ID');
        
        if (empty($eventType)) {
            $result = new EventResult();
            $result->setType(EventResult::ERROR);
            $result->addErrors(['Не выбран тип события']);
            return $result;
        }
        
        if ($eventType === 'meeting' && empty($companyId)) {
            $result = new EventResult();
            $result->setType(EventResult::ERROR);
            $result->addErrors(['Для типа "Встреча" необходимо выбрать компанию']);
            return $result;
        }
        
        $description = $fields['DESCRIPTION'] ?? '';
        $customData = [
            'EVENT_TYPE' => $eventType,
            'COMPANY_ID' => $companyId ?: null
        ];
        
        $jsonMarker = '<!--CUSTOM_CALENDAR_DATA-->';
        $description = preg_replace('/' . preg_quote($jsonMarker, '/') . '.*?<\/!--CUSTOM_CALENDAR_DATA-->/s', '', $description);
        $description .= $jsonMarker . json_encode($customData) . '</!--CUSTOM_CALENDAR_DATA-->';
        
        $event->setParameter('fields', array_merge($fields, ['DESCRIPTION' => $description]));
        
        return new EventResult();
    }
}
