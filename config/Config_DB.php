<?php

$dbHost = 'localhost';
$dbLogin = 'root';
$dbPassword = '1';
$dbName = 'test';
$charset = 'utf8';

$dsn ="mysql:host=" . $dbHost . ";dbname=" . $dbName . ";charset=" . $charset;
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];


