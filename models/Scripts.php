<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Scripts".
 *
 * @property int $scriptID
 * @property int $aplicacionID
 * @property string $version
 * @property string $descripcion
 * @property string $fechaInicio
 * @property string $fechaFin
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property DetalleScripts[] $detalleScripts
 * @property LogEjecucionScripts[] $logEjecucionScripts
 * @property Aplicaciones $aplicacion
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 */
class Scripts extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Scripts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['aplicacionID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['fechaInicio', 'fechaFin', 'regFechaUltimaModificacion'], 'safe'],
            [['regEstado'], 'boolean'],
            [['version'], 'string', 'max' => 20],
            [['descripcion'], 'string', 'max' => 2000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'scriptID' => 'Script ID',
            'aplicacionID' => 'Aplicacion ID',
            'version' => 'Version',
            'descripcion' => 'Descripcion',
            'fechaInicio' => 'Fecha Inicio',
            'fechaFin' => 'Fecha Fin',
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
 public function getIdAplicaciones()
    {
       return $this->hasOne(Aplicaciones::className(), ['aplicacionID' => 'aplicacionID']);
    }


}
