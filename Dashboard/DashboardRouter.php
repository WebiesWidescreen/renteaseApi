<?php

/*************** Start Bordband List ******************/
$f3->route(
    'POST /GetBroadbandList',
    function ($f3) {
        header('Content-Type: application/json');
        $decoded_items =  json_decode($f3->get('BODY'), true);
        if (!$decoded_items == NULL)
            broadbandListGet($decoded_items);
        else
            echo json_encode(array("status" => "error This value", "message_text" => "Invalid input parameters"), JSON_FORCE_OBJECT);
    }
);
/*************** End Bordband List ******************/
