<?php
    // $hostname = 'srv1190.hstgr.io';
    // $dbname   = 'u437965069_hackertest';
    // $username = 'u437965069_hacker';
    // $password = 'IUjV533Tb2'; 
    
    $hostname = 'localhost'; 
    $dbname = 'scrab'; 
    $username = 'root';
    $password = 'root';

    $connect_var = mysqli_connect($hostname, $username, $password, $dbname);
    mysqli_set_charset($connect_var,"utf8");
?>
