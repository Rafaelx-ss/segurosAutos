<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Conductores;

/**
 * ConductoresSearch represents the model behind the search form of `app\models\Conductores`.
 */
class ConductoresSearch extends Conductores
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['conductorID', 'clienteID', 'nombre', 'apellidoPaterno', 'apellidoMaterno', 'fechaNacimiento', 'genero', 'rfc', 'numeroLicencia', 'tipoLicencia', 'vigenciaLicencia', 'estadoEmisorLicenciaID', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'safe'],
            [['scoreConductor'], 'number'],
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
public $idEstados; 


    public function search($params)
    {
        $query = Conductores::find();
		$query->joinWith(['idClientes']);
$query->joinWith(['idEstados']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->sort->attributes['idClientes'] = [
						'asc' => ['Clientes.clienteRazonSocial' => SORT_ASC],
						'desc' => ['Clientes.clienteRazonSocial' => SORT_DESC],
				];
$dataProvider->sort->attributes['idEstados'] = [
						'asc' => ['Estados.nombreEstado' => SORT_ASC],
						'desc' => ['Estados.nombreEstado' => SORT_DESC],
				];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'conductorID' => $this->conductorID,
            'scoreConductor' => $this->scoreConductor,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'nombre', $this->nombre])
            ->andFilterWhere(['like', 'apellidoPaterno', $this->apellidoPaterno])
            ->andFilterWhere(['like', 'apellidoMaterno', $this->apellidoMaterno])
            ->andFilterWhere(['like', 'genero', $this->genero])
            ->andFilterWhere(['like', 'rfc', $this->rfc])
            ->andFilterWhere(['like', 'numeroLicencia', $this->numeroLicencia])
            ->andFilterWhere(['like', 'tipoLicencia', $this->tipoLicencia]);
		$query->andFilterWhere(['like', 'Clientes.clienteRazonSocial', $this->clienteID]);
$query->andFilterWhere(['like', 'fechaNacimiento', $this->fechaNacimiento]);
$query->andFilterWhere(['like', 'vigenciaLicencia', $this->vigenciaLicencia]);
$query->andFilterWhere(['like', 'Estados.nombreEstado', $this->estadoEmisorLicenciaID]);
$query->andWhere(['=', 'Conductores.regEstado', '1']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }

	public function searchelimina($params)
    {
        $query = Conductores::find();
		$query->joinWith(['idClientes']);
$query->joinWith(['idEstados']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->sort->attributes['idClientes'] = [
						'asc' => ['Clientes.clienteRazonSocial' => SORT_ASC],
						'desc' => ['Clientes.clienteRazonSocial' => SORT_DESC],
				];
$dataProvider->sort->attributes['idEstados'] = [
						'asc' => ['Estados.nombreEstado' => SORT_ASC],
						'desc' => ['Estados.nombreEstado' => SORT_DESC],
				];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'conductorID' => $this->conductorID,
            'scoreConductor' => $this->scoreConductor,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'nombre', $this->nombre])
            ->andFilterWhere(['like', 'apellidoPaterno', $this->apellidoPaterno])
            ->andFilterWhere(['like', 'apellidoMaterno', $this->apellidoMaterno])
            ->andFilterWhere(['like', 'genero', $this->genero])
            ->andFilterWhere(['like', 'rfc', $this->rfc])
            ->andFilterWhere(['like', 'numeroLicencia', $this->numeroLicencia])
            ->andFilterWhere(['like', 'tipoLicencia', $this->tipoLicencia]);
		$query->andFilterWhere(['like', 'Clientes.clienteRazonSocial', $this->clienteID]);
$query->andFilterWhere(['like', 'fechaNacimiento', $this->fechaNacimiento]);
$query->andFilterWhere(['like', 'vigenciaLicencia', $this->vigenciaLicencia]);
$query->andFilterWhere(['like', 'Estados.nombreEstado', $this->estadoEmisorLicenciaID]);
$query->andWhere(['=', 'Conductores.regEstado', '0']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }
}
