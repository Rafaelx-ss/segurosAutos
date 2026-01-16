<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "MenusFormularios".
 *
 * @property int $menusFormulariosID
 * @property int $formularioID
 * @property int $menuID
 * @property int $ordenMenuFormulario
 * @property bool $activoMenusForumlarios
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 */
class Mformularios extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'MenusFormularios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['formularioID', 'menuID', 'ordenMenuFormulario', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['activoMenusForumlarios', 'regEstado'], 'boolean'],
            [['regFechaUltimaModificacion'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'menusFormulariosID' => 'ID',
            'formularioID' => 'Formulario',
            'menuID' => 'Menu',
            'ordenMenuFormulario' => 'Orden',
            'activoMenusForumlarios' => 'Activo',
            'versionRegistro' => 'Version Registro',
            'regEstado' => 'Reg Estado',
            'regFechaUltimaModificacion' => 'Reg Fecha Ultima Modificacion',
            'regUsuarioUltimaModificacion' => 'Reg Usuario Ultima Modificacion',
            'regFormularioUltimaModificacion' => 'Reg Formulario Ultima Modificacion',
            'regVersionUltimaModificacion' => 'Reg Version Ultima Modificacion',
        ];
    }
	
	public function getIdMenu(){
       return $this->hasOne(Menus::className(), ['menuID' => 'menuID']);
    }
	
	public function getIdFomulario(){
       return $this->hasOne(Formularios::className(), ['formularioID' => 'formularioID']);
    }
}
