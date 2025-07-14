<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Coupon;

/**
 * @SWG\Definition()
 *
 * @SWG\Property(property="id", type="integer", description="Coupon ID for search")
 * @SWG\Property(property="category_id", type="integer", description="Category ID for search")
 * @SWG\Property(property="amount", type="integer", description="Coupon amount for search")
 * @SWG\Property(property="price", type="integer", description="Coupon price for search")
 * @SWG\Property(property="name", type="string", description="Coupon name for search")
 * @SWG\Property(property="description", type="string", description="Coupon description for search")
 *
 * CouponSearch represents the model behind the search form of `app\models\Coupon`.
 */
class CouponSearch extends Coupon
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'amount', 'price'], 'integer'],
            [['name', 'description'], 'safe'],
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
        $query = Coupon::find();

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
            'category_id' => $this->category_id,
            'amount' => $this->amount,
            'price' => $this->price,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
