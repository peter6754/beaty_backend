<?php

namespace app\models;

use Yii;
use yii\base\Model;

class CouponForm extends Model
{
    public $phone;
    public $name;
    public $coupon_id;

    private $_user = false;

    public function rules()
    {
        return [
            [['name', 'phone'], 'required'],
            ['phone', 'match', 'pattern' => '/^\+7\([0-9]{3}\)\s[0-9]{3}\-[0-9]{2}\-[0-9]{2}$/', 'message' => 'Пример: +7(999) 999-99-99'],
            [['name'], 'string', 'max' => 255],
            ['coupon_id', 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Имя',
            'phone' => 'Телефон',
        ];
    }
}