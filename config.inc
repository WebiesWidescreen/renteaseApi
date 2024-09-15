<?php
    $hostname = 'localhost'; 
    $dbname = 'rentease'; 
    $username = 'root';
    $password = 'root';

    $connect_var = mysqli_connect($hostname, $username, $password, $dbname);
    mysqli_set_charset($connect_var,"utf8");
?>
