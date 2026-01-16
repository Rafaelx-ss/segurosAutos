<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Pmenus;

/**
 * PmenusSearch represents the model behind the search form of `app\models\Pmenus`.
 */
class PmenusSearch extends Pmenus
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['permisosMenusID', 'perfilID', 'menuID', 'orden', 'activoMenusFormularios', 'versionRegistro', 'regEstado', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'safe'],
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
	
	public $idPerfil;
	public $idMenu;
	
    public function search($params)
    {
        $query = Pmenus::find();
		$query->joinWith(['idPerfil']);
		$query->joinWith(['idMenu']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
		
		$dataProvider->sort->attributes['idPerfil'] = [
				'asc' => ['Perfiles.nombrePerfil' => SORT_ASC],
				'desc' => ['Perfiles.nombrePerfil' => SORT_DESC],
		];
		
		$dataProvider->sort->attributes['idMenu'] = [
				'asc' => ['Menus.nombreMenu' => SORT_ASC],
				'desc' => ['Menus.nombreMenu' => SORT_DESC],
		];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'permisosMenusID' => $this->permisosMenusID,
            //'perfilID' => $this->perfilID,
            //'menuID' => $this->menuID,
            'orden' => $this->orden,
            'activoMenusFormularios' => $this->activoMenusFormularios,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);
		
		$query->andWhere(['=', 'PermisosMenus.regEstado', '1']);
		$query->andFilterWhere(['like', 'Perfiles.nombrePerfil', $this->perfilID]);
		$query->andFilterWhere(['like', 'Menus.nombreMenu', $this->menuID]);

        return $dataProvider;
    }
}
