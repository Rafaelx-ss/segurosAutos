<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "FEClaveProdServ".
 *
 * @property int $claveProdServID
 * @property string $claveProdServ
 * @property string $descripcion
 * @property string $fechaDeInicioDeVigencia
 * @property string $fechaDeFinDeVigencia
 * @property string $incluirIVATraslado
 * @property string $incluirIEPSTraslado
 * @property string $complementoQueDebeIncluir
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
class Feclaveprodserv extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'FEClaveProdServ';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descripcion', 'incluirIVATraslado', 'incluirIEPSTraslado', 'complementoQueDebeIncluir'], 'string'],
            [['fechaDeInicioDeVigencia', 'fechaDeFinDeVigencia', 'regFechaUltimaModificacion'], 'safe'],
            [['versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['regEstado'], 'boolean'],
            [['claveProdServ'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'claveProdServID' => 'Clave Prod Serv ID',
            'claveProdServ' => 'Clave Prod Serv',
            'descripcion' => 'Descripcion',
            'fechaDeInicioDeVigencia' => 'Fecha De Inicio De Vigencia',
            'fechaDeFinDeVigencia' => 'Fecha De Fin De Vigencia',
            'incluirIVATraslado' => 'Incluir Iva Traslado',
            'incluirIEPSTraslado' => 'Incluir Ieps Traslado',
            'complementoQueDebeIncluir' => 'Complemento Que Debe Incluir',
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
