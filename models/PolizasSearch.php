<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Polizas;

/**
 * PolizasSearch represents the model behind the search form of `app\models\Polizas`.
 */
class PolizasSearch extends Polizas
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['polizaID', 'clienteID', 'vehiculoID', 'numeroPoliza', 'fechaCompra', 'fechaVencimiento', 'estadoPoliza', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'safe'],
            [['montoCobertura', 'costoPoliza'], 'number'],
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
public $idClientes; 
public $idVehiculos; 


    public function search($params)
    {
        $query = Polizas::find();
		$query->joinWith(['idClientes']);
$query->joinWith(['idVehiculos']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->sort->attributes['idClientes'] = [
						'asc' => ['Clientes.clienteRazonSocial' => SORT_ASC],
						'desc' => ['Clientes.clienteRazonSocial' => SORT_DESC],
				];
$dataProvider->sort->attributes['idVehiculos'] = [
						'asc' => ['Vehiculos.placa' => SORT_ASC],
						'desc' => ['Vehiculos.placa' => SORT_DESC],
				];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'polizaID' => $this->polizaID,
            'montoCobertura' => $this->montoCobertura,
            'costoPoliza' => $this->costoPoliza,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'numeroPoliza', $this->numeroPoliza])
            ->andFilterWhere(['like', 'estadoPoliza', $this->estadoPoliza]);
		$query->andFilterWhere(['like', 'Clientes.clienteRazonSocial', $this->clienteID]);
$query->andFilterWhere(['like', 'Vehiculos.placa', $this->vehiculoID]);
$query->andFilterWhere(['like', 'fechaCompra', $this->fechaCompra]);
$query->andFilterWhere(['like', 'fechaVencimiento', $this->fechaVencimiento]);
$query->andWhere(['=', 'Polizas.regEstado', '1']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }

	public function searchelimina($params)
    {
        $query = Polizas::find();
		$query->joinWith(['idClientes']);
$query->joinWith(['idVehiculos']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->sort->attributes['idClientes'] = [
						'asc' => ['Clientes.clienteRazonSocial' => SORT_ASC],
						'desc' => ['Clientes.clienteRazonSocial' => SORT_DESC],
				];
$dataProvider->sort->attributes['idVehiculos'] = [
						'asc' => ['Vehiculos.placa' => SORT_ASC],
						'desc' => ['Vehiculos.placa' => SORT_DESC],
				];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'polizaID' => $this->polizaID,
            'montoCobertura' => $this->montoCobertura,
            'costoPoliza' => $this->costoPoliza,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'numeroPoliza', $this->numeroPoliza])
            ->andFilterWhere(['like', 'estadoPoliza', $this->estadoPoliza]);
		$query->andFilterWhere(['like', 'Clientes.clienteRazonSocial', $this->clienteID]);
$query->andFilterWhere(['like', 'Vehiculos.placa', $this->vehiculoID]);
$query->andFilterWhere(['like', 'fechaCompra', $this->fechaCompra]);
$query->andFilterWhere(['like', 'fechaVencimiento', $this->fechaVencimiento]);
$query->andWhere(['=', 'Polizas.regEstado', '0']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }
}
