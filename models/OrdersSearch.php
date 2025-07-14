<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Orders;

/**
 * @SWG\Definition()
 *
 * @SWG\Property(property="id", type="integer", description="Order ID for search")
 * @SWG\Property(property="user_id", type="integer", description="User ID for search")
 * @SWG\Property(property="date", type="integer", description="Order date for search (Unix timestamp)")
 * @SWG\Property(property="price", type="integer", description="Order price for search")
 * @SWG\Property(property="type", type="integer", description="Order type for search")
 * @SWG\Property(property="info", type="integer", description="Order info for search")
 *
 * OrdersSearch represents the model behind the search form of `app\models\Orders`.
 */
class OrdersSearch extends Orders
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'date', 'price', 'type', 'info'], 'integer'],
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
        $query = Orders::find();

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
            'date' => $this->date,
            'price' => $this->price,
            'type' => $this->type,
            'info' => $this->info,
        ]);

        return $dataProvider;
    }
}
