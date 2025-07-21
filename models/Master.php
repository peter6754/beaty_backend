<?php

namespace app\models;

use Yii;

class Master extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'middlename', 'firstname', 'birthday', 'date'], 'required'],
            [['user_id', 'balance', 'gender', 'search_radius', 'client_gender', 'status', 'date'], 'integer'],
            [['work_lat', 'work_lon', 'live_lat', 'live_lon'], 'number'],
            [['birthday'], 'date', 'format' => 'php:d.m.Y'],
            [['lastname', 'firstname', 'middlename', 'work_city', 'work_street', 'work_house', 'live_city', 'live_street', 'live_house', 'order_id'], 'string', 'max' => 255],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function afterFind()
    {
        $this->birthday =  date("d.m.Y", $this->birthday);
    }

    public function beforeSave($insert)
    {
        // Преобразуем дату только если она не является timestamp
        if (is_string($this->birthday) && preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $this->birthday)) {
            $this->birthday = Yii::$app->global->convertDateToTime($this->birthday);
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
            'user_id' => 'User ID',
            'balance' => 'Balance',
            'lastname' => 'Lastname',
            'firstname' => 'Firstname',
            'middlename' => 'Middlename',
            'gender' => 'Gender',
            'birthday' => 'Birthday',
            'work_city' => 'Work City',
            'work_street' => 'Work Street',
            'work_house' => 'Work House',
            'work_lat' => 'Work Lat',
            'work_lon' => 'Work Lon',
            'live_city' => 'Live City',
            'live_street' => 'Live Street',
            'live_house' => 'Live House',
            'live_lat' => 'Live Lat',
            'live_lon' => 'Live Lon',
            'search_radius' => 'Search Radius',
            'client_gender' => 'Client Gender',
            'order_id' => 'Order ID',
            'date' => 'Date',
            'status' => 'Status',
        ];
    }
}
