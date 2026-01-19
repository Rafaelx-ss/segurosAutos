<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "RelacionPaquetesSotfware".
 *
 * @property int $relacionPaqueteSotfwareID
 * @property int $paqueteKairosID
 * @property int $softwareKairosID
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property PaquetesKairos $paqueteKairos
 * @property SoftwareKairos $softwareKairos
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 */
class Relacionpaquetessotfware extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'RelacionPaquetesSotfware';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['paqueteKairosID', 'softwareKairosID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['regEstado'], 'boolean'],
            [['regFechaUltimaModificacion'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'relacionPaqueteSotfwareID' => 'Relacion Paquete Sotfware ID',
            'paqueteKairosID' => 'Paquete Kairos ID',
            'softwareKairosID' => 'Software Kairos ID',
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
 public function getIdPaqueteskairos()
    {
       return $this->hasOne(Paqueteskairos::className(), ['paqueteKairosID' => 'paqueteKairosID']);
    }
 public function getIdSoftwarekairos()
    {
       return $this->hasOne(Softwarekairos::className(), ['softwareKairosID' => 'softwareKairosID']);
    }


}
