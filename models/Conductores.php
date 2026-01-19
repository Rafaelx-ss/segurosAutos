<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Conductores".
 *
 * @property int $conductorID ID único del conductor asegurado
 * @property int $clienteID Opcional: Vincula si el conductor es el mismo dueño de la póliza
 * @property string $nombre Nombre(s) del conductor real
 * @property string $apellidoPaterno
 * @property string $apellidoMaterno
 * @property string $fechaNacimiento Dato crítico para calcular edad y nivel de riesgo
 * @property string $genero Para estadísticas demográficas de siniestralidad
 * @property string $rfc RFC del conductor (puede diferir del cliente)
 * @property string $numeroLicencia Número de licencia vigente
 * @property string $tipoLicencia Ej: Chofer, Automovilista, Motociclista
 * @property string $vigenciaLicencia Fecha de expiración para alertas de renovación
 * @property int $estadoEmisorLicenciaID Estado que expidió la licencia
 * @property string $scoreConductor Puntaje inicial 100. Disminuye con cada accidente registrado
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property Accidentes[] $accidentes
 * @property Clientes $cliente
 * @property Estados $estadoEmisorLicencia
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 * @property Infracciones[] $infracciones
 */
class Conductores extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Conductores';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['clienteID', 'estadoEmisorLicenciaID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['fechaNacimiento', 'vigenciaLicencia', 'regFechaUltimaModificacion'], 'safe'],
            [['genero'], 'string'],
            [['scoreConductor'], 'number'],
            [['regEstado'], 'boolean'],
            [['nombre', 'apellidoPaterno', 'apellidoMaterno'], 'string', 'max' => 100],
            [['rfc'], 'string', 'max' => 13],
            [['numeroLicencia'], 'string', 'max' => 50],
            [['tipoLicencia'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'conductorID' => 'Conductor ID',
            'clienteID' => 'Cliente ID',
            'nombre' => 'Nombre',
            'apellidoPaterno' => 'Apellido Paterno',
            'apellidoMaterno' => 'Apellido Materno',
            'fechaNacimiento' => 'Fecha Nacimiento',
            'genero' => 'Genero',
            'rfc' => 'Rfc',
            'numeroLicencia' => 'Numero Licencia',
            'tipoLicencia' => 'Tipo Licencia',
            'vigenciaLicencia' => 'Vigencia Licencia',
            'estadoEmisorLicenciaID' => 'Estado Emisor Licencia ID',
            'scoreConductor' => 'Score Conductor',
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
 public function getIdClientes()
    {
       return $this->hasOne(Clientes::className(), ['clienteID' => 'clienteID']);
    }
 public function getIdEstados()
    {
       return $this->hasOne(Estados::className(), ['estadoID' => 'estadoEmisorLicenciaID']);
    }


}
