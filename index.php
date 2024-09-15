<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Kickstart the framework
$f3 = require('lib/base.php');
$f3->config('config.ini');
$f3->config('readConfig.ini');

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

// require 'test/test.php';

/****** GENERAL  *****/
require 'General/JwtTocknDecode.php';
require 'General/JwtTokenEncode.php';

/****** TOKEN ENCODE COMPONENT  *****/
require 'EncodeJson/EncodeTokenJsonRouter.php';
require 'EncodeJson/EncodeTokenJsonComponent.php';

/****** LOGIN COMPONENET  *****/
require 'Login/LoginRouter.php';
require 'Login/LoginComponent.php';

/****** DASHBOARD COMPONENET  *****/
require 'Dashboard/DashboardRouter.php';
require 'Dashboard/DashboardComponent.php';


$f3->route(
    'GET /',
    function ($f3) {
        echo "Hey There";
    }
);
$f3->run();
