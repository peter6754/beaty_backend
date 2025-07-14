<?php

namespace app\models;

use Yii;

/**
 * @SWG\Definition(required={"id", "date", "user_id", "coupon_id", "price"})
 *
 * @SWG\Property(property="id", type="integer")
 * @SWG\Property(property="user_id", type="integer", description="User ID")
 * @SWG\Property(property="user", type="object", ref="#/definitions/User")
 * @SWG\Property(property="coupon_id", type="integer", description="Coupon ID")
 * @SWG\Property(property="coupon", type="object", ref="#/definitions/Coupon")
 * @SWG\Property(property="date", type="integer", description="Order date (Unix timestamp)")
 * @SWG\Property(property="price", type="number", format="float", description="Order price")
 * @SWG\Property(property="order_id", type="string", description="External order ID")
 * @SWG\Property(property="status", type="integer", description="Order status")
 */
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
