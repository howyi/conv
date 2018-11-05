<?php
set_time_limit(0);

require dirname(__FILE__) . '/../vendor/autoload.php';

$pdo = \Laminaria\Conv\TestUtility::getPdo();

$pdo->exec('DROP DATABASE IF EXISTS conv_test');
$pdo->exec('CREATE DATABASE conv_test');
