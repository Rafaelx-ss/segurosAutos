<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Dependientetres;

/**
 * DependientetresSearch represents the model behind the search form of `app\models\Dependientetres`.
 */
class DependientetresSearch extends Dependientetres
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dependienteTresID', 'establecimientoID', 'dependienteOneID', 'dependienteTwoID', 'Captura', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'safe'],
            [['regEstado'], 'boolean'],
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
        $query = Dependientetres::find();
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
            'dependienteTresID' => $this->dependienteTresID,
            'establecimientoID' => $this->establecimientoID,
            'dependienteOneID' => $this->dependienteOneID,
            'dependienteTwoID' => $this->dependienteTwoID,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'Captura', $this->Captura]);

        $query->andWhere(['=', 'dependientetres.regEstado', '1']);
		

	return $dataProvider;		
		
    }
}
