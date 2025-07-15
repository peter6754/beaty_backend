<?php

namespace app\models;

use Yii;

class OrderCoupon extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_coupon';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date', 'user_id', 'coupon_id', 'price'], 'required'],
            [['date', 'user_id', 'coupon_id', 'status'], 'integer'],
            ['price', 'number'],
            [['order_id'], 'string', 'max' => 255],
            [['order_id'], 'safe'],
        ];
    }

    public function getCoupon()
    {
        return $this->hasOne(Coupon::className(), ['id' => 'coupon_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'Date',
            'order_id' => 'Order ID',
            'user_id' => 'User ID',
            'coupon_id' => 'Coupon ID',
            'price' => 'Price',
            'status' => 'Status',
        ];
    }
}
