<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Infracciones;

/**
 * InfraccionesSearch represents the model behind the search form of `app\models\Infracciones`.
 */
class InfraccionesSearch extends Infracciones
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['infraccionID', 'accidenteID', 'polizaID', 'conductorID', 'tipoInfraccion', 'fechaInfraccion', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'safe'],
            [['montoMulta'], 'number'],
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
public $idAccidentes; 
public $idPolizas; 
public $idConductores; 


    public function search($params)
    {
        $query = Infracciones::find();
		$query->joinWith(['idAccidentes']);
$query->joinWith(['idPolizas']);
$query->joinWith(['idConductores']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->sort->attributes['idAccidentes'] = [
						'asc' => ['Accidentes.descripcion' => SORT_ASC],
						'desc' => ['Accidentes.descripcion' => SORT_DESC],
				];
$dataProvider->sort->attributes['idPolizas'] = [
						'asc' => ['Polizas.numeroPoliza' => SORT_ASC],
						'desc' => ['Polizas.numeroPoliza' => SORT_DESC],
				];
$dataProvider->sort->attributes['idConductores'] = [
						'asc' => ['Conductores.nombre' => SORT_ASC],
						'desc' => ['Conductores.nombre' => SORT_DESC],
				];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'infraccionID' => $this->infraccionID,
            'montoMulta' => $this->montoMulta,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'tipoInfraccion', $this->tipoInfraccion]);
		$query->andFilterWhere(['like', 'Accidentes.descripcion', $this->accidenteID]);
$query->andFilterWhere(['like', 'Polizas.numeroPoliza', $this->polizaID]);
$query->andFilterWhere(['like', 'Conductores.nombre', $this->conductorID]);
$query->andFilterWhere(['like', 'fechaInfraccion', $this->fechaInfraccion]);
$query->andWhere(['=', 'Infracciones.regEstado', '1']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }

	public function searchelimina($params)
    {
        $query = Infracciones::find();
		$query->joinWith(['idAccidentes']);
$query->joinWith(['idPolizas']);
$query->joinWith(['idConductores']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->sort->attributes['idAccidentes'] = [
						'asc' => ['Accidentes.descripcion' => SORT_ASC],
						'desc' => ['Accidentes.descripcion' => SORT_DESC],
				];
$dataProvider->sort->attributes['idPolizas'] = [
						'asc' => ['Polizas.numeroPoliza' => SORT_ASC],
						'desc' => ['Polizas.numeroPoliza' => SORT_DESC],
				];
$dataProvider->sort->attributes['idConductores'] = [
						'asc' => ['Conductores.nombre' => SORT_ASC],
						'desc' => ['Conductores.nombre' => SORT_DESC],
				];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'infraccionID' => $this->infraccionID,
            'montoMulta' => $this->montoMulta,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'tipoInfraccion', $this->tipoInfraccion]);
		$query->andFilterWhere(['like', 'Accidentes.descripcion', $this->accidenteID]);
$query->andFilterWhere(['like', 'Polizas.numeroPoliza', $this->polizaID]);
$query->andFilterWhere(['like', 'Conductores.nombre', $this->conductorID]);
$query->andFilterWhere(['like', 'fechaInfraccion', $this->fechaInfraccion]);
$query->andWhere(['=', 'Infracciones.regEstado', '0']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }
}
