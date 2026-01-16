<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "PermisosMenus".
 *
 * @property int $permisosMenusID
 * @property int $perfilID
 * @property int $menuID
 * @property int $orden
 * @property int $activoMenusFormularios
 * @property int $versionRegistro
 * @property int $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 */
class Pmenus extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'PermisosMenus';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['perfilID', 'menuID', 'orden', 'activoMenusFormularios', 'versionRegistro', 'regEstado', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['regFechaUltimaModificacion'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'permisosMenusID' => 'Permisos Menus ID',
            'perfilID' => 'Perfil ID',
            'menuID' => 'Menu ID',
            'orden' => 'Orden',
            'activoMenusFormularios' => 'Activo Menus Formularios',
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
	
	public function getIdMenu(){
       return $this->hasOne(Menus::className(), ['menuID' => 'menuID']);
    }
}
