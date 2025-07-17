<?php

namespace app\components;

use Yii;
use yii\base\Component;

class Sms extends Component
{
    public function send($phone, $message)
    {
        // тестовая отправка SMS
        Yii::info("Sending SMS to {$phone}: {$message}", 'sms');
        return true;
    }
}
