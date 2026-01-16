<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Apis".
 *
 * @property int $apiID
 * @property string $nombreApi
 * @property bool $estadoApi
 * @property int $versionRegistro
 * @property int $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 * @property string $rutaApi
 * @property int $aplicacionID
 * @property double $ordenMigracion
 * @property string $tipoLista
 *
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 * @property Aplicaciones $aplicacion
 * @property MetodosApis[] $metodosApis
 * @property MigracionApis[] $migracionApis
 * @property Aplicaciones[] $aplicacions
 */
class Apis extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Apis';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['estadoApi'], 'boolean'],
            [['versionRegistro', 'regEstado', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion', 'aplicacionID'], 'integer'],
            [['regFechaUltimaModificacion'], 'safe'],
            [['ordenMigracion'], 'number'],
            [['nombreApi'], 'string', 'max' => 200],
            [['rutaApi'], 'string', 'max' => 500],
            [['tipoLista'], 'string', 'max' => 1],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'apiID' => 'Api ID',
            'nombreApi' => 'Nombre Api',
            'estadoApi' => 'Estado Api',
            'versionRegistro' => 'Version Registro',
            'regEstado' => 'Reg Estado',
            'regFechaUltimaModificacion' => 'Reg Fecha Ultima Modificacion',
            'regUsuarioUltimaModificacion' => 'Reg Usuario Ultima Modificacion',
            'regFormularioUltimaModificacion' => 'Reg Formulario Ultima Modificacion',
            'regVersionUltimaModificacion' => 'Reg Version Ultima Modificacion',
            'rutaApi' => 'Ruta Api',
            'aplicacionID' => 'Aplicacion ID',
            'ordenMigracion' => 'Orden Migracion',
            'tipoLista' => 'Tipo Lista',
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
