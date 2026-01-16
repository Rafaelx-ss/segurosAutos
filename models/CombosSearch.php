<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Combos;

/**
 * CombosSearch represents the model behind the search form of `app\models\Combos`.
 */
class CombosSearch extends Combos
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['comboAnidadoID', 'catalogoID', 'campoIDPadre', 'campoIDdependiente', 'controlQuery', 'queryValue', 'queryText', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion', 'activoCombo', 'parametrosQuery'], 'safe'],
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
	public $idPadre;
	public $idDependiente;
	
    public function search($params)
    {
        $query = Combos::find();
		
		$query->joinWith(['idPadre']);
		$query->joinWith(['idDependiente']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
		
		$dataProvider->sort->attributes['idPadre'] = [
				'asc' => ['Campos.nombreCampo' => SORT_ASC],
				'desc' => ['Campos.nombreCampo' => SORT_DESC],
		];
		
		$dataProvider->sort->attributes['idDependiente'] = [
				'asc' => ['Campos.nombreCampo' => SORT_ASC],
				'desc' => ['Campos.nombreCampo' => SORT_DESC],
		];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'comboAnidadoID' => $this->comboAnidadoID,
            'CombosAnidados.catalogoID' => $this->catalogoID,
           // 'campoIDPadre' => $this->campoIDPadre,
           // 'campoIDdependiente' => $this->campoIDdependiente,
            'CombosAnidados.versionRegistro' => $this->versionRegistro,
            'CombosAnidados.regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'CombosAnidados.regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'CombosAnidados.regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'CombosAnidados.regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
			'activoCombo' => $this->activoCombo,
        ]);

        $query->andFilterWhere(['like', 'controlQuery', $this->controlQuery])
            ->andFilterWhere(['like', 'queryValue', $this->queryValue])
			->andFilterWhere(['like', 'parametrosQuery', $this->parametrosQuery])
            ->andFilterWhere(['like', 'queryText', $this->queryText]);

        $query->andWhere(['=', 'CombosAnidados.regEstado', '1']);
		$query->andFilterWhere(['like', 'Campos.nombreCampo', $this->campoIDPadre]);
		$query->andFilterWhere(['like', 'Campos.nombreCampo', $this->campoIDdependiente]);
		$query->andWhere(['=', 'CombosAnidados.catalogoID', $_GET['token']]);

        return $dataProvider;
    }
}
