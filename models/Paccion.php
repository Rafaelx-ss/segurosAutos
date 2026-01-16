<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "PerfilAccionFormulario".
 *
 * @property int $PerfilAccionFormularioID
 * @property int $perfilID
 * @property int $establecimientoID
 * @property int $accionFormularioID
 * @property bool $activoPerfilAccionFormulario
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 */
class Paccion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'PerfilAccionFormulario';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['perfilID',  'accionFormularioID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['activoPerfilAccionFormulario', 'regEstado'], 'boolean'],
            [['regFechaUltimaModificacion'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'PerfilAccionFormularioID' => 'ID',
            'perfilID' => 'Perfil',
            'accionFormularioID' => 'Accion Formulario',
            'activoPerfilAccionFormulario' => 'Activo',
            'versionRegistro' => 'Version Registro',
            'regEstado' => 'Reg Estado',
            'regFechaUltimaModificacion' => 'Reg Fecha Ultima Modificacion',
            'regUsuarioUltimaModificacion' => 'Reg Usuario Ultima Modificacion',
            'regFormularioUltimaModificacion' => 'Reg Formulario Ultima Modificacion',
            'regVersionUltimaModificacion' => 'Reg Version Ultima Modificacion',
        ];
    }
	
	public function getIdPerfil(){
       return $this->hasOne(Perfiles::className(), ['perfilID' => 'perfilID']);
    }
	
	public function getIdAformularios(){
       return $this->hasOne(Aformularios::className(), ['accionFormularioID' => 'accionFormularioID']);
    }
}
