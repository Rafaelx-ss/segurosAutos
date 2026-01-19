<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "FEMetodoPago".
 *
 * @property int $metodoPagoID
 * @property string $metodoPago
 * @property string $descripcion
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
class Femetodopago extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'FEMetodoPago';
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
            [['metodoPago'], 'string', 'max' => 50],
            [['descripcion'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'metodoPagoID' => 'Metodo Pago ID',
            'metodoPago' => 'Metodo Pago',
            'descripcion' => 'Descripcion',
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
