<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * @SWG\Definition(required={"hash", "work_city", "work_street", "work_house", "live_city", "live_street", "live_house", "search_radius"})
 *
 * @SWG\Property(property="hash", type="string", description="Master registration hash")
 * @SWG\Property(property="work_city", type="string", description="Work city")
 * @SWG\Property(property="work_street", type="string", description="Work street")
 * @SWG\Property(property="work_house", type="string", description="Work house number")
 * @SWG\Property(property="work_lat", type="number", format="float", description="Work location latitude")
 * @SWG\Property(property="work_lon", type="number", format="float", description="Work location longitude")
 * @SWG\Property(property="live_city", type="string", description="Living city")
 * @SWG\Property(property="live_street", type="string", description="Living street")
 * @SWG\Property(property="live_house", type="string", description="Living house number")
 * @SWG\Property(property="live_apartment", type="string", description="Living apartment number")
 * @SWG\Property(property="live_lat", type="number", format="float", description="Living location latitude")
 * @SWG\Property(property="live_lon", type="number", format="float", description="Living location longitude")
 * @SWG\Property(property="search_radius", type="integer", description="Search radius in kilometers")
 * @SWG\Property(property="client_gender", type="integer", description="Preferred client gender (1 - male, 0 - female)")
 * @SWG\Property(property="products", type="array", @SWG\Items(type="integer"), description="Array of product IDs")
 */
class MasterProceedForm extends Model
{
    public $hash;
    public $work_city;
    public $work_street;
    public $work_house;
    public $work_lat;
    public $work_lon;
    public $live_city;
    public $live_street;
    public $live_house;
    public $live_apartment;
    public $live_lat;
    public $live_lon;
    public $search_radius;
    public $client_gender;
    public $products;

    public function rules()
    {
        return [
            [['hash', 'work_city', 'work_street', 'work_house', 'live_city', 'live_street', 'live_house', 'search_radius'], 'required'],
            ['products', 'each', 'rule' => ['integer']],
            [['work_lat', 'work_lon', 'live_lat', 'live_lon'], 'number'],
            [['search_radius', 'client_gender'], 'integer'],
            [['hash', 'work_city', 'work_street', 'work_house', 'live_city', 'live_street', 'live_house', 'live_apartment'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'products' => 'Услуги',

            'work_city' => 'Город',
            'work_street' => 'Улица',
            'work_house' => 'Дом',
            'apartment' => 'Квартира',
            'work_lat' => 'Work Lat',
            'work_lon' => 'Work Lon',

            'live_city' => 'Город',
            'live_street' => 'Улица',
            'live_house' => 'Дом',
            'live_apartment' => 'Квартира',
            'live_lat' => 'Live Lat',
            'live_lon' => 'Live Lon',

            'search_radius' => 'Радиус поиска заказов',
            'client_gender' => 'Пол клиентов',
        ];
    }
}