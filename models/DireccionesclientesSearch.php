<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Direccionesclientes;

/**
 * DireccionesclientesSearch represents the model behind the search form of `app\models\Direccionesclientes`.
 */
class DireccionesclientesSearch extends Direccionesclientes
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['direccionClienteID', 'alias', 'calle', 'numeroInterior', 'numeroExterior', 'codigoPostal', 'colonia', 'localidad', 'referencia', 'municipio', 'estadoID', 'clienteID', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'safe'],
            [['esDefault', 'estadoDireccion', 'regEstado'], 'boolean'],
            [['latitud', 'longitud'], 'number'],
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
public $idEstados; 
public $idClientes; 


    public function search($params)
    {
        $query = Direccionesclientes::find();
		$query->joinWith(['idEstados']);
$query->joinWith(['idClientes']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->sort->attributes['idEstados'] = [
						'asc' => ['Estados.nombreEstado' => SORT_ASC],
						'desc' => ['Estados.nombreEstado' => SORT_DESC],
				];
$dataProvider->sort->attributes['idClientes'] = [
						'asc' => ['Clientes.clienteRazonSocial' => SORT_ASC],
						'desc' => ['Clientes.clienteRazonSocial' => SORT_DESC],
				];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'direccionClienteID' => $this->direccionClienteID,
            'esDefault' => $this->esDefault,
            'latitud' => $this->latitud,
            'longitud' => $this->longitud,
            'estadoDireccion' => $this->estadoDireccion,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'alias', $this->alias])
            ->andFilterWhere(['like', 'calle', $this->calle])
            ->andFilterWhere(['like', 'numeroInterior', $this->numeroInterior])
            ->andFilterWhere(['like', 'numeroExterior', $this->numeroExterior])
            ->andFilterWhere(['like', 'codigoPostal', $this->codigoPostal])
            ->andFilterWhere(['like', 'colonia', $this->colonia])
            ->andFilterWhere(['like', 'localidad', $this->localidad])
            ->andFilterWhere(['like', 'referencia', $this->referencia])
            ->andFilterWhere(['like', 'municipio', $this->municipio]);
		$query->andFilterWhere(['like', 'Estados.nombreEstado', $this->estadoID]);
$query->andFilterWhere(['like', 'Clientes.clienteRazonSocial', $this->clienteID]);
$query->andWhere(['=', 'DireccionesClientes.regEstado', '1']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }

	public function searchelimina($params)
    {
        $query = Direccionesclientes::find();
		$query->joinWith(['idEstados']);
$query->joinWith(['idClientes']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->sort->attributes['idEstados'] = [
						'asc' => ['Estados.nombreEstado' => SORT_ASC],
						'desc' => ['Estados.nombreEstado' => SORT_DESC],
				];
$dataProvider->sort->attributes['idClientes'] = [
						'asc' => ['Clientes.clienteRazonSocial' => SORT_ASC],
						'desc' => ['Clientes.clienteRazonSocial' => SORT_DESC],
				];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'direccionClienteID' => $this->direccionClienteID,
            'esDefault' => $this->esDefault,
            'latitud' => $this->latitud,
            'longitud' => $this->longitud,
            'estadoDireccion' => $this->estadoDireccion,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'alias', $this->alias])
            ->andFilterWhere(['like', 'calle', $this->calle])
            ->andFilterWhere(['like', 'numeroInterior', $this->numeroInterior])
            ->andFilterWhere(['like', 'numeroExterior', $this->numeroExterior])
            ->andFilterWhere(['like', 'codigoPostal', $this->codigoPostal])
            ->andFilterWhere(['like', 'colonia', $this->colonia])
            ->andFilterWhere(['like', 'localidad', $this->localidad])
            ->andFilterWhere(['like', 'referencia', $this->referencia])
            ->andFilterWhere(['like', 'municipio', $this->municipio]);
		$query->andFilterWhere(['like', 'Estados.nombreEstado', $this->estadoID]);
$query->andFilterWhere(['like', 'Clientes.clienteRazonSocial', $this->clienteID]);
$query->andWhere(['=', 'DireccionesClientes.regEstado', '0']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }
}
