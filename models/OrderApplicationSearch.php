<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\OrderApplication;

/**
 * OrderApplicationSearch represents the model behind the search form of `app\models\OrderApplication`.
 */
class OrderApplicationSearch extends OrderApplication
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'order_coupon_id', 'price', 'phone', 'date', 'time', 'product_id', 'master_id', 'status'], 'integer'],
            [['name', 'city', 'street', 'house', 'apartment', 'entrance', 'floor', 'intercom', 'comment'], 'safe'],
            [['lat', 'lon'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = OrderApplication::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (! $this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'order_coupon_id' => $this->order_coupon_id,
            'price' => $this->price,
            'phone' => $this->phone,
            'date' => $this->date,
            'time' => $this->time,
            'lat' => $this->lat,
            'lon' => $this->lon,
            'product_id' => $this->product_id,
            'master_id' => $this->master_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'street', $this->street])
            ->andFilterWhere(['like', 'house', $this->house])
            ->andFilterWhere(['like', 'apartment', $this->apartment])
            ->andFilterWhere(['like', 'entrance', $this->entrance])
            ->andFilterWhere(['like', 'floor', $this->floor])
            ->andFilterWhere(['like', 'intercom', $this->intercom])
            ->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
