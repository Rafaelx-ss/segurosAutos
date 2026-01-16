<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "GruposClientes".
 *
 * @property int $grupoClienteID
 * @property string $grupoClienteDescripcion
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property Clientes[] $clientes
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 */
class Gruposclientes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'GruposClientes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['regEstado'], 'boolean'],
            [['regFechaUltimaModificacion'], 'safe'],
            [['grupoClienteDescripcion'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'grupoClienteID' => 'Grupo Cliente ID',
            'grupoClienteDescripcion' => 'Grupo Cliente Descripcion',
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
