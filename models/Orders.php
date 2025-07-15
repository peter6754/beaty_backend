<?php

namespace app\models;

use Yii;

/**
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
