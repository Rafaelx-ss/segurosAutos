<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Accidentes;

/**
 * AccidentesSearch represents the model behind the search form of `app\models\Accidentes`.
 */
class AccidentesSearch extends Accidentes
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['accidenteID', 'polizaID', 'vehiculoID', 'conductorID', 'municipioID', 'fechaHoraAccidente', 'descripcion', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'safe'],
            [['latitud', 'longitud'], 'number'],
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
public $idPolizas; 
public $idVehiculos; 
public $idConductores; 
public $idMunicipios; 


    public function search($params)
    {
        $query = Accidentes::find();
		$query->joinWith(['idPolizas']);
$query->joinWith(['idVehiculos']);
$query->joinWith(['idConductores']);
$query->joinWith(['idMunicipios']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->sort->attributes['idPolizas'] = [
						'asc' => ['Polizas.numeroPoliza' => SORT_ASC],
						'desc' => ['Polizas.numeroPoliza' => SORT_DESC],
				];
$dataProvider->sort->attributes['idVehiculos'] = [
						'asc' => ['Vehiculos.placa' => SORT_ASC],
						'desc' => ['Vehiculos.placa' => SORT_DESC],
				];
$dataProvider->sort->attributes['idConductores'] = [
						'asc' => ['Conductores.nombre' => SORT_ASC],
						'desc' => ['Conductores.nombre' => SORT_DESC],
				];
$dataProvider->sort->attributes['idMunicipios'] = [
						'asc' => ['Municipios.nombreMunicipio' => SORT_ASC],
						'desc' => ['Municipios.nombreMunicipio' => SORT_DESC],
				];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'accidenteID' => $this->accidenteID,
            'latitud' => $this->latitud,
            'longitud' => $this->longitud,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'descripcion', $this->descripcion]);
		$query->andFilterWhere(['like', 'Polizas.numeroPoliza', $this->polizaID]);
$query->andFilterWhere(['like', 'Vehiculos.placa', $this->vehiculoID]);
$query->andFilterWhere(['like', 'Conductores.nombre', $this->conductorID]);
$query->andFilterWhere(['like', 'Municipios.nombreMunicipio', $this->municipioID]);
$query->andFilterWhere(['like', 'fechaHoraAccidente', $this->fechaHoraAccidente]);
$query->andWhere(['=', 'Accidentes.regEstado', '1']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }

	public function searchelimina($params)
    {
        $query = Accidentes::find();
		$query->joinWith(['idPolizas']);
$query->joinWith(['idVehiculos']);
$query->joinWith(['idConductores']);
$query->joinWith(['idMunicipios']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->sort->attributes['idPolizas'] = [
						'asc' => ['Polizas.numeroPoliza' => SORT_ASC],
						'desc' => ['Polizas.numeroPoliza' => SORT_DESC],
				];
$dataProvider->sort->attributes['idVehiculos'] = [
						'asc' => ['Vehiculos.placa' => SORT_ASC],
						'desc' => ['Vehiculos.placa' => SORT_DESC],
				];
$dataProvider->sort->attributes['idConductores'] = [
						'asc' => ['Conductores.nombre' => SORT_ASC],
						'desc' => ['Conductores.nombre' => SORT_DESC],
				];
$dataProvider->sort->attributes['idMunicipios'] = [
						'asc' => ['Municipios.nombreMunicipio' => SORT_ASC],
						'desc' => ['Municipios.nombreMunicipio' => SORT_DESC],
				];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'accidenteID' => $this->accidenteID,
            'latitud' => $this->latitud,
            'longitud' => $this->longitud,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'descripcion', $this->descripcion]);
		$query->andFilterWhere(['like', 'Polizas.numeroPoliza', $this->polizaID]);
$query->andFilterWhere(['like', 'Vehiculos.placa', $this->vehiculoID]);
$query->andFilterWhere(['like', 'Conductores.nombre', $this->conductorID]);
$query->andFilterWhere(['like', 'Municipios.nombreMunicipio', $this->municipioID]);
$query->andFilterWhere(['like', 'fechaHoraAccidente', $this->fechaHoraAccidente]);
$query->andWhere(['=', 'Accidentes.regEstado', '0']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }
}
