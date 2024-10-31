<?php

/*************** Start ADD_PROPERTY ******************/
$f3->route(
    'POST /AddProperty',
    function ($f3) {
        header('Content-Type: application/json');
        $decoded_items =  json_decode($f3->get('BODY'), true);
        if (!$decoded_items == NULL)
            propertyAdd($decoded_items);
        else
            echo json_encode(array("status" => "error This value", "message_text" => "Invalid input parameters"), JSON_FORCE_OBJECT);
    }
);
/*************** End ADD_PROPERTY ******************/

/*************** Start UPDATE_PROPERTY ******************/
$f3->route(
    'POST /UpdateProperty',
    function ($f3) {
        header('Content-Type: application/json');
        $decoded_items =  json_decode($f3->get('BODY'), true);
        if (!$decoded_items == NULL)
            propertyUpdate($decoded_items);
        else
            echo json_encode(array("status" => "error This value", "message_text" => "Invalid input parameters"), JSON_FORCE_OBJECT);
    }
);
/*************** End UPDATE_PROPERTY ******************/

/*************** Start DELETE_PROPERTY ******************/
$f3->route(
    'POST /DeleteProperty',
    function ($f3) {
        header('Content-Type: application/json');
        $decoded_items =  json_decode($f3->get('BODY'), true);
        if (!$decoded_items == NULL)
            propertyDelete($decoded_items);
        else
            echo json_encode(array("status" => "error This value", "message_text" => "Invalid input parameters"), JSON_FORCE_OBJECT);
    }
);
/*************** End DELETE_PROPERTY ******************/

/*************** Start GET_PROPERTY_LIST ******************/
$f3->route(
    'POST /GetPropertyList',
    function ($f3) {
        header('Content-Type: application/json');
        $decoded_items =  json_decode($f3->get('BODY'), true);
        if (!$decoded_items == NULL)
            propertyListGet($decoded_items);
        else
            echo json_encode(array("status" => "error This value", "message_text" => "Invalid input parameters"), JSON_FORCE_OBJECT);
    }
);
/*************** End GET_PROPERTY_LIST ******************/

/*************** Start ADD_PROPERTY_UNIT ******************/
$f3->route(
    'POST /AddPropertyUnit',
    function ($f3) {
        header('Content-Type: application/json');
        $decoded_items =  json_decode($f3->get('BODY'), true);
        if (!$decoded_items == NULL)
            propertyUnitAdd($decoded_items);
        else
            echo json_encode(array("status" => "error This value", "message_text" => "Invalid input parameters"), JSON_FORCE_OBJECT);
    }
);
/*************** End ADD_PROPERTY_UNIT ******************/

/*************** Start UPDATE_PROPERTY_UNIT ******************/
$f3->route(
    'POST /UpdatePropertyUnit',
    function ($f3) {
        header('Content-Type: application/json');
        $decoded_items =  json_decode($f3->get('BODY'), true);
        if (!$decoded_items == NULL)
            propertyUnitUpdate($decoded_items);
        else
            echo json_encode(array("status" => "error This value", "message_text" => "Invalid input parameters"), JSON_FORCE_OBJECT);
    }
);
/*************** End UPDATE_PROPERTY_UNIT ******************/

/*************** Start DELETE_PROPERTY_UNIT ******************/
$f3->route(
    'POST /DeletePropertyUnit',
    function ($f3) {
        header('Content-Type: application/json');
        $decoded_items =  json_decode($f3->get('BODY'), true);
        if (!$decoded_items == NULL)
            propertyUnitDelete($decoded_items);
        else
            echo json_encode(array("status" => "error This value", "message_text" => "Invalid input parameters"), JSON_FORCE_OBJECT);
    }
);
/*************** End DELETE_PROPERTY_UNIT ******************/

/*************** Start GET_PROPERTY_UNIT_LIST ******************/
$f3->route(
    'POST /GetPropertyUnitList',
    function ($f3) {
        header('Content-Type: application/json');
        $decoded_items =  json_decode($f3->get('BODY'), true);
        if (!$decoded_items == NULL)
            propertyUnitListGet($decoded_items);
        else
            echo json_encode(array("status" => "error This value", "message_text" => "Invalid input parameters"), JSON_FORCE_OBJECT);
    }
);
/*************** End GET_PROPERTY_UNIT_LIST ******************/
