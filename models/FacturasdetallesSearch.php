<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Facturasdetalles;

/**
 * FacturasdetallesSearch represents the model behind the search form of `app\models\Facturasdetalles`.
 */
class FacturasdetallesSearch extends Facturasdetalles
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        if(isset($_GET['r'])){

            $array = explode('/', $_GET['r']);
            if($array[0] == 'facturasdetalles' ){

                return [
                    [['facturaID', 'fechaFactura', 'fechaPago', 'observaciones', 'establecimientoID', 'lugarExpedicion', 'direccionEmpresa', 'clienteID', 'direccionCliente', 'metodoPagoID', 'formaPagoID', 'usoCfdiID', 'tipoComprobante', 'serie', 'folio', 'versionTimbrado', 'uuid', 'fechaTimbrado', 'cadenaoriginal', 'sello', 'selloSAT', 'aprobacion', 'certificado', 'pac', 'url_factura', 'tipoRelacionID', 'cfdiRelacionados', 'numeroOperacionPago', 'rfcCuentaBeneficiario', 'cuentaBeneficiario', 'rfcCuentaOrdenante', 'cuentaOrdenante', 'mensageCancelacion', 'fechaSolicitudCancelacion', 'numeroIntentosCancelacion', 'codigoRespuestaCancelacion', 'fechaCancelacion', 'modo', 'tipoMoneda', 'diasCredito', 'estatusPago', 'activoFactura'], 'safe'],
                    [['subTotalFactura', 'ivaFactura', 'isrFactura', 'iepsFactura', 'totalFactura', 'tipoCambio'], 'number'],
                    [['correoEnviado', 'regEstado'], 'boolean'],
                ];

            }

        }
        return [
            [['facturaDetalleID', 'facturaID', 'productoID', 'almacenID', 'nombreProducto', 'unidad', 'conceptoFacturado', 'facturaRelacionadaID', 'numeroParcialidad'], 'safe'],
            [['cantidadDetalleFactura', 'precioDetalleFactura', 'subTotalDetalleFactura', 'totalDetalleFactura', 'ivaDetalleFactura', 'iepsDetalleFactura', 'isrDetalleFactura', 'saldoAnterior', 'importePagado', 'saldoInsoluto'], 'number'],
            [['activoFacturaDetalle', 'estadoFacturaDetalle', 'regEstado'], 'boolean'],
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


     public $idCliente;
     public $idEstablecimiento;
     public $idFormaPago;
     public $idMetodoPago;
     public $idUsoCfdi;
     public $idTipoComprobante;
 
    public function search($params)
    {
        $query = Facturasdetalles::find();
        $validaPermisos = false;
        if(isset($_GET['r'])){
            $array = explode('/', $_GET['r']);
            if($array[0] == 'facturasdetalles' ){
                $query = Facturas::find();
                $validaPermisos = true;
            }
        }
		$query->joinWith(['idCliente']);
		$query->joinWith(['idEstablecimiento']);
		// $query->joinWith(['idFormaPago']);
		// $query->joinWith(['idMetodoPago']);
		// $query->joinWith(['idUsoCfdi']);
		// $query->joinWith(['idTipoComprobante']);
		// add conditions that should always apply here

        if($validaPermisos){
            //limitar por permisos de usuarios establecimientos, 1
            $userEstablecimientos = $this->getUserEstablecimientos(Yii::$app->user->identity->usuarioID);
            if (empty($userEstablecimientos)) {
                return new ActiveDataProvider([
                    'query' => $query->where('1 = 0'), // Esto garantiza que no se devuelvan registros
                ]);
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if($validaPermisos){
            //limitar por permisos de usuarios establecimientos, 2
            $query->andFilterWhere(['in', 'Facturas.establecimientoID', $userEstablecimientos]);
        }

		
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }



        if(isset($_GET['r'])){
            $array = explode('/', $_GET['r']);
            if($array[0] == 'facturasdetalles' ){

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

            }

        }else{

            // grid filtering conditions
            $query->andFilterWhere([
                'facturaDetalleID' => $this->facturaDetalleID,
                'facturaID' => $this->facturaID,
                'productoID' => $this->productoID,
                'almacenID' => $this->almacenID,
                'cantidadDetalleFactura' => $this->cantidadDetalleFactura,
                'precioDetalleFactura' => $this->precioDetalleFactura,
                'subTotalDetalleFactura' => $this->subTotalDetalleFactura,
                'totalDetalleFactura' => $this->totalDetalleFactura,
                'ivaDetalleFactura' => $this->ivaDetalleFactura,
                'iepsDetalleFactura' => $this->iepsDetalleFactura,
                'isrDetalleFactura' => $this->isrDetalleFactura,
                'facturaRelacionadaID' => $this->facturaRelacionadaID,
                'saldoAnterior' => $this->saldoAnterior,
                'importePagado' => $this->importePagado,
                'saldoInsoluto' => $this->saldoInsoluto,
                'numeroParcialidad' => $this->numeroParcialidad,
                'activoFacturaDetalle' => $this->activoFacturaDetalle,
                'estadoFacturaDetalle' => $this->estadoFacturaDetalle,
                'regEstado' => $this->regEstado,
            ]);

            $query->andFilterWhere(['like', 'nombreProducto', $this->nombreProducto])
            ->andFilterWhere(['like', 'unidad', $this->unidad])
            ->andFilterWhere(['like', 'conceptoFacturado', $this->conceptoFacturado]);
            $query->andWhere(['=', 'FacturasDetalles.regEstado', '1']);


        }
        $query->andFilterWhere(['like', 'Clientes.razonSocial', $this->clienteID]);
        $query->andFilterWhere(['like', 'Establecimientos.aliasEstablecimiento', $this->establecimientoID]);// razonSocialEstablecimiento


     

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

        $query->andWhere(['=', 'tipoComprobante', 'P']);



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
        $query = Facturasdetalles::find();
		        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if(isset($_GET['r'])){
            $array = explode('/', $_GET['r']);
            if($array[0] == 'facturasdetalles' ){

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

            }



        }

        // grid filtering conditions
        $query->andFilterWhere([
            'facturaDetalleID' => $this->facturaDetalleID,
            'facturaID' => $this->facturaID,
            'productoID' => $this->productoID,
            'almacenID' => $this->almacenID,
            'cantidadDetalleFactura' => $this->cantidadDetalleFactura,
            'precioDetalleFactura' => $this->precioDetalleFactura,
            'subTotalDetalleFactura' => $this->subTotalDetalleFactura,
            'totalDetalleFactura' => $this->totalDetalleFactura,
            'ivaDetalleFactura' => $this->ivaDetalleFactura,
            'iepsDetalleFactura' => $this->iepsDetalleFactura,
            'isrDetalleFactura' => $this->isrDetalleFactura,
            'facturaRelacionadaID' => $this->facturaRelacionadaID,
            'saldoAnterior' => $this->saldoAnterior,
            'importePagado' => $this->importePagado,
            'saldoInsoluto' => $this->saldoInsoluto,
            'numeroParcialidad' => $this->numeroParcialidad,
            'activoFacturaDetalle' => $this->activoFacturaDetalle,
            'estadoFacturaDetalle' => $this->estadoFacturaDetalle,
            'regEstado' => $this->regEstado,
        ]);

        $query->andFilterWhere(['like', 'nombreProducto', $this->nombreProducto])
            ->andFilterWhere(['like', 'unidad', $this->unidad])
            ->andFilterWhere(['like', 'conceptoFacturado', $this->conceptoFacturado]);
		$query->andWhere(['=', 'FacturasDetalles.regEstado', '0']);


	return $dataProvider;		
		
    }
}
