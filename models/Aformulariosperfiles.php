<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "PermisosAccionesFormulariosPerfiles".
 *
 * @property int $permisoAccionID
 * @property int $permisoFormularioID
 * @property int $accionFormularioID
 * @property int $perfilID
 * @property int $establecimientoID
 * @property bool $activoPermiso
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property AccionesFormularios $accionFormulario
 * @property Establecimientos $establecimiento
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 * @property Perfiles $establecimiento0
 * @property PermisosFormulariosPerfiles $permisoFormulario
 */
class Aformulariosperfiles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'PermisosAccionesFormulariosPerfiles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['permisoFormularioID', 'accionFormularioID', 'perfilID', 'establecimientoID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['activoPermiso', 'regEstado'], 'boolean'],
            [['regFechaUltimaModificacion'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'permisoAccionID' => 'Permiso Accion ID',
            'permisoFormularioID' => 'Permiso Formulario ID',
            'accionFormularioID' => 'Accion Formulario ID',
            'perfilID' => 'Perfil ID',
            'establecimientoID' => 'Establecimiento ID',
            'activoPermiso' => 'Activo Permiso',
            'versionRegistro' => 'Version Registro',
            'regEstado' => 'Reg Estado',
            'regFechaUltimaModificacion' => 'Reg Fecha Ultima Modificacion',
            'regUsuarioUltimaModificacion' => 'Reg Usuario Ultima Modificacion',
            'regFormularioUltimaModificacion' => 'Reg Formulario Ultima Modificacion',
            'regVersionUltimaModificacion' => 'Reg Version Ultima Modificacion',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccionFormulario()
    {
        return $this->hasOne(AccionesFormularios::className(), ['accionFormularioID' => 'accionFormularioID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEstablecimiento()
    {
        return $this->hasOne(Establecimientos::className(), ['establecimientoID' => 'establecimientoID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegUsuarioUltimaModificacion0()
    {
        return $this->hasOne(Usuarios::className(), ['usuarioID' => 'regUsuarioUltimaModificacion']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegFormularioUltimaModificacion0()
    {
        return $this->hasOne(Formularios::className(), ['formularioID' => 'regFormularioUltimaModificacion']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegVersionUltimaModificacion0()
    {
        return $this->hasOne(Versiones::className(), ['versionID' => 'regVersionUltimaModificacion']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEstablecimiento0()
    {
        return $this->hasOne(Perfiles::className(), ['establecimientoID' => 'establecimientoID', 'perfilID' => 'perfilID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermisoFormulario()
    {
        return $this->hasOne(PermisosFormulariosPerfiles::className(), ['permisoFormularioID' => 'permisoFormularioID', 'establecimientoID' => 'establecimientoID', 'perfilID' => 'perfilID']);
    }
}
