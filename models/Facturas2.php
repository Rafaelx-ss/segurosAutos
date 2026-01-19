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
 * @property string $xmlBase64
 * @property int $estatusPago
 * @property string $periodicidad
 * @property int $anio
 * @property int $mes
 * @property string $rutaPDF
 * @property string $rutaXML
 * @property string $motivo
 * @property string $uuidSustituto
 * @property string $ctaPago
 * @property string $urlAcuseCancelacion
 * @property int $codyFactID
 * @property string $activoFactura
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 * @property MovimientosBancosFacturas[] $movimientosBancosFacturas
 * @property Pagos[] $pagos
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
            [['cotizacionID', 'establecimientoID', 'clienteID', 'metodoPagoID', 'formaPagoID', 'usoCfdiID', 'tipoRelacionID', 'numeroIntentosCancelacion', 'diasCredito', 'estatusPago', 'anio', 'mes', 'codyFactID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['fechaFactura', 'fechaPago', 'fechaTimbrado', 'fechaSolicitudCancelacion', 'fechaCancelacion', 'regFechaUltimaModificacion'], 'safe'],
            [['subTotalFactura', 'ivaFactura', 'isrFactura', 'iepsFactura', 'totalFactura', 'tipoCambio'], 'number'],
            [['observaciones', 'uuid', 'cadenaoriginal', 'sello', 'selloSAT', 'aprobacion', 'certificado', 'cfdiRelacionados', 'xmlBase64'], 'string'],
            [['correoEnviado', 'regEstado'], 'boolean'],
            [['lugarExpedicion', 'serie', 'folio', 'versionTimbrado', 'rfcCuentaBeneficiario', 'rfcCuentaOrdenante'], 'string', 'max' => 20],
            [['direccionEmpresa', 'direccionCliente', 'url_factura'], 'string', 'max' => 255],
            [['tipoComprobante', 'modo'], 'string', 'max' => 10],
            [['pac', 'numeroOperacionPago'], 'string', 'max' => 100],
            [['cuentaBeneficiario', 'cuentaOrdenante'], 'string', 'max' => 250],
            [['mensageCancelacion'], 'string', 'max' => 500],
            [['codigoRespuestaCancelacion'], 'string', 'max' => 200],
            [['tipoMoneda'], 'string', 'max' => 5],
            [['periodicidad', 'motivo'], 'string', 'max' => 2],
            [['rutaPDF', 'rutaXML', 'urlAcuseCancelacion'], 'string', 'max' => 256],
            [['uuidSustituto'], 'string', 'max' => 36],
            [['ctaPago'], 'string', 'max' => 50],
            [['activoFactura'], 'string', 'max' => 45],
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
            'establecimientoID' => 'Establecimiento ID',
            'lugarExpedicion' => 'Lugar Expedicion',
            'direccionEmpresa' => 'Direccion Empresa',
            'clienteID' => 'Cliente ID',
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
            'xmlBase64' => 'Xml Base64',
            'estatusPago' => 'Estatus Pago',
            'periodicidad' => 'Periodicidad',
            'anio' => 'Anio',
            'mes' => 'Mes',
            'rutaPDF' => 'Ruta Pdf',
            'rutaXML' => 'Ruta Xml',
            'motivo' => 'Motivo',
            'uuidSustituto' => 'Uuid Sustituto',
            'ctaPago' => 'Cta Pago',
            'urlAcuseCancelacion' => 'Url Acuse Cancelacion',
            'codyFactID' => 'Cody Fact ID',
            'activoFactura' => 'Activo Factura',
            'versionRegistro' => 'Version Registro',
            'regEstado' => 'Reg Estado',
            'regFechaUltimaModificacion' => 'Reg Fecha Ultima Modificacion',
            'regUsuarioUltimaModificacion' => 'Reg Usuario Ultima Modificacion',
            'regFormularioUltimaModificacion' => 'Reg Formulario Ultima Modificacion',
            'regVersionUltimaModificacion' => 'Reg Version Ultima Modificacion',
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


}
