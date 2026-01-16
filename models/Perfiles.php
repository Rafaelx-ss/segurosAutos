<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Perfiles".
 *
 * @property int $perfilID
 * @property string $nombrePerfil
 * @property bool $activoPerfil
 * @property int $establecimientoID
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property Establecimientos $establecimiento
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 * @property PerfilesCompuestos[] $perfilesCompuestos
 * @property PerfilesCompuestos[] $perfilesCompuestos0
 * @property PermisosAccionesFormulariosPerfiles[] $permisosAccionesFormulariosPerfiles
 * @property PermisosFormulariosPerfiles[] $permisosFormulariosPerfiles
 */
class Perfiles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Perfiles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['activoPerfil', 'regEstado'], 'boolean'],
            [['versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['regFechaUltimaModificacion'], 'safe'],
            [['nombrePerfil'], 'string', 'max' => 250],
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'perfilID' => 'ID',
            'nombrePerfil' => 'Nombre',
            'activoPerfil' => 'Activo',
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
   
}
