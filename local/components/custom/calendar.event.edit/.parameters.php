<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

$arComponentParameters = [
    "GROUPS" => [],
    "PARAMETERS" => [
        "EVENT_ID" => [
            "PARENT" => "BASE",
            "NAME" => "ID события",
            "TYPE" => "STRING",
            "DEFAULT" => ""
        ]
    ]
];
