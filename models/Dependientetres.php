<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dependientetres".
 *
 * @property int $dependienteTresID
 * @property int $establecimientoID
 * @property int $dependienteOneID
 * @property int $dependienteTwoID
 * @property string $Captura
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 */
class Dependientetres extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dependientetres';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['establecimientoID', 'dependienteOneID', 'dependienteTwoID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['regEstado'], 'boolean'],
            [['regFechaUltimaModificacion'], 'safe'],
            [['Captura'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'dependienteTresID' => 'Dependiente Tres ID',
            'establecimientoID' => 'Establecimiento ID',
            'dependienteOneID' => 'Dependiente One ID',
            'dependienteTwoID' => 'Dependiente Two ID',
            'Captura' => 'Captura',
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
