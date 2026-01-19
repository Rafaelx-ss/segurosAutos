<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Polizas".
 *
 * @property int $polizaID
 * @property int $clienteID Cliente que paga la póliza (Titular)
 * @property int $vehiculoID Vehículo protegido bajo este contrato
 * @property string $numeroPoliza Folio interno o comercial de la póliza
 * @property string $montoCobertura Monto máximo que la aseguradora pagará (Suma Asegurada)
 * @property string $costoPoliza Precio venta (Prima) que pagó el cliente
 * @property string $fechaCompra Fecha de emisión/compra
 * @property string $fechaVencimiento Fecha fin de vigencia
 * @property string $estadoPoliza Estatus actual para validación rápida
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property Accidentes[] $accidentes
 * @property Infracciones[] $infracciones
 * @property Clientes $cliente
 * @property Vehiculos $vehiculo
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 */
class Polizas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Polizas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['clienteID', 'vehiculoID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['montoCobertura', 'costoPoliza'], 'number'],
            [['fechaCompra', 'fechaVencimiento', 'regFechaUltimaModificacion'], 'safe'],
            [['estadoPoliza'], 'string'],
            [['regEstado'], 'boolean'],
            [['numeroPoliza'], 'string', 'max' => 50],
            [['numeroPoliza'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'polizaID' => 'Poliza ID',
            'clienteID' => 'Cliente ID',
            'vehiculoID' => 'Vehiculo ID',
            'numeroPoliza' => 'Numero Poliza',
            'montoCobertura' => 'Monto Cobertura',
            'costoPoliza' => 'Costo Poliza',
            'fechaCompra' => 'Fecha Compra',
            'fechaVencimiento' => 'Fecha Vencimiento',
            'estadoPoliza' => 'Estado Poliza',
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
 public function getIdVehiculos()
    {
       return $this->hasOne(Vehiculos::className(), ['vehiculoID' => 'vehiculoID']);
    }


}
