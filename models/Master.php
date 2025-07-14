<?php

namespace app\models;

use Yii;

/**
 * @SWG\Definition(required={"id"})
 *
 * @SWG\Property(property="id", type="integer")
 * @SWG\Property(property="user", type="object", ref="#/definitions/User")
 * @SWG\Property(property="balance", type="number")
 * @SWG\Property(property="lastname", type="string")
 * @SWG\Property(property="firstname", type="string")
 * @SWG\Property(property="middlename", type="string")
 * @SWG\Property(property="gender", type="integer")
 * @SWG\Property(property="birthday", type="string")
 * @SWG\Property(property="work_city", type="string")
 * @SWG\Property(property="work_street", type="string")
 * @SWG\Property(property="work_house", type="string")
 * @SWG\Property(property="work_lat", type="number")
 * @SWG\Property(property="work_lon", type="number")
 * @SWG\Property(property="live_city", type="string")
 * @SWG\Property(property="live_street", type="string")
 * @SWG\Property(property="live_house", type="string")
 * @SWG\Property(property="live_lat", type="number")
 * @SWG\Property(property="live_lon", type="number")
 * @SWG\Property(property="search_radius", type="integer")
 * @SWG\Property(property="client_gender", type="integer")
 */
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
        $this->birthday = Yii::$app->global->convertDateToTime($this->birthday);

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
