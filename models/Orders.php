<?php

namespace app\models;

use Yii;

/**
 * @SWG\Definition(required={"id", "user_id", "date", "price", "type"})
 *
 * @SWG\Property(property="id", type="integer")
 * @SWG\Property(property="user_id", type="integer")
 * @SWG\Property(property="user", type="object", ref="#/definitions/User")
 * @SWG\Property(property="date", type="integer", description="Unix timestamp")
 * @SWG\Property(property="price", type="number", format="float")
 * @SWG\Property(property="type", type="integer")
 * @SWG\Property(property="info", type="integer")
 *
 * This is the model class for table "orders".
 */
class Orders extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'date', 'price', 'type', 'info'], 'required'],
            [['user_id', 'date', 'type', 'info'], 'integer'],
            ['price', 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'date' => 'Date',
            'price' => 'Price',
            'type' => 'Type',
            'info' => 'Info',
        ];
    }
}
