<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Acciones".
 *
 * @property int $accionID
 * @property string $nombreAccion
 * @property string $imagen
 * @property bool $estadoAccion
 * @property int $textoID
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property Textos $texto
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 * @property AccionesFormularios[] $accionesFormularios
 */
class Acciones extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Acciones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['estadoAccion', 'regEstado'], 'boolean'],
            [['textoID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['regFechaUltimaModificacion', 'paginaAccion'], 'safe'],
            [['nombreAccion'], 'string', 'max' => 100],
            [['imagen'], 'string', 'max' => 20],
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'accionID' => 'ID',
            'nombreAccion' => 'Nombre',
            'imagen' => 'Icono',
            'estadoAccion' => 'Activo',
            'textoID' => 'TextoID',
			'paginaAccion' => 'Página',
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
