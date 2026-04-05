<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (!check_bitrix_sessid()) {
    echo json_encode(['success' => false, 'error' => 'Invalid session']);
    die();
}

$action = $_REQUEST['action'] ?? '';
$search = $_REQUEST['search'] ?? '';

if ($action === 'get_companies') {
    $result = ['success' => true, 'companies' => []];
    
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
            $result['companies'][] = [
                'id' => $company['ID'],
                'name' => $company['TITLE']
            ];
        }
    }
    
    echo json_encode($result);
} else {
    echo json_encode(['success' => false, 'error' => 'Unknown action']);
}

die();
