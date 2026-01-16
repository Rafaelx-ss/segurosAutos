<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Pcompuestos;

/**
 * PcompuestosSearch represents the model behind the search form of `app\models\Pcompuestos`.
 */
class PcompuestosSearch extends Pcompuestos
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['perfilCompuestoID', 'usuarioID', 'perfilID',  'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'safe'],
            [['activoPermiso', 'regEstado'], 'boolean'],
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
	public $idUsuarios;
	
    public function search($params)
    {
        $query = Pcompuestos::find();
		$query->joinWith(['idPerfil']);
		$query->joinWith(['idUsuarios']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
		
		$dataProvider->sort->attributes['idPerfil'] = [
				'asc' => ['Perfiles.nombrePerfil' => SORT_ASC],
				'desc' => ['Perfiles.nombrePerfil' => SORT_DESC],
		];
		
		$dataProvider->sort->attributes['idUsuarios'] = [
				'asc' => ['Usuarios.usuario' => SORT_ASC],
				'desc' => ['Usuarios.usuario' => SORT_DESC],
		];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'perfilCompuestoID' => $this->perfilCompuestoID,
            //'usuarioID' => $this->usuarioID,
            //'perfilID' => $this->perfilID,
            'activoPermiso' => $this->activoPermiso,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);
		
		$query->andWhere(['=', 'PerfilesCompuestos.regEstado', '1']);
		$query->andFilterWhere(['like', 'Perfiles.nombrePerfil', $this->perfilID]);
		$query->andFilterWhere(['like', 'Usuarios.usuario', $this->usuarioID]);

        return $dataProvider;
    }
}
