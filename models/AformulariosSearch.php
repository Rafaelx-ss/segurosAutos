<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Aformularios;

/**
 * AformulariosSearch represents the model behind the search form of `app\models\Aformularios`.
 */
class AformulariosSearch extends Aformularios
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['accionFormularioID',  'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['estadoAccion', 'regEstado'], 'boolean'],
            [['accionID', 'claveAccion', 'formularioID', 'regFechaUltimaModificacion'], 'safe'],
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
	public $idAccion;
	public $idFomulario;
	
	
    public function search($params)
    {
        $query = Aformularios::find();
		$query->joinWith(['idAccion']);
		$query->joinWith(['idFomulario']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
		
		$dataProvider->sort->attributes['idAccion'] = [
				'asc' => ['Acciones.nombreAccion' => SORT_ASC],
				'desc' => ['Acciones.nombreAccion' => SORT_DESC],
		];
		
		$dataProvider->sort->attributes['idFomulario'] = [
				'asc' => ['Formularios.nombreFormulario' => SORT_ASC],
				'desc' => ['Formularios.nombreFormulario' => SORT_DESC],
		];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'accionFormularioID' => $this->accionFormularioID,
            'estadoAccion' => $this->estadoAccion,
            //'accionID' => $this->accionID,
            //'formularioID' => $this->formularioID,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);
		
		$query->andWhere(['=', 'AccionesFormularios.regEstado', '1']);
		$query->andFilterWhere(['like', 'claveAccion', $this->claveAccion]);
		$query->andFilterWhere(['like', 'Acciones.nombreAccion', $this->accionID]);
		$query->andFilterWhere(['like', 'Formularios.nombreFormulario', $this->formularioID]);

        return $dataProvider;
    }
}
