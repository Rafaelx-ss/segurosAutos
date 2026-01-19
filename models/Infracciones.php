<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Infracciones".
 *
 * @property int $infraccionID
 * @property int $accidenteID Opcional: Si la multa derivó de un choque registrado
 * @property int $polizaID Póliza relacionada
 * @property int $conductorID Conductor infraccionado
 * @property string $tipoInfraccion Motivo (Ej: Exceso Velocidad, Alcohol)
 * @property string $montoMulta Costo monetario de la infracción
 * @property string $fechaInfraccion Fecha y hora de la boleta
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property Accidentes $accidente
 * @property Polizas $poliza
 * @property Conductores $conductor
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 */
class Infracciones extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Infracciones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['accidenteID', 'polizaID', 'conductorID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['montoMulta'], 'number'],
            [['fechaInfraccion', 'regFechaUltimaModificacion'], 'safe'],
            [['regEstado'], 'boolean'],
            [['tipoInfraccion'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'infraccionID' => 'Infraccion ID',
            'accidenteID' => 'Accidente ID',
            'polizaID' => 'Poliza ID',
            'conductorID' => 'Conductor ID',
            'tipoInfraccion' => 'Tipo Infraccion',
            'montoMulta' => 'Monto Multa',
            'fechaInfraccion' => 'Fecha Infraccion',
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
 public function getIdAccidentes()
    {
       return $this->hasOne(Accidentes::className(), ['accidenteID' => 'accidenteID']);
    }
 public function getIdPolizas()
    {
       return $this->hasOne(Polizas::className(), ['polizaID' => 'polizaID']);
    }
 public function getIdConductores()
    {
       return $this->hasOne(Conductores::className(), ['conductorID' => 'conductorID']);
    }


}
