<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ReportesPdf".
 *
 * @property int $reportesPdfID
 * @property int $establecimientoID
 * @property string $folioReporte
 * @property string $arrayCampos
 * @property string $codigoReporte
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 */
class Reportespdf extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ReportesPdf';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['establecimientoID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['arrayCampos', 'codigoReporte'], 'string'],
            [['regEstado'], 'boolean'],
            [['regFechaUltimaModificacion', 'headerReporte', 'footerReporte', 'paginacionReporte', 'altoHeader', 'altoFooter'], 'safe'],
            [['folioReporte'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'reportesPdfID' => 'Reportes Pdf ID',
            'establecimientoID' => 'Establecimiento',
            'folioReporte' => 'Folio Reporte',
            'arrayCampos' => 'Array Campos',
			'headerReporte' => 'Header',
			'footerReporte' => 'Footer',
			'altoHeader' => 'Alto cabecera (pixeles)',
			'altoFooter' => 'Alto pie (pixeles)',
            'codigoReporte' => 'Codigo Reporte',
			'paginacionReporte' => 'Requiere Paginación',
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
 public function getIdEstablecimientos()
    {
       return $this->hasOne(Establecimientos::className(), ['establecimientoID' => 'establecimientoID']);
    }


}
