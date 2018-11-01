<?php
set_time_limit(0);

require dirname(__FILE__) . '/../vendor/autoload.php';

$pdo = new \PDO('mysql:host=127.0.0.1;charset=utf8;', 'root', '');
$pdo->exec('DROP DATABASE IF EXISTS conv_test');
$pdo->exec('CREATE DATABASE conv_test');
