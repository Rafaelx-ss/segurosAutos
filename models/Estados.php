<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Estados".
 *
 * @property int $estadoID
 * @property string $nombreEstado
 * @property bool $estadoEstado
 * @property int $paisID
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property DireccionesEstablecimientos[] $direccionesEstablecimientos
 * @property Paises $pais
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 */
class Estados extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Estados';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['estadoEstado', 'regEstado'], 'boolean'],
            [['paisID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['regFechaUltimaModificacion'], 'safe'],
            [['nombreEstado'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'estadoID' => 'Estado ID',
            'nombreEstado' => 'Nombre Estado',
            'estadoEstado' => 'Estado Estado',
            'paisID' => 'Pais ID',
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
 public function getIdPaises()
    {
       return $this->hasOne(Paises::className(), ['paisID' => 'paisID']);
    }


}
