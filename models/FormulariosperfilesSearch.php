<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Formulariosperfiles;

/**
 * FormulariosperfilesSearch represents the model behind the search form of `app\models\Formulariosperfiles`.
 */
class FormulariosperfilesSearch extends Formulariosperfiles
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['permisoFormularioID',  'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['activoPermiso', 'regEstado'], 'boolean'],
            [['regFechaUltimaModificacion', 'perfilID', 'formularioID'], 'safe'],
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
	public $idFomulario;
	
    public function search($params)
    {
        $query = Formulariosperfiles::find();
		$query->joinWith(['idPerfil']);
		$query->joinWith(['idFomulario']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
		
		$dataProvider->sort->attributes['idPerfil'] = [
				'asc' => ['Perfiles.nombrePerfil' => SORT_ASC],
				'desc' => ['Perfiles.nombrePerfil' => SORT_DESC],
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
            'permisoFormularioID' => $this->permisoFormularioID,
            //'perfilID' => $this->perfilID,
            //'formularioID' => $this->formularioID,
            'activoPermiso' => $this->activoPermiso,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);
		
		$query->andWhere(['=', 'PermisosFormulariosPerfiles.regEstado', '1']);
		$query->andFilterWhere(['like', 'Perfiles.nombrePerfil', $this->perfilID]);
		$query->andFilterWhere(['like', 'Formularios.nombreFormulario', $this->formularioID]);

        return $dataProvider;
    }
}
