<?php

namespace app\models;

use Yii;

class AuthCode extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_code';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'code', 'date'], 'required'],
            [['user_id', 'code', 'date'], 'integer'],
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
            'code' => 'Code',
            'date' => 'Date',
        ];
    }
}