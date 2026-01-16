<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Campos;

/**
 * CamposSearch represents the model behind the search form of `app\models\Campos`.
 */
class CamposSearch extends Campos
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['campoID', 'orden', 'catalogoID', 'catalogoReferenciaID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['textoID', 'nombreCampo', 'tipoControl', 'longitud', 'controlQuery', 'tipoCampo', 'textField', 'valueField', 'valorDefault', 'CSS', 'regFechaUltimaModificacion'], 'safe'],
            [['campoPK', 'campoFK', 'visible', 'campoRequerido', 'regEstado'], 'boolean'],
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
        $query = Campos::find();
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
            'campoID' => $this->campoID,
            'campoPK' => $this->campoPK,
            'campoFK' => $this->campoFK,
            'visible' => $this->visible,
            'orden' => $this->orden,
            'campoRequerido' => $this->campoRequerido,
            'catalogoID' => $this->catalogoID,
           // 'textoID' => $this->textoID,
            'catalogoReferenciaID' => $this->catalogoReferenciaID,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'nombreCampo', $this->nombreCampo])
			->andFilterWhere(['like', 'Textos.nombreTexto', $this->textoID])
            ->andFilterWhere(['like', 'tipoControl', $this->tipoControl])
            ->andFilterWhere(['like', 'longitud', $this->longitud])
            ->andFilterWhere(['like', 'controlQuery', $this->controlQuery])
            ->andFilterWhere(['like', 'tipoCampo', $this->tipoCampo])
            ->andFilterWhere(['like', 'textField', $this->textField])
            ->andFilterWhere(['like', 'valueField', $this->valueField])
            ->andFilterWhere(['like', 'valorDefault', $this->valorDefault])
            ->andFilterWhere(['like', 'CSS', $this->CSS])
			->andWhere(['=', 'Campos.regEstado', '1'])
			->andWhere(['=', 'catalogoID', $_GET['token']])
			->orderBy(['orden' => SORT_ASC]);

        return $dataProvider;
    }
}
