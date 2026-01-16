<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "PerfilesCompuestos".
 *
 * @property int $perfilCompuestoID
 * @property int $usuarioID
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
 * @property Usuarios $usuario
 * @property Perfiles $perfil
 * @property Establecimientos $establecimiento
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 * @property Perfiles $establecimiento0
 */
class Pcompuestos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'PerfilesCompuestos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['usuarioID', 'perfilID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
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
            'perfilCompuestoID' => 'ID',
            'usuarioID' => 'Usuario',
            'perfilID' => 'Perfil',
            'activoPermiso' => 'Activo',
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
	
	public function getIdUsuarios(){
       return $this->hasOne(Usuarios::className(), ['usuarioID' => 'usuarioID']);
    }

    
}
