<?php
require_once 'jwt-auth/firebase/php-jwt/src/BeforeValidException.php';
require_once 'jwt-auth/firebase/php-jwt/src/ExpiredException.php';
require_once 'jwt-auth/firebase/php-jwt/src/SignatureInvalidException.php';
require_once 'jwt-auth/firebase/php-jwt/src/JWT.php';

use \Firebase\JWT\JWT;

function encodeToken($endcodeToken, $secratekey)
{
    $encodeToken = JWT::encode($endcodeToken, $secratekey, 'HS512');
    return $encodeToken;
}
