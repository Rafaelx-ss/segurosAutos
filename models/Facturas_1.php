<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Facturas".
 *
 * @property int $facturaID
 * @property int $cotizacionID
 * @property string $fechaFactura
 * @property string $fechaPago
 * @property double $subTotalFactura
 * @property double $ivaFactura
 * @property double $isrFactura
 * @property double $iepsFactura
 * @property double $totalFactura
 * @property string $observaciones
 * @property int $establecimientoID
 * @property string $lugarExpedicion
 * @property string $direccionEmpresa
 * @property int $clienteID
 * @property string $direccionCliente
 * @property int $metodoPagoID
 * @property int $formaPagoID
 * @property int $usoCfdiID
 * @property string $tipoComprobante
 * @property string $serie
 * @property string $folio
 * @property string $versionTimbrado
 * @property string $uuid
 * @property string $fechaTimbrado
 * @property string $cadenaoriginal
 * @property string $sello
 * @property string $selloSAT
 * @property string $aprobacion
 * @property string $certificado
 * @property string $pac
 * @property bool $correoEnviado
 * @property string $url_factura
 * @property int $tipoRelacionID
 * @property string $cfdiRelacionados
 * @property string $numeroOperacionPago
 * @property string $rfcCuentaBeneficiario
 * @property string $cuentaBeneficiario
 * @property string $rfcCuentaOrdenante
 * @property string $cuentaOrdenante
 * @property string $mensageCancelacion
 * @property string $fechaSolicitudCancelacion
 * @property int $numeroIntentosCancelacion
 * @property string $codigoRespuestaCancelacion
 * @property string $fechaCancelacion
 * @property string $modo
 * @property string $tipoMoneda
 * @property double $tipoCambio
 * @property int $diasCredito
 * @property int $estatusPago
 * @property int $activoFactura
 * @property bool $regEstado
 */
class Facturas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Facturas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
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

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
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
            'formaPagoID' => 'Forma Pago',
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


 /**
     * funciones relaciones
     * relaciones con tablas
     */
 public function getIdClientes()
    {
       return $this->hasOne(Clientes::className(), ['clienteID' => 'clienteID']);
    }
 public function getIdFemetodopago()
    {
       return $this->hasOne(Femetodopago::className(), ['metodoPagoID' => 'metodoPagoID']);
    }
 public function getIdFeformapago()
    {
       return $this->hasOne(Feformapago::className(), ['formaPagoID' => 'formaPagoID']);
    }
 public function getIdFeusocfdi()
    {
       return $this->hasOne(Feusocfdi::className(), ['usoCfdiID' => 'usoCfdiID']);
    }


    public function getIdEstablecimiento() {
      return $this->hasOne(Establecimientos::className(), ['establecimientoID' => 'establecimientoID']);
    }


}
