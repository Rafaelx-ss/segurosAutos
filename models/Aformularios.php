<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "AccionesFormularios".
 *
 * @property int $accionFormularioID
 * @property bool $estadoAccion
 * @property int $accionID
 * @property int $formularioID
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property Acciones $accion
 * @property Formularios $formulario
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 * @property PermisosAccionesFormulariosPerfiles[] $permisosAccionesFormulariosPerfiles
 */
class Aformularios extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'AccionesFormularios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['estadoAccion', 'regEstado'], 'boolean'],
            [['accionID', 'formularioID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['regFechaUltimaModificacion', 'claveAccion'], 'safe'],
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'accionFormularioID' => 'ID',
			'claveAccion' => 'Clave',
            'estadoAccion' => 'Estado',
            'accionID' => 'Accion',
            'formularioID' => 'Formulario',
            'versionRegistro' => 'Version Registro',
            'regEstado' => 'Reg Estado',
            'regFechaUltimaModificacion' => 'Reg Fecha Ultima Modificacion',
            'regUsuarioUltimaModificacion' => 'Reg Usuario Ultima Modificacion',
            'regFormularioUltimaModificacion' => 'Reg Formulario Ultima Modificacion',
            'regVersionUltimaModificacion' => 'Reg Version Ultima Modificacion',
        ];
    }
	
	public function getIdAccion(){
       return $this->hasOne(Acciones::className(), ['accionID' => 'accionID']);
    }
	
	public function getIdFomulario(){
       return $this->hasOne(Formularios::className(), ['formularioID' => 'formularioID']);
    }

   
}
