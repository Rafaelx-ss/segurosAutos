<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Admin;

/**
 * AdminSearch represents the model behind the search form of `app\models\Admin`.
 */
class AdminSearch extends Admin
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id_admin'], 'integer'],
            [['Tipo_user', 'Nombre_admin', 'User_admin', 'Pass_hadmin', 'Pass_radmin', 'AuthKey', 'Status_admin'], 'safe'],
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
        $query = Admin::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'Id_admin' => $this->Id_admin,
        ]);

        $query->andFilterWhere(['like', 'Tipo_user', $this->Tipo_user])
            ->andFilterWhere(['like', 'Nombre_admin', $this->Nombre_admin])
            ->andFilterWhere(['like', 'User_admin', $this->User_admin])
            ->andFilterWhere(['like', 'Pass_hadmin', $this->Pass_hadmin])
            ->andFilterWhere(['like', 'Pass_radmin', $this->Pass_radmin])
            ->andFilterWhere(['like', 'AuthKey', $this->AuthKey])
            ->andFilterWhere(['like', 'Status_admin', $this->Status_admin])
            ->andWhere(['!=', 'Status_admin', 'Eliminado']);

        return $dataProvider;
    }
}
