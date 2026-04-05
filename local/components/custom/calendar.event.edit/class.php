<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * Компонент для отображения формы создания/редактирования события календаря
 * с дополнительными полями: тип события и компания
 */

class CCatalogCustomCalendarEventEdit extends \CBitrixComponent
{
    public function executeComponent()
    {
        $this->includeComponentTemplate();
    }
    
    /**
     * Получение списка компаний из CRM
     */
    public function getCompaniesList($search = '')
    {
        $result = [];
        
        if (\Bitrix\Main\Loader::includeModule('crm')) {
            $filter = [];
            if (!empty($search)) {
                $filter['%TITLE'] = $search;
            }
            
            $dbRes = \Bitrix\Crm\CompanyTable::getList([
                'select' => ['ID', 'TITLE'],
                'filter' => $filter,
                'order' => ['TITLE' => 'ASC'],
                'limit' => 50
            ]);
            
            while ($company = $dbRes->fetch()) {
                $result[] = [
                    'id' => $company['ID'],
                    'name' => $company['TITLE']
                ];
            }
        }
        
        return $result;
    }
    
    /**
     * Получение данных о событии из описания
     */
    public function getEventDataFromDescription($description)
    {
        if (preg_match('/\[CUSTOM_DATA\](.*?)\[\/CUSTOM_DATA\]/s', $description, $matches)) {
            $data = $matches[1];
            $result = [];
            
            if (preg_match('/EVENT_TYPE:(\w+)/', $data, $typeMatch)) {
                $result['event_type'] = $typeMatch[1];
            }
            
            if (preg_match('/COMPANY_ID:(\d+)/', $data, $companyMatch)) {
                $result['company_id'] = (int)$companyMatch[1];
            }
            
            return $result;
        }
        
        return [];
    }
}
