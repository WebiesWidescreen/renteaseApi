<?php
    // $hostname = 'localhost'; 
    // $dbname = 'rentease'; 
    // $username = 'root';
    // $password = 'root';

    $hostname = 'localhost'; 
    $dbname = 'wide4992_rentease'; 
    $username = 'wide4992_root';
    $password = 'Admin@2024';

    $connect_var = mysqli_connect($hostname, $username, $password, $dbname);
    mysqli_set_charset($connect_var,"utf8");
?>
