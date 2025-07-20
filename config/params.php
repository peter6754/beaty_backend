<?php

$params = [
    'bsVersion' => '5.x',
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'ymap_api_key' => '2b9adc0b-4e63-4faf-9690-1b7e915e812d',
    'master_status' => [
        "0" => "Не оплачен",
        "1" => "Оплачен",
        "2" => "Подтвержден",
        "3" => "Отклонить",
        "4" => "Оплата не прошла"
    ]
];

// Настройки Robokassa
$params['robokassa_login'] = 'beautyms.ru';

if (YII_ENV_DEV) {
    // Тестовые пароли для разработки
    $params['robokassa_pass1'] = 'nlGY1S15gYx4Uam3ObWJ';
    $params['robokassa_pass2'] = 'gg6pl1c0MRRdi0g0WCal';
    $params['robokassa_test'] = 1;
} else {
    // Продакшн пароли (нужно получить от Robokassa)
    $params['robokassa_pass1'] = $_ENV['ROBOKASSA_PASS1'] ?? 'PRODUCTION_PASS1';
    $params['robokassa_pass2'] = $_ENV['ROBOKASSA_PASS2'] ?? 'PRODUCTION_PASS2';
    $params['robokassa_test'] = 0;
}

return $params;
