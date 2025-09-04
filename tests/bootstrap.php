<?php
set_time_limit(0);

require __DIR__ . '/../vendor/autoload.php';

foreach (\Howyi\Conv\TestUtility::getPdoArray() as $pdo) {
    $pdo->exec('DROP DATABASE IF EXISTS conv_test');
    $pdo->exec('CREATE DATABASE conv_test');
}
