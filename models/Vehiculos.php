<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Vehiculos".
 *
 * @property int $vehiculoID
 * @property string $marca Ej: Nissan, Ford
 * @property string $modelo Ej: Versa, Figo
 * @property int $anio Año del modelo para avalúo
 * @property string $placa Placa actual del vehículo
 * @property string $numeroSerie VIN / Número de serie (Identificador único legal)
 * @property string $color Color para identificación visual en siniestros
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property Accidentes[] $accidentes
 * @property Polizas[] $polizas
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 */
class Vehiculos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Vehiculos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['anio', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['regEstado'], 'boolean'],
            [['regFechaUltimaModificacion'], 'safe'],
            [['marca', 'modelo', 'numeroSerie'], 'string', 'max' => 50],
            [['placa'], 'string', 'max' => 20],
            [['color'], 'string', 'max' => 30],
            [['placa'], 'unique'],
            [['numeroSerie'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'vehiculoID' => 'Vehiculo ID',
            'marca' => 'Marca',
            'modelo' => 'Modelo',
            'anio' => 'Anio',
            'placa' => 'Placa',
            'numeroSerie' => 'Numero Serie',
            'color' => 'Color',
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
