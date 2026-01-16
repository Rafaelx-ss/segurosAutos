<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "MensajesBitacoraIdiomas".
 *
 * @property int $mensajeBitacoraIdiomasID
 * @property string $mensaje
 * @property int $mensajeBitacoraID
 * @property int $idiomaID
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property MensajesBitacora $mensajeBitacora
 * @property Idiomas $idioma
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 */
class Mensajesbitacoraidiomas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'MensajesBitacoraIdiomas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mensajeBitacoraID', 'idiomaID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['regEstado'], 'boolean'],
            [['regFechaUltimaModificacion'], 'safe'],
            [['mensaje'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'mensajeBitacoraIdiomasID' => 'Mensaje Bitacora Idiomas ID',
            'mensaje' => 'Mensaje',
            'mensajeBitacoraID' => 'Mensaje Bitacora ID',
            'idiomaID' => 'Idioma ID',
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
 public function getIdMensajesbitacora()
    {
       return $this->hasOne(Mensajesbitacora::className(), ['mensajeBitacoraID' => 'mensajeBitacoraID']);
    }
 public function getIdIdiomas()
    {
       return $this->hasOne(Idiomas::className(), ['idiomaID' => 'idiomaID']);
    }


}
