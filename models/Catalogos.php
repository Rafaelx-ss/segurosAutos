<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "catalogos".
 *
 * @property int $catalogoID
 * @property string $nombreCatalogo
 * @property string $sqlQuery
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 * @property bool $activoCatalogo
 */
class Catalogos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Catalogos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
           // [['nombreCatalogo', 'sqlQuery', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'required'],
            [['sqlQuery'], 'string'],
            [['versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['regEstado', 'activoCatalogo'], 'boolean'],
            [['regFechaUltimaModificacion', 'nombreModelo'], 'safe'],
            [['nombreCatalogo'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'catalogoID' => 'Catalogo ID',
            'nombreCatalogo' => 'Nombre Catalogo',
			'nombreModelo' => 'Nombre Modelo',
            'sqlQuery' => 'Sql Query',
            'versionRegistro' => 'Version Registro',
            'regEstado' => 'Reg Estado',
            'regFechaUltimaModificacion' => 'Reg Fecha Ultima Modificacion',
            'regUsuarioUltimaModificacion' => 'Reg Usuario Ultima Modificacion',
            'regFormularioUltimaModificacion' => 'Reg Formulario Ultima Modificacion',
            'regVersionUltimaModificacion' => 'Reg Version Ultima Modificacion',
            'activoCatalogo' => 'Activo Catalogo',
        ];
    }
}
