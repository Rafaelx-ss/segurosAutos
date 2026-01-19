<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "FacturasDetalles".
 *
 * @property int $facturaDetalleID
 * @property int $facturaID
 * @property int $productoID
 * @property int $almacenID
 * @property string $nombreProducto
 * @property string $unidad
 * @property string $conceptoFacturado
 * @property double $cantidadDetalleFactura
 * @property double $precioDetalleFactura
 * @property double $subTotalDetalleFactura
 * @property double $totalDetalleFactura
 * @property double $ivaDetalleFactura
 * @property double $iepsDetalleFactura
 * @property double $isrDetalleFactura
 * @property int $facturaRelacionadaID
 * @property double $saldoAnterior
 * @property double $importePagado
 * @property double $saldoInsoluto
 * @property int $numeroParcialidad
 * @property bool $activoFacturaDetalle
 * @property bool $estadoFacturaDetalle
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 */
class Facturasdetalles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        $nombreModelo= "FacturasDetalles";
        if(isset($_GET["r"])) {
            $array = explode('/',$_GET['r']);
            if($array[0] == 'facturasdetalles') {
                $nombreModelo= "Facturas";
            }
        }
        return $nombreModelo;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        if(isset($_GET['r'])){
            $array = explode('/', $_GET['r']);
            if($array[0] == 'facturasdetalles') {
                return [
                    [['cotizacionID', 'establecimientoID', 'clienteID', 'metodoPagoID', 'formaPagoID', 'usoCfdiID', 'tipoRelacionID', 'numeroIntentosCancelacion', 'diasCredito', 'estatusPago'], 'integer'],
                    [['fechaFactura', 'fechaPago', 'fechaTimbrado', 'activoFactura', 'fechaSolicitudCancelacion', 'fechaCancelacion'], 'safe'],
                    [['subTotalFactura', 'ivaFactura', 'isrFactura', 'iepsFactura', 'totalFactura', 'tipoCambio'], 'number'],
                    [['observaciones', 'uuid', 'cadenaoriginal', 'sello', 'selloSAT', 'aprobacion', 'certificado', 'cfdiRelacionados'], 'string'],
                    [['correoEnviado', 'regEstado'], 'boolean'],
                    [['lugarExpedicion', 'serie', 'folio', 'versionTimbrado', 'rfcCuentaBeneficiario', 'rfcCuentaOrdenante'], 'string', 'max' => 20],
                    [['direccionEmpresa', 'direccionCliente', 'url_factura'], 'string', 'max' => 255],
                    [['tipoComprobante', 'modo'], 'string', 'max' => 10],
                    [['pac', 'numeroOperacionPago'], 'string', 'max' => 100],
                    [['cuentaBeneficiario', 'cuentaOrdenante'], 'string', 'max' => 250],
                    [['mensageCancelacion'], 'string', 'max' => 500],
                    [['codigoRespuestaCancelacion'], 'string', 'max' => 200],
                    [['tipoMoneda'], 'string', 'max' => 5],
                ];
            }
        }

        return [
            [['facturaID', 'productoID', 'almacenID', 'facturaRelacionadaID', 'numeroParcialidad', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['cantidadDetalleFactura', 'precioDetalleFactura', 'subTotalDetalleFactura', 'totalDetalleFactura', 'ivaDetalleFactura', 'iepsDetalleFactura', 'isrDetalleFactura', 'saldoAnterior', 'importePagado', 'saldoInsoluto'], 'number'],
            [['activoFacturaDetalle', 'estadoFacturaDetalle', 'regEstado'], 'boolean'],
            [['nombreProducto', 'unidad'], 'string', 'max' => 250],
            [['conceptoFacturado'], 'string', 'max' => 500],
            [['regFechaUltimaModificacion'], 'safe'],
            // Valores por defecto para campos de control
            [['regUsuarioUltimaModificacion'], 'default', 'value' => 1],
            [['regFormularioUltimaModificacion'], 'default', 'value' => 147],
            [['regVersionUltimaModificacion'], 'default', 'value' => 1],
            // Foreign key constraints
            [['regUsuarioUltimaModificacion'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::className(), 'targetAttribute' => ['regUsuarioUltimaModificacion' => 'usuarioID']],
            [['regFormularioUltimaModificacion'], 'exist', 'skipOnError' => true, 'targetClass' => Formularios::className(), 'targetAttribute' => ['regFormularioUltimaModificacion' => 'formularioID']],
            [['regVersionUltimaModificacion'], 'exist', 'skipOnError' => true, 'targetClass' => Versiones::className(), 'targetAttribute' => ['regVersionUltimaModificacion' => 'versionID']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        if(isset($_GET['r'])){
            $array = explode('/', $_GET['r']);
            if($array[0] == 'facturasdetalles'){
                return [
                    'facturaID' => 'Factura ID',
                    'cotizacionID' => 'Cotizacion ID',
                    'fechaFactura' => 'Fecha Factura',
                    'fechaPago' => 'Fecha Pago',
                    'subTotalFactura' => 'Sub Total Factura',
                    'ivaFactura' => 'Iva Factura',
                    'isrFactura' => 'Isr Factura',
                    'iepsFactura' => 'Ieps Factura',
                    'totalFactura' => 'Total Factura',
                    'observaciones' => 'Observaciones',
                    'establecimientoID' => 'Establecimiento',
                    'lugarExpedicion' => 'Lugar Expedicion',
                    'direccionEmpresa' => 'Direccion Empresa',
                    'clienteID' => 'Cliente',
                    'direccionCliente' => 'Direccion Cliente',
                    'metodoPagoID' => 'Metodo Pago ID',
                    'formaPagoID' => 'Forma Pago ID',
                    'usoCfdiID' => 'Uso Cfdi ID',
                    'tipoComprobante' => 'Tipo Comprobante',
                    'serie' => 'Serie',
                    'folio' => 'Folio',
                    'versionTimbrado' => 'Version Timbrado',
                    'uuid' => 'Uuid',
                    'fechaTimbrado' => 'Fecha Timbrado',
                    'cadenaoriginal' => 'Cadenaoriginal',
                    'sello' => 'Sello',
                    'selloSAT' => 'Sello Sat',
                    'aprobacion' => 'Aprobacion',
                    'certificado' => 'Certificado',
                    'pac' => 'Pac',
                    'correoEnviado' => 'Correo Enviado',
                    'url_factura' => 'Url Factura',
                    'tipoRelacionID' => 'Tipo Relacion ID',
                    'cfdiRelacionados' => 'Cfdi Relacionados',
                    'numeroOperacionPago' => 'Numero Operacion Pago',
                    'rfcCuentaBeneficiario' => 'Rfc Cuenta Beneficiario',
                    'cuentaBeneficiario' => 'Cuenta Beneficiario',
                    'rfcCuentaOrdenante' => 'Rfc Cuenta Ordenante',
                    'cuentaOrdenante' => 'Cuenta Ordenante',
                    'mensageCancelacion' => 'Mensage Cancelacion',
                    'fechaSolicitudCancelacion' => 'Fecha Solicitud Cancelacion',
                    'numeroIntentosCancelacion' => 'Numero Intentos Cancelacion',
                    'codigoRespuestaCancelacion' => 'Codigo Respuesta Cancelacion',
                    'fechaCancelacion' => 'Fecha Cancelacion',
                    'modo' => 'Modo',
                    'tipoMoneda' => 'Tipo Moneda',
                    'tipoCambio' => 'Tipo Cambio',
                    'diasCredito' => 'Dias Credito',
                    'estatusPago' => 'Estatus Pago',
                    'activoFactura' => 'Estatus',
                    'regEstado' => 'Reg Estado',
                ];
            }
        }
        
        return [
            'facturaDetalleID' => 'Factura Detalle ID',
            'facturaID' => 'Factura ID',
            'productoID' => 'Producto ID',
            'almacenID' => 'Almacen ID',
            'nombreProducto' => 'Nombre Producto',
            'unidad' => 'Unidad',
            'conceptoFacturado' => 'Concepto Facturado',
            'cantidadDetalleFactura' => 'Cantidad Detalle Factura',
            'precioDetalleFactura' => 'Precio Detalle Factura',
            'subTotalDetalleFactura' => 'Sub Total Detalle Factura',
            'totalDetalleFactura' => 'Total Detalle Factura',
            'ivaDetalleFactura' => 'Iva Detalle Factura',
            'iepsDetalleFactura' => 'Ieps Detalle Factura',
            'isrDetalleFactura' => 'Isr Detalle Factura',
            'facturaRelacionadaID' => 'Factura Relacionada ID',
            'saldoAnterior' => 'Saldo Anterior',
            'importePagado' => 'Importe Pagado',
            'saldoInsoluto' => 'Saldo Insoluto',
            'numeroParcialidad' => 'Numero Parcialidad',
            'activoFacturaDetalle' => 'Activo Factura Detalle',
            'estadoFacturaDetalle' => 'Estado Factura Detalle',
            'regEstado' => 'Reg Estado',
            'regFechaUltimaModificacion' => 'Fecha Última Modificación',
            'regUsuarioUltimaModificacion' => 'Usuario Última Modificación',
            'regFormularioUltimaModificacion' => 'Formulario Última Modificación',
            'regVersionUltimaModificacion' => 'Versión Última Modificación'
        ];
    }

    /**
     * funciones relaciones
     * relaciones con tablas
     */

    public function getIdCliente(){
        return $this->hasOne(Clientes::className(), ['clienteID' => 'clienteID']);
    }

    public function getIdEmpresa(){
        return $this->hasOne(Empresas::className(), ['empresaID' => 'empresaID']);
    }

    public function getIdFormaPago(){
        return $this->hasOne(Satformaspagos::className(), ['formaPagoID' => 'formaPagoID']);
    }

    public function getIdMetodoPago(){
        return $this->hasOne(Satmetodospagos::className(), ['metodoPagoID' => 'metodoPagoID']);
    }

    public function getIdUsoCfdi(){
        return $this->hasOne(Satusocfdiid::className(), ['usoCfdiID' => 'usoCfdiID']);
    }

    public function getIdTipoComprobante(){
        return $this->hasOne(Sattiposcomprobantes::className(), ['nombreTipoComprobante' => 'tipoComprobante']);
    }

    // Relaciones para campos de control
    public function getRegUsuarioUltimaModificacion0()
    {
        return $this->hasOne(Usuarios::className(), ['usuarioID' => 'regUsuarioUltimaModificacion']);
    }

    public function getRegFormularioUltimaModificacion0()
    {
        return $this->hasOne(Formularios::className(), ['formularioID' => 'regFormularioUltimaModificacion']);
    }

    public function getRegVersionUltimaModificacion0()
    {
        return $this->hasOne(Versiones::className(), ['versionID' => 'regVersionUltimaModificacion']);
    }
}
