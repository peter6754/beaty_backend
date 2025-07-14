<?php

namespace app\models;

use Yii;

/**
 * @SWG\Definition(required={"id", "user_id", "code", "date"})
 *
 * @SWG\Property(property="id", type="integer")
 * @SWG\Property(property="user_id", type="integer")
 * @SWG\Property(property="user", type="object", ref="#/definitions/User")
 * @SWG\Property(property="code", type="integer", description="Authentication code")
 * @SWG\Property(property="date", type="integer", description="Unix timestamp")
 *
 * This is the model class for table "auth_code".
 */
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