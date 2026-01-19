<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "FEUsoCFDI".
 *
 * @property int $usoCfdiID
 * @property string $usoCFDI
 * @property string $descripcion
 * @property string $fisica
 * @property string $moral
 * @property string $fechaInicioVigencia
 * @property string $fechaFinVigencia
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property CotizacionesDetalle[] $cotizacionesDetalles
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 */
class Feusocfdi extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'FEUsoCFDI';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fechaInicioVigencia', 'fechaFinVigencia', 'regFechaUltimaModificacion'], 'safe'],
            [['versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['regEstado'], 'boolean'],
            [['usoCFDI'], 'string', 'max' => 50],
            [['descripcion'], 'string', 'max' => 5000],
            [['fisica', 'moral'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'usoCfdiID' => 'Uso Cfdi ID',
            'usoCFDI' => 'Uso Cfdi',
            'descripcion' => 'Descripcion',
            'fisica' => 'Fisica',
            'moral' => 'Moral',
            'fechaInicioVigencia' => 'Fecha Inicio Vigencia',
            'fechaFinVigencia' => 'Fecha Fin Vigencia',
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
