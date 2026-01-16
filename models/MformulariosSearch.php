<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Mformularios;

/**
 * MformulariosSearch represents the model behind the search form of `app\models\Mformularios`.
 */
class MformulariosSearch extends Mformularios
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['menusFormulariosID', 'formularioID', 'menuID', 'ordenMenuFormulario', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'safe'],
            [['activoMenusForumlarios', 'regEstado'], 'boolean'],
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
	public $idMenu;
	public $idFomulario;
	
    public function search($params)
    {
        $query = Mformularios::find();
		$query->joinWith(['idFomulario']);
		$query->joinWith(['idMenu']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
		
		$dataProvider->sort->attributes['idMenu'] = [
				'asc' => ['Menus.nombreMenu' => SORT_ASC],
				'desc' => ['Menus.nombreMenu' => SORT_DESC],
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
            'menusFormulariosID' => $this->menusFormulariosID,
            //'formularioID' => $this->formularioID,
            //'menuID' => $this->menuID,
            'ordenMenuFormulario' => $this->ordenMenuFormulario,
            'activoMenusForumlarios' => $this->activoMenusForumlarios,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);
		
		$query->andWhere(['=', 'MenusFormularios.regEstado', '1']);
		$query->andFilterWhere(['like', 'Menus.nombreMenu', $this->menuID]);
		$query->andFilterWhere(['like', 'Formularios.nombreFormulario', $this->formularioID]);

        return $dataProvider;
    }
}
