<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Aformulariosperfiles;

/**
 * AformulariosperfilesSearch represents the model behind the search form of `app\models\Aformulariosperfiles`.
 */
class AformulariosperfilesSearch extends Aformulariosperfiles
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['permisoAccionID', 'permisoFormularioID', 'accionFormularioID', 'perfilID', 'establecimientoID', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'safe'],
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
	public $idPformulario;
	public $idAformulario;
	
    public function search($params)
    {
        $query = Aformulariosperfiles::find();
		$query->joinWith(['idPerfil']);
		$query->joinWith(['idPformulario']);
		$query->joinWith(['idAformulario']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
		
		$dataProvider->sort->attributes['idPerfil'] = [
				'asc' => ['Perfiles.nombrePerfil' => SORT_ASC],
				'desc' => ['Perfiles.nombrePerfil' => SORT_DESC],
		];
		
		$dataProvider->sort->attributes['idPformulario'] = [
				'asc' => ['PermisosFormulariosPerfiles.permisoFormularioID' => SORT_ASC],
				'desc' => ['PermisosFormulariosPerfiles.permisoFormularioID' => SORT_DESC],
		];
		
		$dataProvider->sort->attributes['idAformulario'] = [
				'asc' => ['AccionesFormularios.accionFormularioID' => SORT_ASC],
				'desc' => ['AccionesFormularios.accionFormularioID' => SORT_DESC],
		];
		

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'permisoAccionID' => $this->permisoAccionID,
           // 'permisoFormularioID' => $this->permisoFormularioID,
           // 'accionFormularioID' => $this->accionFormularioID,
           // 'perfilID' => $this->perfilID,
            'establecimientoID' => $this->establecimientoID,
            'activoPermiso' => $this->activoPermiso,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);
		
		$query->andWhere(['=', 'PermisosAccionesFormulariosPerfiles.regEstado', '1']);
		$query->andFilterWhere(['like', 'Perfiles.nombrePerfil', $this->perfilID]);
		$query->andFilterWhere(['like', 'PermisosFormulariosPerfiles.permisoFormularioID', $this->permisoFormularioID]);
		$query->andFilterWhere(['like', 'AccionesFormularios.accionFormularioID', $this->accionFormularioID]);
        return $dataProvider;
    }
}
