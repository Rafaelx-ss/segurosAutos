<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "MensajesBitacora".
 *
 * @property int $mensajeBitacoraID
 * @property string $nombreMensaje
 * @property bool $activoMensaje
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property Bitacora[] $bitacoras
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 * @property MensajesBitacoraIdiomas[] $mensajesBitacoraIdiomas
 */
class Mensajesbitacora extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'MensajesBitacora';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['activoMensaje', 'regEstado'], 'boolean'],
            [['versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['regFechaUltimaModificacion'], 'safe'],
            [['nombreMensaje'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'mensajeBitacoraID' => 'Mensaje Bitacora ID',
            'nombreMensaje' => 'Nombre Mensaje',
            'activoMensaje' => 'Activo Mensaje',
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
