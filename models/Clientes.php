<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Clientes".
 *
 * @property int $clienteID
 * @property string $nombreComercial
 * @property string $clienteRazonSocial
 * @property string $clienteRFC
 * @property string $clienteTelefono
 * @property string $clienteEmail
 * @property double $vpc
 * @property int $grupoClienteID
 * @property string $cuentaContable
 * @property string $establecimientoProvisiona
 * @property int $tipoClienteID
 * @property string $afectaSaldoRem
 * @property string $cuentafactura
 * @property int $metodoPagoID
 * @property int $formaPagoID
 * @property int $UsoCFDIID
 * @property int $clienteGrupoFacturacionID
 * @property string $condicionesPago
 * @property int $regimenFiscalID
 * @property string $codigoPostalCliente
 * @property string $clienteTipoPersona
 * @property bool $validarSaldo
 * @property bool $estadoCliente
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 * @property FEFormaPago $formaPago
 * @property GruposClientes $grupoCliente
 * @property ClientesGruposFacturacion $clienteGrupoFacturacion
 * @property FEMetodoPago $metodoPago
 * @property FERegimenFiscal $regimenFiscal
 * @property TiposClientes $tipoCliente
 * @property FEUsoCFDI $usoCFDI
 * @property ContactosClientes[] $contactosClientes
 * @property Cotizaciones[] $cotizaciones
 * @property DireccionesClientes[] $direccionesClientes
 * @property Ventas[] $ventas
 */
class Clientes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Clientes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['clienteEmail'], 'string'],
            [['vpc'], 'number'],
            [['grupoClienteID', 'tipoClienteID', 'metodoPagoID', 'formaPagoID', 'UsoCFDIID', 'clienteGrupoFacturacionID', 'regimenFiscalID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['validarSaldo', 'estadoCliente', 'regEstado'], 'boolean'],
            [['regFechaUltimaModificacion'], 'safe'],
            [['nombreComercial', 'clienteRazonSocial'], 'string', 'max' => 500],
            [['clienteRFC', 'clienteTelefono'], 'string', 'max' => 100],
            [['cuentaContable', 'cuentafactura'], 'string', 'max' => 25],
            [['establecimientoProvisiona'], 'string', 'max' => 10],
            [['afectaSaldoRem', 'clienteTipoPersona'], 'string', 'max' => 1],
            [['condicionesPago'], 'string', 'max' => 256],
            [['codigoPostalCliente'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'clienteID' => 'Cliente ID',
            'nombreComercial' => 'Nombre Comercial',
            'clienteRazonSocial' => 'Cliente Razon Social',
            'clienteRFC' => 'Cliente Rfc',
            'clienteTelefono' => 'Cliente Telefono',
            'clienteEmail' => 'Cliente Email',
            'vpc' => 'Vpc',
            'grupoClienteID' => 'Grupo Cliente ID',
            'cuentaContable' => 'Cuenta Contable',
            'establecimientoProvisiona' => 'Establecimiento Provisiona',
            'tipoClienteID' => 'Tipo Cliente ID',
            'afectaSaldoRem' => 'Afecta Saldo Rem',
            'cuentafactura' => 'Cuentafactura',
            'metodoPagoID' => 'Metodo Pago ID',
            'formaPagoID' => 'Forma Pago ID',
            'UsoCFDIID' => 'Uso Cfdiid',
            'clienteGrupoFacturacionID' => 'Cliente Grupo Facturacion ID',
            'condicionesPago' => 'Condiciones Pago',
            'regimenFiscalID' => 'Regimen Fiscal ID',
            'codigoPostalCliente' => 'Codigo Postal Cliente',
            'clienteTipoPersona' => 'Cliente Tipo Persona',
            'validarSaldo' => 'Validar Saldo',
            'estadoCliente' => 'Estado Cliente',
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
 public function getIdFeregimenfiscal()
    {
       return $this->hasOne(Feregimenfiscal::className(), ['regimenFiscalID' => 'regimenFiscalID']);
    }
 public function getIdTiposclientes()
    {
       return $this->hasOne(Tiposclientes::className(), ['tipoClienteID' => 'tipoClienteID']);
    }


}
