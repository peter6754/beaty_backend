<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Master;

/**
 * @SWG\Definition()
 *
 * @SWG\Property(property="id", type="integer", description="Master ID for search")
 * @SWG\Property(property="user_id", type="integer", description="User ID for search")
 * @SWG\Property(property="balance", type="integer", description="Master balance for search")
 * @SWG\Property(property="gender", type="integer", description="Master gender for search")
 * @SWG\Property(property="birthday", type="integer", description="Master birthday for search")
 * @SWG\Property(property="search_radius", type="integer", description="Search radius for search")
 * @SWG\Property(property="client_gender", type="integer", description="Client gender preference for search")
 * @SWG\Property(property="status", type="integer", description="Master status for search")
 * @SWG\Property(property="lastname", type="string", description="Master last name for search")
 * @SWG\Property(property="firstname", type="string", description="Master first name for search")
 * @SWG\Property(property="middlename", type="string", description="Master middle name for search")
 * @SWG\Property(property="work_city", type="string", description="Work city for search")
 * @SWG\Property(property="work_street", type="string", description="Work street for search")
 * @SWG\Property(property="work_house", type="string", description="Work house for search")
 * @SWG\Property(property="live_city", type="string", description="Living city for search")
 * @SWG\Property(property="live_street", type="string", description="Living street for search")
 * @SWG\Property(property="live_house", type="string", description="Living house for search")
 * @SWG\Property(property="work_lat", type="number", format="float", description="Work latitude for search")
 * @SWG\Property(property="work_lon", type="number", format="float", description="Work longitude for search")
 * @SWG\Property(property="live_lat", type="number", format="float", description="Living latitude for search")
 * @SWG\Property(property="live_lon", type="number", format="float", description="Living longitude for search")
 *
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
