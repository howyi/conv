<?php
set_time_limit(0);

require dirname(__FILE__) . '/../vendor/autoload.php';

foreach (\Laminaria\Conv\TestUtility::getPdoArray() as $pdo) {
    $pdo->exec('DROP DATABASE IF EXISTS conv_test');
    $pdo->exec('CREATE DATABASE conv_test');
}
