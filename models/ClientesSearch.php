<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Clientes;

/**
 * ClientesSearch represents the model behind the search form of `app\models\Clientes`.
 */
class ClientesSearch extends Clientes
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['clienteID', 'nombreComercial', 'clienteRazonSocial', 'clienteRFC', 'clienteTelefono', 'clienteEmail', 'grupoClienteID', 'cuentaContable', 'establecimientoProvisiona', 'tipoClienteID', 'afectaSaldoRem', 'cuentafactura', 'metodoPagoID', 'formaPagoID', 'UsoCFDIID', 'clienteGrupoFacturacionID', 'condicionesPago', 'regimenFiscalID', 'codigoPostalCliente', 'clienteTipoPersona', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'safe'],
            [['vpc'], 'number'],
            [['validarSaldo', 'estadoCliente', 'regEstado'], 'boolean'],
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
public $idFeregimenfiscal; 
public $idTiposclientes; 


    public function search($params)
    {
        $query = Clientes::find();
		$query->joinWith(['idFeregimenfiscal']);
$query->joinWith(['idTiposclientes']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->sort->attributes['idFeregimenfiscal'] = [
						'asc' => ['FERegimenFiscal.nombreRegimenFiscal' => SORT_ASC],
						'desc' => ['FERegimenFiscal.nombreRegimenFiscal' => SORT_DESC],
				];
$dataProvider->sort->attributes['idTiposclientes'] = [
						'asc' => ['TiposClientes.tipoClienteDescripcion' => SORT_ASC],
						'desc' => ['TiposClientes.tipoClienteDescripcion' => SORT_DESC],
				];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'clienteID' => $this->clienteID,
            'vpc' => $this->vpc,
            'grupoClienteID' => $this->grupoClienteID,
            'metodoPagoID' => $this->metodoPagoID,
            'formaPagoID' => $this->formaPagoID,
            'UsoCFDIID' => $this->UsoCFDIID,
            'clienteGrupoFacturacionID' => $this->clienteGrupoFacturacionID,
            'validarSaldo' => $this->validarSaldo,
            'estadoCliente' => $this->estadoCliente,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'nombreComercial', $this->nombreComercial])
            ->andFilterWhere(['like', 'clienteRazonSocial', $this->clienteRazonSocial])
            ->andFilterWhere(['like', 'clienteRFC', $this->clienteRFC])
            ->andFilterWhere(['like', 'clienteTelefono', $this->clienteTelefono])
            ->andFilterWhere(['like', 'clienteEmail', $this->clienteEmail])
            ->andFilterWhere(['like', 'cuentaContable', $this->cuentaContable])
            ->andFilterWhere(['like', 'establecimientoProvisiona', $this->establecimientoProvisiona])
            ->andFilterWhere(['like', 'afectaSaldoRem', $this->afectaSaldoRem])
            ->andFilterWhere(['like', 'cuentafactura', $this->cuentafactura])
            ->andFilterWhere(['like', 'condicionesPago', $this->condicionesPago])
            ->andFilterWhere(['like', 'codigoPostalCliente', $this->codigoPostalCliente])
            ->andFilterWhere(['like', 'clienteTipoPersona', $this->clienteTipoPersona]);
		$query->andFilterWhere(['like', 'FERegimenFiscal.nombreRegimenFiscal', $this->regimenFiscalID]);
$query->andFilterWhere(['like', 'TiposClientes.tipoClienteDescripcion', $this->tipoClienteID]);
$query->andWhere(['=', 'Clientes.regEstado', '1']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }

	public function searchelimina($params)
    {
        $query = Clientes::find();
		$query->joinWith(['idFeregimenfiscal']);
$query->joinWith(['idTiposclientes']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->sort->attributes['idFeregimenfiscal'] = [
						'asc' => ['FERegimenFiscal.nombreRegimenFiscal' => SORT_ASC],
						'desc' => ['FERegimenFiscal.nombreRegimenFiscal' => SORT_DESC],
				];
$dataProvider->sort->attributes['idTiposclientes'] = [
						'asc' => ['TiposClientes.tipoClienteDescripcion' => SORT_ASC],
						'desc' => ['TiposClientes.tipoClienteDescripcion' => SORT_DESC],
				];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'clienteID' => $this->clienteID,
            'vpc' => $this->vpc,
            'grupoClienteID' => $this->grupoClienteID,
            'metodoPagoID' => $this->metodoPagoID,
            'formaPagoID' => $this->formaPagoID,
            'UsoCFDIID' => $this->UsoCFDIID,
            'clienteGrupoFacturacionID' => $this->clienteGrupoFacturacionID,
            'validarSaldo' => $this->validarSaldo,
            'estadoCliente' => $this->estadoCliente,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'nombreComercial', $this->nombreComercial])
            ->andFilterWhere(['like', 'clienteRazonSocial', $this->clienteRazonSocial])
            ->andFilterWhere(['like', 'clienteRFC', $this->clienteRFC])
            ->andFilterWhere(['like', 'clienteTelefono', $this->clienteTelefono])
            ->andFilterWhere(['like', 'clienteEmail', $this->clienteEmail])
            ->andFilterWhere(['like', 'cuentaContable', $this->cuentaContable])
            ->andFilterWhere(['like', 'establecimientoProvisiona', $this->establecimientoProvisiona])
            ->andFilterWhere(['like', 'afectaSaldoRem', $this->afectaSaldoRem])
            ->andFilterWhere(['like', 'cuentafactura', $this->cuentafactura])
            ->andFilterWhere(['like', 'condicionesPago', $this->condicionesPago])
            ->andFilterWhere(['like', 'codigoPostalCliente', $this->codigoPostalCliente])
            ->andFilterWhere(['like', 'clienteTipoPersona', $this->clienteTipoPersona]);
		$query->andFilterWhere(['like', 'FERegimenFiscal.nombreRegimenFiscal', $this->regimenFiscalID]);
$query->andFilterWhere(['like', 'TiposClientes.tipoClienteDescripcion', $this->tipoClienteID]);
$query->andWhere(['=', 'Clientes.regEstado', '0']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }
}
