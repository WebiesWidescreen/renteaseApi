<?php

/*************** Start Login ******************/
$f3->route(
    'POST /LoginCheck',
    function ($f3) {
        header('Content-Type: application/json');
        $decoded_items =  json_decode($f3->get('BODY'), true);
        if (!$decoded_items == NULL)
            checkLogin($decoded_items);
        else
            echo json_encode(array("status" => "error This value", "message_text" => "Invalid input parameters"), JSON_FORCE_OBJECT);
    }
);
/*************** End Login ******************/

/*************** Start Sign Up OTP Check  ******************/
$f3->route(
    'POST /SignUpOTPCheck',
    function ($f3) {
        header('Content-Type: application/json');
        $decoded_items =  json_decode($f3->get('BODY'), true);
        if (!$decoded_items == NULL)
            checkSignUpOTP($decoded_items);
        else
            echo json_encode(array("status" => "error This value", "message_text" => "Invalid input parameters"), JSON_FORCE_OBJECT);
    }
);
/*************** End Sign Up AVN Check ******************/

/*************** Start Resend OTP Check  ******************/
$f3->route(
    'POST /ResendOTPCheck',
    function ($f3) {
        header('Content-Type: application/json');
        $decoded_items =  json_decode($f3->get('BODY'), true);
        if (!$decoded_items == NULL)
            checkResendOTP($decoded_items);
        else
            echo json_encode(array("status" => "error This value", "message_text" => "Invalid input parameters"), JSON_FORCE_OBJECT);
    }
);
/*************** End Sign Up AVN Check ******************/

/*************** Start Sign Up AVN Check  ******************/
$f3->route(
    'POST /SignUpAVNCheck',
    function ($f3) {
        header('Content-Type: application/json');
        $decoded_items =  json_decode($f3->get('BODY'), true);
        if (!$decoded_items == NULL)
            checkAVNSignUp($decoded_items);
        else
            echo json_encode(array("status" => "error This value", "message_text" => "Invalid input parameters"), JSON_FORCE_OBJECT);
    }
);
/*************** End Sign Up AVN Check ******************/
