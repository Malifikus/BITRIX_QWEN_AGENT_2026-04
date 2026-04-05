<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

class CCustomCalendarEventEdit extends \CBitrixComponent
{
    public function executeComponent()
    {
        global $APPLICATION;
        
        if ($this->StartResultCache()) {
            $this->arResult['EVENT_TYPES'] = [
                'meeting' => 'Встреча',
                'interview' => 'Собеседование',
                'call' => 'Созвон',
                'other' => 'Другое'
            ];
            
            $this->arResult['EXISTING_DATA'] = $this->getExistingData();
            
            $this->IncludeComponentTemplate();
        }
    }
    
    protected function getExistingData()
    {
        $eventId = $_REQUEST['event_id'] ?? null;
        if (!$eventId) {
            return ['EVENT_TYPE' => '', 'COMPANY_ID' => '', 'COMPANY_NAME' => ''];
        }
        
        // Получаем событие календаря
        $event = \CCalendarEvent::GetList([
            'arFilter' => ['ID' => $eventId],
            'fetchDescription' => true
        ]);
        
        if (!empty($event[0]['DESCRIPTION'])) {
            $description = $event[0]['DESCRIPTION'];
            $jsonMarker = '<!--CUSTOM_CALENDAR_DATA-->';
            if (preg_match('/' . preg_quote($jsonMarker, '/') . '(.*?)<\/!--CUSTOM_CALENDAR_DATA-->/s', $description, $matches)) {
                $data = json_decode($matches[1], true);
                if ($data) {
                    $companyName = '';
                    if (!empty($data['COMPANY_ID'])) {
                        $company = \Bitrix\Crm\CompanyTable::getById($data['COMPANY_ID'])->fetch();
                        if ($company) {
                            $companyName = $company['TITLE'];
                        }
                    }
                    return [
                        'EVENT_TYPE' => $data['EVENT_TYPE'] ?? '',
                        'COMPANY_ID' => $data['COMPANY_ID'] ?? '',
                        'COMPANY_NAME' => $companyName
                    ];
                }
            }
        }
        
        return ['EVENT_TYPE' => '', 'COMPANY_ID' => '', 'COMPANY_NAME' => ''];
    }
}
