<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "PermisosFormulariosPerfiles".
 *
 * @property int $permisoFormularioID
 * @property int $perfilID
 * @property int $formularioID
 * @property int $establecimientoID
 * @property bool $activoPermiso
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property PermisosAccionesFormulariosPerfiles[] $permisosAccionesFormulariosPerfiles
 * @property Formularios $formulario
 * @property Establecimientos $establecimiento
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 * @property Perfiles $perfil
 */
class Formulariosperfiles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'PermisosFormulariosPerfiles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['perfilID', 'formularioID',  'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
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
            'permisoFormularioID' => 'ID',
            'perfilID' => 'Perfil',
            'formularioID' => 'Formulario',
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
	
	public function getIdFomulario(){
       return $this->hasOne(Formularios::className(), ['formularioID' => 'formularioID']);
    }
   
}
