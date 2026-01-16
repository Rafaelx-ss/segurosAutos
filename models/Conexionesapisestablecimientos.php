<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ConexionesApisEstablecimientos".
 *
 * @property int $conexionApiEstablecimientoID
 * @property int $establecimientoID
 * @property int $conexionApiID
 * @property bool $versionActual
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property Establecimientos $establecimiento
 * @property ConexionesApis $conexionApi
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 */
class Conexionesapisestablecimientos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ConexionesApisEstablecimientos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['establecimientoID', 'conexionApiID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['versionActual', 'regEstado'], 'boolean'],
            [['regFechaUltimaModificacion'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'conexionApiEstablecimientoID' => 'Conexion Api Establecimiento ID',
            'establecimientoID' => 'Establecimiento ID',
            'conexionApiID' => 'Conexion Api ID',
            'versionActual' => 'Version Actual',
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


}
