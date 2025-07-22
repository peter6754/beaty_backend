<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "favorite_master".
 *
 * @property int $id
 * @property int $user_id
 * @property int $master_id
 * @property int $created_at
 */
class FavoriteMaster extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'favorite_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'master_id'], 'required'],
            [['user_id', 'master_id', 'created_at'], 'integer'],
            [['user_id', 'master_id'], 'unique', 'targetAttribute' => ['user_id', 'master_id'], 'message' => 'Мастер уже добавлен в избранное'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['master_id'], 'exist', 'skipOnError' => true, 'targetClass' => Master::class, 'targetAttribute' => ['master_id' => 'id']],
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
            'master_id' => 'Master ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for User.
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Gets query for Master.
     */
    public function getMaster()
    {
        return $this->hasOne(Master::class, ['id' => 'master_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_at = time();
        }
        return parent::beforeSave($insert);
    }

    /**
     * Check if master is favorited by user
     */
    public static function isFavorite($userId, $masterId)
    {
        return static::find()
            ->where(['user_id' => $userId, 'master_id' => $masterId])
            ->exists();
    }
}