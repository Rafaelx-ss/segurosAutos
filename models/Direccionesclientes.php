<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "DireccionesClientes".
 *
 * @property int $direccionClienteID
 * @property string $alias
 * @property string $calle
 * @property string $numeroInterior
 * @property string $numeroExterior
 * @property string $codigoPostal
 * @property string $colonia
 * @property string $localidad
 * @property string $referencia
 * @property string $municipio
 * @property bool $esDefault
 * @property string $latitud
 * @property string $longitud
 * @property int $estadoID
 * @property int $clienteID
 * @property bool $estadoDireccion
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property Estados $estado
 * @property Clientes $cliente
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 */
class Direccionesclientes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'DireccionesClientes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['esDefault', 'estadoDireccion', 'regEstado'], 'boolean'],
            [['latitud', 'longitud'], 'number'],
            [['estadoID', 'clienteID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['regFechaUltimaModificacion'], 'safe'],
            [['alias'], 'string', 'max' => 250],
            [['calle', 'codigoPostal'], 'string', 'max' => 100],
            [['numeroInterior', 'numeroExterior'], 'string', 'max' => 50],
            [['colonia', 'localidad', 'referencia', 'municipio'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'direccionClienteID' => 'Direccion Cliente ID',
            'alias' => 'Alias',
            'calle' => 'Calle',
            'numeroInterior' => 'Numero Interior',
            'numeroExterior' => 'Numero Exterior',
            'codigoPostal' => 'Codigo Postal',
            'colonia' => 'Colonia',
            'localidad' => 'Localidad',
            'referencia' => 'Referencia',
            'municipio' => 'Municipio',
            'esDefault' => 'Es Default',
            'latitud' => 'Latitud',
            'longitud' => 'Longitud',
            'estadoID' => 'Estado ID',
            'clienteID' => 'Cliente ID',
            'estadoDireccion' => 'Estado Direccion',
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
 public function getIdEstados()
    {
       return $this->hasOne(Estados::className(), ['estadoID' => 'estadoID']);
    }
 public function getIdClientes()
    {
       return $this->hasOne(Clientes::className(), ['clienteID' => 'clienteID']);
    }


}
