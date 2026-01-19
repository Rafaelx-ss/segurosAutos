<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "FEClaveUnidad".
 *
 * @property int $claveUnidadID
 * @property string $claveUnidad
 * @property string $nombre
 * @property string $descripcion
 * @property string $fechaDeInicioDeVigencia
 * @property string $fechaDeFinDeVigencia
 * @property string $simbolo
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 * @property ProductosCongo[] $productosCongos
 */
class Feclaveunidad extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'FEClaveUnidad';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'descripcion', 'simbolo'], 'string'],
            [['fechaDeInicioDeVigencia', 'fechaDeFinDeVigencia', 'regFechaUltimaModificacion'], 'safe'],
            [['versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['regEstado'], 'boolean'],
            [['claveUnidad'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'claveUnidadID' => 'Clave Unidad ID',
            'claveUnidad' => 'Clave Unidad',
            'nombre' => 'Nombre',
            'descripcion' => 'Descripcion',
            'fechaDeInicioDeVigencia' => 'Fecha De Inicio De Vigencia',
            'fechaDeFinDeVigencia' => 'Fecha De Fin De Vigencia',
            'simbolo' => 'Simbolo',
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
