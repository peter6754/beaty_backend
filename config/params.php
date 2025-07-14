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

// Настройки YooMoney в зависимости от среды
if (YII_ENV_DEV) {
    // Тестовые параметры для разработки
    $params['yoomoney_shopid'] = 'test_shopid';
    $params['yoomoney_secret'] = 'test_secret';
} else {
    // Продакшн параметры
    $params['yoomoney_shopid'] = '931869';
    $params['yoomoney_secret'] = 'live_yeDVRr2PkoyuM-_nQ_qEGv_ehMCFUbzVk1ew_F9Jih8';
}

return $params;
