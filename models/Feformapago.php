<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "FEFormaPago".
 *
 * @property int $formaPagoID
 * @property string $formaPago
 * @property string $descripcion
 * @property string $bancarizado
 * @property string $numeroOperacion
 * @property string $rfcEmisorCuentaOrdenante
 * @property string $cuentaOrdenante
 * @property string $patronCuentaOrdenante
 * @property string $rfcEmisorCuentaBeneficiario
 * @property string $cuentaBenenficiario
 * @property string $patronCuentaBeneficiaria
 * @property string $tipoCadenaPago
 * @property string $nombreBancoEmisorCuentaOrdenanteEnCasoExtranjero
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
 */
class Feformapago extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'FEFormaPago';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descripcion', 'bancarizado', 'numeroOperacion', 'rfcEmisorCuentaOrdenante', 'cuentaOrdenante', 'patronCuentaOrdenante', 'rfcEmisorCuentaBeneficiario', 'cuentaBenenficiario', 'patronCuentaBeneficiaria', 'tipoCadenaPago', 'nombreBancoEmisorCuentaOrdenanteEnCasoExtranjero'], 'string'],
            [['versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['regEstado'], 'boolean'],
            [['regFechaUltimaModificacion'], 'safe'],
            [['formaPago'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'formaPagoID' => 'Forma Pago ID',
            'formaPago' => 'Forma Pago',
            'descripcion' => 'Descripcion',
            'bancarizado' => 'Bancarizado',
            'numeroOperacion' => 'Numero Operacion',
            'rfcEmisorCuentaOrdenante' => 'Rfc Emisor Cuenta Ordenante',
            'cuentaOrdenante' => 'Cuenta Ordenante',
            'patronCuentaOrdenante' => 'Patron Cuenta Ordenante',
            'rfcEmisorCuentaBeneficiario' => 'Rfc Emisor Cuenta Beneficiario',
            'cuentaBenenficiario' => 'Cuenta Benenficiario',
            'patronCuentaBeneficiaria' => 'Patron Cuenta Beneficiaria',
            'tipoCadenaPago' => 'Tipo Cadena Pago',
            'nombreBancoEmisorCuentaOrdenanteEnCasoExtranjero' => 'Nombre Banco Emisor Cuenta Ordenante En Caso Extranjero',
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
