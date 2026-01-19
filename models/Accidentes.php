<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Accidentes".
 *
 * @property int $accidenteID
 * @property int $polizaID Póliza afectada
 * @property int $vehiculoID Vehículo siniestrado
 * @property int $conductorID Persona que conducía al momento del choque (Para historial)
 * @property int $municipioID Municipio donde ocurrió (Reporte Kanasín)
 * @property string $fechaHoraAccidente Exactitud necesaria para reporte de HORAS PICO
 * @property string $descripcion Narrativa breve del siniestro
 * @property string $latitud Coordenada GPS para mapa de calor
 * @property string $longitud Coordenada GPS para mapa de calor
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property Polizas $poliza
 * @property Vehiculos $vehiculo
 * @property Conductores $conductor
 * @property Municipios $municipio
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 * @property Infracciones[] $infracciones
 */
class Accidentes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Accidentes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['polizaID', 'vehiculoID', 'conductorID', 'municipioID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['fechaHoraAccidente', 'regFechaUltimaModificacion'], 'safe'],
            [['descripcion'], 'string'],
            [['latitud', 'longitud'], 'number'],
            [['regEstado'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'accidenteID' => 'Accidente ID',
            'polizaID' => 'Poliza ID',
            'vehiculoID' => 'Vehiculo ID',
            'conductorID' => 'Conductor ID',
            'municipioID' => 'Municipio ID',
            'fechaHoraAccidente' => 'Fecha Hora Accidente',
            'descripcion' => 'Descripcion',
            'latitud' => 'Latitud',
            'longitud' => 'Longitud',
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
 public function getIdPolizas()
    {
       return $this->hasOne(Polizas::className(), ['polizaID' => 'polizaID']);
    }
 public function getIdVehiculos()
    {
       return $this->hasOne(Vehiculos::className(), ['vehiculoID' => 'vehiculoID']);
    }
 public function getIdConductores()
    {
       return $this->hasOne(Conductores::className(), ['conductorID' => 'conductorID']);
    }
 public function getIdMunicipios()
    {
       return $this->hasOne(Municipios::className(), ['municipioID' => 'municipioID']);
    }


}
