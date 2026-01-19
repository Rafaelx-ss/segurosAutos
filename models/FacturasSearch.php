<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Facturas;

/**
 * FacturasSearch represents the model behind the search form of `app\models\Facturas`.
 */
class FacturasSearch extends Facturas
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['facturaID', 'fechaFactura', 'fechaPago', 'observaciones', 'establecimientoID', 'lugarExpedicion', 'direccionEmpresa', 'clienteID', 'direccionCliente', 'metodoPagoID', 'formaPagoID', 'usoCfdiID', 'tipoComprobante', 'serie', 'folio', 'versionTimbrado', 'uuid', 'fechaTimbrado', 'cadenaoriginal', 'sello', 'selloSAT', 'aprobacion', 'certificado', 'pac', 'url_factura', 'tipoRelacionID', 'cfdiRelacionados', 'numeroOperacionPago', 'rfcCuentaBeneficiario', 'cuentaBeneficiario', 'rfcCuentaOrdenante', 'cuentaOrdenante', 'mensageCancelacion', 'fechaSolicitudCancelacion', 'numeroIntentosCancelacion', 'codigoRespuestaCancelacion', 'fechaCancelacion', 'modo', 'tipoMoneda', 'diasCredito', 'estatusPago', 'activoFactura'], 'safe'],
            [['subTotalFactura', 'ivaFactura', 'isrFactura', 'iepsFactura', 'totalFactura', 'tipoCambio'], 'number'],
            [['correoEnviado', 'regEstado'], 'boolean'],
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
public $idFemetodopago; 
public $idFeformapago; 
public $idFeusocfdi; 
public $idEstablecimiento;


    public function search($params)
    {
        $query = Facturas::find();
        $query->joinWith(['idClientes']);
        $query->joinWith(['idFemetodopago']);
        $query->joinWith(['idFeformapago']);
        $query->joinWith(['idFeusocfdi']);
        $query->joinWith(['idEstablecimiento']);
        // add conditions that should always apply here

        //limitar por permisos de usuarios establecimientos, 1
        $userEstablecimientos = $this->getUserEstablecimientos(Yii::$app->user->identity->usuarioID);
        if (empty($userEstablecimientos)) {
            return new ActiveDataProvider([
                'query' => $query->where('1 = 0'), // Esto garantiza que no se devuelvan registros
            ]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // 		$dataProvider->sort->attributes['idEstablecimientos'] = [
        //             'asc' => ['Establecimientos.aliasEstablecimiento' => SORT_ASC],
        //             'desc' => ['Establecimientos.aliasEstablecimiento' => SORT_DESC],
        //     ];
        // $dataProvider->sort->attributes['idClientes'] = [
        // 						'asc' => ['Clientes.nombreComercial' => SORT_ASC],
        // 						'desc' => ['Clientes.nombreComercial' => SORT_DESC],
        // 				];
        // $dataProvider->sort->attributes['idFemetodopago'] = [
        // 						'asc' => ['FEMetodoPago.metodoPago' => SORT_ASC],
        // 						'desc' => ['FEMetodoPago.metodoPago' => SORT_DESC],
        // 				];
        // $dataProvider->sort->attributes['idFeformapago'] = [
        // 						'asc' => ['FEFormaPago.formaPago' => SORT_ASC],
        // 						'desc' => ['FEFormaPago.formaPago' => SORT_DESC],
        // 				];
        // $dataProvider->sort->attributes['fechaFactura'] = [
        // 						'asc' => ['fechaFactura' => SORT_ASC],
        // 						'desc' => ['fechaFactura' => SORT_DESC],
        // 				];

        //limitar por permisos de usuarios establecimientos, 2
        $query->andFilterWhere(['in', 'Facturas.establecimientoID', $userEstablecimientos]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'facturaID' => $this->facturaID,
            'subTotalFactura' => $this->subTotalFactura,
            'ivaFactura' => $this->ivaFactura,
            'isrFactura' => $this->isrFactura,
            'iepsFactura' => $this->iepsFactura,
            'totalFactura' => $this->totalFactura,
            'correoEnviado' => $this->correoEnviado,
            'tipoRelacionID' => $this->tipoRelacionID,
            'numeroIntentosCancelacion' => $this->numeroIntentosCancelacion,
            'tipoCambio' => $this->tipoCambio,
            'diasCredito' => $this->diasCredito,
            'estatusPago' => $this->estatusPago,
            'regEstado' => $this->regEstado,
        ]);

        $query->andFilterWhere(['like', 'observaciones', $this->observaciones])
            ->andFilterWhere(['like', 'lugarExpedicion', $this->lugarExpedicion])
            ->andFilterWhere(['like', 'direccionEmpresa', $this->direccionEmpresa])
            ->andFilterWhere(['like', 'direccionCliente', $this->direccionCliente])
            ->andFilterWhere(['like', 'tipoComprobante', $this->tipoComprobante])
            ->andFilterWhere(['like', 'serie', $this->serie])
            ->andFilterWhere(['like', 'folio', $this->folio])
            ->andFilterWhere(['like', 'versionTimbrado', $this->versionTimbrado])
            ->andFilterWhere(['like', 'uuid', $this->uuid])
            ->andFilterWhere(['like', 'cadenaoriginal', $this->cadenaoriginal])
            ->andFilterWhere(['like', 'sello', $this->sello])
            ->andFilterWhere(['like', 'selloSAT', $this->selloSAT])
            ->andFilterWhere(['like', 'aprobacion', $this->aprobacion])
            ->andFilterWhere(['like', 'certificado', $this->certificado])
            ->andFilterWhere(['like', 'pac', $this->pac])
            ->andFilterWhere(['like', 'url_factura', $this->url_factura])
            ->andFilterWhere(['like', 'cfdiRelacionados', $this->cfdiRelacionados])
            ->andFilterWhere(['like', 'numeroOperacionPago', $this->numeroOperacionPago])
            ->andFilterWhere(['like', 'rfcCuentaBeneficiario', $this->rfcCuentaBeneficiario])
            ->andFilterWhere(['like', 'cuentaBeneficiario', $this->cuentaBeneficiario])
            ->andFilterWhere(['like', 'rfcCuentaOrdenante', $this->rfcCuentaOrdenante])
            ->andFilterWhere(['like', 'cuentaOrdenante', $this->cuentaOrdenante])
            ->andFilterWhere(['like', 'mensageCancelacion', $this->mensageCancelacion])
            ->andFilterWhere(['like', 'codigoRespuestaCancelacion', $this->codigoRespuestaCancelacion])
            ->andFilterWhere(['like', 'modo', $this->modo])
            ->andFilterWhere(['like', 'tipoMoneda', $this->tipoMoneda])
            ->andFilterWhere(['like', 'activoFactura', $this->activoFactura]);
		$query->andFilterWhere(['like', 'fechaFactura', $this->fechaFactura]);
        $query->andFilterWhere(['like', 'fechaPago', $this->fechaPago]);
        $query->andFilterWhere(['like', 'Establecimientos.aliasEstablecimiento', $this->establecimientoID]);
        $query->andFilterWhere(['like', 'Clientes.nombreComercial', $this->clienteID]);
        $query->andFilterWhere(['like', 'FEMetodoPago.metodoPago', $this->metodoPagoID]);
        $query->andFilterWhere(['like', 'FEFormaPago.formaPago', $this->formaPagoID]);
        $query->andFilterWhere(['like', 'FEUsoCFDI.usoCFDI', $this->usoCfdiID]);
        $query->andFilterWhere(['like', 'fechaTimbrado', $this->fechaTimbrado]);
        $query->andFilterWhere(['like', 'fechaSolicitudCancelacion', $this->fechaSolicitudCancelacion]);
        $query->andFilterWhere(['like', 'fechaCancelacion', $this->fechaCancelacion]);
        $query->andWhere(['=', 'Facturas.regEstado', '1']);
        $query->andWhere(['=', 'tipoComprobante', 'Ingreso']);

        $query->orderBy(['fechaFactura' => SORT_DESC]);

        return $dataProvider;		
    
    }

    //limitar por permisos de usuarios establecimientos, 3
    private function getUserEstablecimientos($userId)
    {
        // Aquí iría la lógica para obtener los establecimientos permitidos para el usuario.
        // Ejemplo: Obtener todos los establecimientos asociados al usuario
        return Usuariosestablecimientos::find()
            ->select('establecimientoID')
            ->where(['usuarioID' => $userId])
            ->column();  // Devuelve un array de IDs de establecimientos
    }



	public function searchelimina($params)
    {
        $query = Facturas::find();
		//$query->joinWith(['idEmpresas']);
        $query->joinWith(['idClientes']);
        $query->joinWith(['idFemetodopago']);
        $query->joinWith(['idFeformapago']);
        $query->joinWith(['idFeusocfdi']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		/*$dataProvider->sort->attributes['idEmpresas'] = [
						'asc' => ['Empresas.nombreEmpresa' => SORT_ASC],
						'desc' => ['Empresas.nombreEmpresa' => SORT_DESC],
        // 				];*/
        // $dataProvider->sort->attributes['idClientes'] = [
        // 						'asc' => ['Clientes.nombreComercial' => SORT_ASC],
        // 						'desc' => ['Clientes.nombreComercial' => SORT_DESC],
        // 				];
        // $dataProvider->sort->attributes['idFemetodopago'] = [
        // 						'asc' => ['FEMetodoPago.metodoPago' => SORT_ASC],
        // 						'desc' => ['FEMetodoPago.metodoPago' => SORT_DESC],
        // 				];
        // $dataProvider->sort->attributes['idFeformapago'] = [
        // 						'asc' => ['FEFormaPago.formaPago' => SORT_ASC],
        // 						'desc' => ['FEFormaPago.formaPago' => SORT_DESC],
        // 				];
        // $dataProvider->sort->attributes['idFeusocfdi'] = [
        // 						'asc' => ['FEUsoCFDI.usoCFDI' => SORT_ASC],
        // 						'desc' => ['FEUsoCFDI.usoCFDI' => SORT_DESC],
        // 				];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'facturaID' => $this->facturaID,
            'subTotalFactura' => $this->subTotalFactura,
            'ivaFactura' => $this->ivaFactura,
            'isrFactura' => $this->isrFactura,
            'iepsFactura' => $this->iepsFactura,
            'totalFactura' => $this->totalFactura,
            'correoEnviado' => $this->correoEnviado,
            'tipoRelacionID' => $this->tipoRelacionID,
            'numeroIntentosCancelacion' => $this->numeroIntentosCancelacion,
            'tipoCambio' => $this->tipoCambio,
            'diasCredito' => $this->diasCredito,
            'estatusPago' => $this->estatusPago,
            'regEstado' => $this->regEstado,
        ]);

        $query->andFilterWhere(['like', 'observaciones', $this->observaciones])
            ->andFilterWhere(['like', 'lugarExpedicion', $this->lugarExpedicion])
            ->andFilterWhere(['like', 'direccionEmpresa', $this->direccionEmpresa])
            ->andFilterWhere(['like', 'direccionCliente', $this->direccionCliente])
            ->andFilterWhere(['like', 'tipoComprobante', $this->tipoComprobante])
            ->andFilterWhere(['like', 'serie', $this->serie])
            ->andFilterWhere(['like', 'folio', $this->folio])
            ->andFilterWhere(['like', 'versionTimbrado', $this->versionTimbrado])
            ->andFilterWhere(['like', 'uuid', $this->uuid])
            ->andFilterWhere(['like', 'cadenaoriginal', $this->cadenaoriginal])
            ->andFilterWhere(['like', 'sello', $this->sello])
            ->andFilterWhere(['like', 'selloSAT', $this->selloSAT])
            ->andFilterWhere(['like', 'aprobacion', $this->aprobacion])
            ->andFilterWhere(['like', 'certificado', $this->certificado])
            ->andFilterWhere(['like', 'pac', $this->pac])
            ->andFilterWhere(['like', 'url_factura', $this->url_factura])
            ->andFilterWhere(['like', 'cfdiRelacionados', $this->cfdiRelacionados])
            ->andFilterWhere(['like', 'numeroOperacionPago', $this->numeroOperacionPago])
            ->andFilterWhere(['like', 'rfcCuentaBeneficiario', $this->rfcCuentaBeneficiario])
            ->andFilterWhere(['like', 'cuentaBeneficiario', $this->cuentaBeneficiario])
            ->andFilterWhere(['like', 'rfcCuentaOrdenante', $this->rfcCuentaOrdenante])
            ->andFilterWhere(['like', 'cuentaOrdenante', $this->cuentaOrdenante])
            ->andFilterWhere(['like', 'mensageCancelacion', $this->mensageCancelacion])
            ->andFilterWhere(['like', 'codigoRespuestaCancelacion', $this->codigoRespuestaCancelacion])
            ->andFilterWhere(['like', 'modo', $this->modo])
            ->andFilterWhere(['like', 'tipoMoneda', $this->tipoMoneda])
            ->andFilterWhere(['like', 'activoFactura', $this->activoFactura]);
		$query->andFilterWhere(['like', 'fechaFactura', $this->fechaFactura]);
        $query->andFilterWhere(['like', 'fechaPago', $this->fechaPago]);
        $query->andFilterWhere(['like', 'Empresas.nombreEmpresa', $this->empresaID]);
        $query->andFilterWhere(['like', 'Clientes.nombreComercial', $this->clienteID]);
        $query->andFilterWhere(['like', 'FEMetodoPago.metodoPago', $this->metodoPagoID]);
        $query->andFilterWhere(['like', 'FEFormaPago.formaPago', $this->formaPagoID]);
        $query->andFilterWhere(['like', 'FEUsoCFDI.usoCFDI', $this->usoCfdiID]);
        $query->andFilterWhere(['like', 'fechaTimbrado', $this->fechaTimbrado]);
        $query->andFilterWhere(['like', 'fechaSolicitudCancelacion', $this->fechaSolicitudCancelacion]);
        $query->andFilterWhere(['like', 'fechaCancelacion', $this->fechaCancelacion]);
        $query->andWhere(['=', 'Facturas.regEstado', '0']);

        $query->orderBy(['fechaFactura' => SORT_DESC]);

	    return $dataProvider;		
		
    }
}
