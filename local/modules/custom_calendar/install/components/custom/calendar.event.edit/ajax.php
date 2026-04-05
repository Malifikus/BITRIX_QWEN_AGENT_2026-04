<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

global $APPLICATION;
$APPLICATION->RestartBuffer();

header('Content-Type: application/json');

$request = \Bitrix\Main\Context::getCurrent()->getRequest();
$action = $request->getPost('action');

try {
    if ($action === 'get_company') {
        $companyId = intval($request->getPost('company_id'));
        if ($companyId > 0) {
            $company = \Bitrix\Crm\CompanyTable::getById($companyId)->fetch();
            if ($company) {
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'id' => $company['ID'],
                        'title' => $company['TITLE']
                    ]
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Компания не найдена']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Не указан ID компании']);
        }
    } elseif ($action === 'get_event_data') {
        $eventId = intval($request->getPost('event_id'));
        if ($eventId > 0) {
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
                        echo json_encode([
                            'success' => true,
                            'data' => [
                                'EVENT_TYPE' => $data['EVENT_TYPE'] ?? '',
                                'COMPANY_ID' => $data['COMPANY_ID'] ?? '',
                                'COMPANY_NAME' => $companyName
                            ]
                        ]);
                        exit;
                    }
                }
            }
            echo json_encode([
                'success' => true,
                'data' => ['EVENT_TYPE' => '', 'COMPANY_ID' => '', 'COMPANY_NAME' => '']
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Не указан ID события']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Неизвестное действие']);
    }
} catch (\Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
