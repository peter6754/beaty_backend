<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "review".
 *
 * @property int $id
 * @property int $user_id
 * @property int $master_id
 * @property int $rating
 * @property string|null $comment
 * @property int $created_at
 */
class Review extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'review';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'master_id', 'rating'], 'required'],
            [['user_id', 'master_id', 'rating', 'created_at'], 'integer'],
            [['rating'], 'integer', 'min' => 1, 'max' => 5],
            [['comment'], 'string'],
            [['comment'], 'string', 'max' => 1000],
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
            'rating' => 'Rating',
            'comment' => 'Comment',
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
     * Get average rating for master
     */
    public static function getAverageRating($masterId)
    {
        return static::find()
            ->where(['master_id' => $masterId])
            ->average('rating');
    }

    /**
     * Get reviews count for master
     */
    public static function getReviewsCount($masterId)
    {
        return static::find()
            ->where(['master_id' => $masterId])
            ->count();
    }
}