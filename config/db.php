<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host='.env('DB_HOST', 'localhost').'; dbname='.env('DB_NAME', 'beautyms_beauty_db'),
    'username' => env('DB_USER', 'root'),
    'password' => env('DB_PASS', 'root'),
    'charset' => 'utf8mb4',
];