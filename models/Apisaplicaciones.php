<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ApisAplicaciones".
 *
 * @property int $apiAplicacionID
 * @property string $apiEndPoint
 * @property string $tiposolicitud
 * @property int $rutaAplicacionID
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property RutasApisAplicaciones $rutaAplicacion
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 */
class Apisaplicaciones extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ApisAplicaciones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rutaAplicacionID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['regEstado'], 'boolean'],
            [['regFechaUltimaModificacion'], 'safe'],
            [['apiEndPoint'], 'string', 'max' => 256],
            [['tiposolicitud'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'apiAplicacionID' => 'Api Aplicacion ID',
            'apiEndPoint' => 'Api End Point',
            'tiposolicitud' => 'Tiposolicitud',
            'rutaAplicacionID' => 'Ruta Aplicacion ID',
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
 public function getIdRutasapisaplicaciones()
    {
       return $this->hasOne(Rutasapisaplicaciones::className(), ['rutaAplicacionID' => 'rutaAplicacionID']);
    }


}
