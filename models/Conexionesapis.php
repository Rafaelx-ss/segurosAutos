<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ConexionesApis".
 *
 * @property int $conexionApiID
 * @property int $aplicacionConexionApiID
 * @property string $rutaApi
 * @property string $usuario
 * @property string $password
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property AplicacionesConexionApi $aplicacionConexionApi
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 * @property ConexionesApisEstablecimientos[] $conexionesApisEstablecimientos
 */
class Conexionesapis extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ConexionesApis';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['aplicacionConexionApiID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['regEstado'], 'boolean'],
            [['regFechaUltimaModificacion'], 'safe'],
            [['rutaApi'], 'string', 'max' => 545],
            [['usuario'], 'string', 'max' => 250],
            [['password'], 'string', 'max' => 2048],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'conexionApiID' => 'Conexion Api ID',
            'aplicacionConexionApiID' => 'Aplicacion Conexion Api ID',
            'rutaApi' => 'Ruta Api',
            'usuario' => 'Usuario',
            'password' => 'Password',
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
 public function getIdAplicacionesconexionapi()
    {
       return $this->hasOne(Aplicacionesconexionapi::className(), ['aplicacionConexionApiID' => 'aplicacionConexionApiID']);
    }


}
