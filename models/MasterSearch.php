<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Master;

/**
 * MasterSearch represents the model behind the search form of `app\models\Master`.
 */
class MasterSearch extends Master
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'balance', 'gender', 'birthday', 'search_radius', 'client_gender', 'status'], 'integer'],
            [['lastname', 'firstname', 'middlename', 'work_city', 'work_street', 'work_house', 'live_city', 'live_street', 'live_house'], 'safe'],
            [['work_lat', 'work_lon', 'live_lat', 'live_lon'], 'number'],
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
        $query = Master::find();

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
            'balance' => $this->balance,
            'gender' => $this->gender,
            'birthday' => $this->birthday,
            'work_lat' => $this->work_lat,
            'work_lon' => $this->work_lon,
            'live_lat' => $this->live_lat,
            'live_lon' => $this->live_lon,
            'search_radius' => $this->search_radius,
            'client_gender' => $this->client_gender,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'lastname', $this->lastname])
            ->andFilterWhere(['like', 'firstname', $this->firstname])
            ->andFilterWhere(['like', 'middlename', $this->middlename])
            ->andFilterWhere(['like', 'work_city', $this->work_city])
            ->andFilterWhere(['like', 'work_street', $this->work_street])
            ->andFilterWhere(['like', 'work_house', $this->work_house])
            ->andFilterWhere(['like', 'live_city', $this->live_city])
            ->andFilterWhere(['like', 'live_street', $this->live_street])
            ->andFilterWhere(['like', 'live_house', $this->live_house]);

        return $dataProvider;
    }
}
