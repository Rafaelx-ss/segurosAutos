<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Paccion;

/**
 * PaccionSearch represents the model behind the search form of `app\models\Paccion`.
 */
class PaccionSearch extends Paccion
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['PerfilAccionFormularioID', 'perfilID', 'accionFormularioID', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'safe'],
            [['activoPerfilAccionFormulario', 'regEstado'], 'boolean'],
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
	public $idAformularios;
	
    public function search($params)
    {
        $query = Paccion::find();
		$query->joinWith(['idPerfil']);
		$query->joinWith(['idAformularios']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
		
		$dataProvider->sort->attributes['idPerfil'] = [
				'asc' => ['Perfiles.nombrePerfil' => SORT_ASC],
				'desc' => ['Perfiles.nombrePerfil' => SORT_DESC],
		];
		
		$dataProvider->sort->attributes['idAformularios'] = [
				'asc' => ['AccionesFormularios.claveAccion' => SORT_ASC],
				'desc' => ['AccionesFormularios.claveAccion' => SORT_DESC],
		];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'PerfilAccionFormularioID' => $this->PerfilAccionFormularioID,
           // 'perfilID' => $this->perfilID,
            //'accionFormularioID' => $this->accionFormularioID,
            'activoPerfilAccionFormulario' => $this->activoPerfilAccionFormulario,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);
		
		$query->andWhere(['=', 'PerfilAccionFormulario.regEstado', '1']);
		$query->andFilterWhere(['like', 'Perfiles.nombrePerfil', $this->perfilID]);
		$query->andFilterWhere(['like', 'AccionesFormularios.claveAccion', $this->accionFormularioID]);

        return $dataProvider;
    }
}
