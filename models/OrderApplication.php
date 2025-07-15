<?php

namespace app\models;

use Yii;

class OrderApplication extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_application';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'phone', 'date', 'city', 'street', 'house', 'apartment', 'entrance', 'floor', 'intercom', 'product_id', 'time'], 'required'],
            [['product_id', 'master_id', 'status', 'user_id', 'order_coupon_id', 'time'], 'integer'],
            ['price', 'number'],
            [['date'], 'date', 'format' => 'php:d.m.Y'],
            ['phone', 'match', 'pattern' => '/^\+7\([0-9]{3}\)\s[0-9]{3}\-[0-9]{2}\-[0-9]{2}$/', 'message' => 'Пример: +7(999) 999-99-99'],
            [['lat', 'lon'], 'number'],
            [['lat', 'lon'], 'default', 'value' => 0],
            [['comment'], 'string'],
            [['name', 'city', 'street', 'house'], 'string', 'max' => 255],
        ];
    }

    public function afterFind()
    {
        $this->date = date("d.m.Y", $this->date);
        $this->phone = $this->maskPhone($this->phone);
    }

    static function maskPhone($number)
    {
        return sprintf("+%s(%s) %s-%s-%s",
            substr($number, 0, 1),
            substr($number, 1, 3),
            substr($number, 4, 3),
            substr($number, 7, 2),
            substr($number, 9)
        );
    }

    public function beforeSave($insert)
    {
        $this->date = Yii::$app->global->convertDateToTime($this->date);
        $this->phone = preg_replace('/[^0-9]/', '', $this->phone);

        if (empty($this->lat) || $this->lat === '') {
            $this->lat = 0.0;
        }
        if (empty($this->lon) || $this->lon === '') {
            $this->lon = 0.0;
        }

        if (parent::beforeSave($insert)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'phone' => 'Телефон',
            'time' => 'Время',
            'date' => 'Дата',
            'city' => 'Город',
            'street' => 'Улица',
            'house' => 'Дом',
            'apartment' => 'Квартира',
            'entrance' => 'Подъезд',
            'floor' => 'Этаж',
            'intercom' => 'Домофон',
            'lat' => 'Lat',
            'lon' => 'Lon',
            'product_id' => 'Продукт',
            'comment' => 'Комментарий',
            'master_id' => 'Мастер',
            'status' => 'Статус',
        ];
    }
}
