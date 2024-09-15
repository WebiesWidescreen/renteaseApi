<?php
require_once 'jwt-auth/firebase/php-jwt/src/BeforeValidException.php';
require_once 'jwt-auth/firebase/php-jwt/src/ExpiredException.php';
require_once 'jwt-auth/firebase/php-jwt/src/SignatureInvalidException.php';
require_once 'jwt-auth/firebase/php-jwt/src/JWT.php';

use \Firebase\JWT\JWT;

function decodeToken($token, $secretKey)
{
    $returnData = '';
    try {
        $decodedToken = JWT::decode($token, $secretKey, array('HS512'));
        $returnData = $decodedToken;
    } catch (Exception $ex) {
        $returnData = $ex->getMessage();
    }
    return $returnData;
}
