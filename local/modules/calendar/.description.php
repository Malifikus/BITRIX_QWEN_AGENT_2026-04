<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

return [
    'event_handlers' => [
        [
            'event_name' => 'OnBeforeCalendarEventSave',
            'module_id' => 'calendar',
            'event' => 'OnBeforeCalendarEventSave',
            'type' => 'function',
            'handler' => 'CustomCalendar_OnBeforeCalendarEventSave'
        ],
        [
            'event_name' => 'OnAfterCalendarEventSave',
            'module_id' => 'calendar',
            'event' => 'OnAfterCalendarEventSave',
            'type' => 'function',
            'handler' => 'CustomCalendar_OnAfterCalendarEventSave'
        ]
    ]
];
