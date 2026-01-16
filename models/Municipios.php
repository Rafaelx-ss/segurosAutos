<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Municipios".
 *
 * @property int $municipioID Identificador único del municipio
 * @property string $nombreMunicipio Nombre oficial (Ej: Kanasín, Mérida)
 * @property int $estadoID Relación con tabla Estados (1=Yucatán)
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property Accidentes[] $accidentes
 * @property Clientes[] $clientes
 * @property Estados $estado
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 */
class Municipios extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Municipios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['estadoID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['regEstado'], 'boolean'],
            [['regFechaUltimaModificacion'], 'safe'],
            [['nombreMunicipio'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'municipioID' => 'Municipio ID',
            'nombreMunicipio' => 'Nombre Municipio',
            'estadoID' => 'Estado ID',
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


}
