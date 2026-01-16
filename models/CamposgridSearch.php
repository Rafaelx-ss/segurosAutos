<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Camposgrid;

/**
 * CamposgridSearch represents the model behind the search form of `app\models\Camposgrid`.
 */
class CamposgridSearch extends Camposgrid
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['campoGridID', 'orden',  'catalogoID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['textoID', 'nombreCampo', 'regFechaUltimaModificacion', 'catalogoReferenciaID', 'tipoControl', 'textField', 'valueField', 'searchVisible', 'valorDefault', 'controlQuery', 'queryValor', 'searchQuery'], 'safe'],
            [['visible', 'regEstado'], 'boolean'],
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
	public $idTextos;
    public function search($params)
    {
        $query = Camposgrid::find();
		$query->joinWith(['idTextos']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
		
		$dataProvider->sort->attributes['idTextos'] = [
            // se agregan los atributos relacionados en las tablas
            'asc' => ['Textos.nombreTexto' => SORT_ASC],
            'desc' => ['Textos.nombreTexto' => SORT_DESC],
        ];


        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'campoGridID' => $this->campoGridID,
            'visible' => $this->visible,
            'orden' => $this->orden,
            //'textoID' => $this->textoID,
            'catalogoID' => $this->catalogoID,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

		//'tipoControl', 'textField', 'searchVisible'
        $query->andFilterWhere(['like', 'nombreCampo', $this->nombreCampo])
			->andFilterWhere(['like', 'Textos.nombreTexto', $this->textoID])
			->andFilterWhere(['like', 'textField', $this->textField])
			->andFilterWhere(['like', 'searchVisible', $this->searchVisible])
			->andFilterWhere(['like', 'tipoControl', $this->tipoControl])
			->andWhere(['=', 'CamposGrid.regEstado', '1'])
			->andWhere(['=', 'catalogoID', $_GET['token']])
			->orderBy(['orden' => SORT_ASC]);

        return $dataProvider;
    }
}
