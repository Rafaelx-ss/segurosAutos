<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ReportesConfiguraciones".
 *
 * @property int $reporteConfiguracionID
 * @property int $templateReporteID
 * @property string $nombreReporte
 * @property string $queryReporte
 * @property string $columnasReporte
 * @property bool $imprimirLogoPdf
 * @property bool $imprimirEncabezado
 * @property bool $imprimirFechaHora
 * @property bool $imprimirNombreUsuario
 * @property bool $imprimirLogoExcel
 * @property bool $imprimirPie
 * @property bool $imprimirEncabezadoExcel
 * @property bool $imprimirFechaHoraExcel
 * @property bool $imprimirNombreUsuarioExcel
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 */
class Reportesconfig extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ReportesConfiguraciones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['templateReporteID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['queryReporte'], 'string'],
            [['imprimirLogoPdf', 'imprimirEncabezado', 'imprimirFechaHora', 'imprimirNombreUsuario', 'imprimirLogoExcel', 'imprimirPie', 'imprimirEncabezadoExcel', 'imprimirFechaHoraExcel', 'imprimirNombreUsuarioExcel', 'regEstado'], 'boolean'],
            [['regFechaUltimaModificacion', 'orientacionPagina'], 'safe'],
            [['nombreReporte'], 'string', 'max' => 500],
            [['columnasReporte'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'reporteConfiguracionID' => 'ID',
            'templateReporteID' => 'Template ID',
            'nombreReporte' => 'Nombre',
            'queryReporte' => 'Query Reporte',
			'orientacionPagina' => 'Orientación',
            'columnasReporte' => 'Columnas Reporte',
            'imprimirLogoPdf' => 'Imprimir Logo Pdf',
            'imprimirEncabezado' => 'Imprimir Encabezado Pdf',
            'imprimirFechaHora' => 'Imprimir Fecha Hora Pdf',
            'imprimirNombreUsuario' => 'Imprimir Nombre Usuario Pdf',
            'imprimirLogoExcel' => 'Imprimir Logo Excel',
            'imprimirPie' => 'Imprimir Pie Pdf',
            'imprimirEncabezadoExcel' => 'Imprimir Encabezado Excel',
            'imprimirFechaHoraExcel' => 'Imprimir Fecha Hora Excel',
            'imprimirNombreUsuarioExcel' => 'Imprimir Nombre Usuario Excel',
            'versionRegistro' => 'Version Registro',
            'regEstado' => 'Reg Estado',
            'regFechaUltimaModificacion' => 'Reg Fecha Ultima Modificacion',
            'regUsuarioUltimaModificacion' => 'Reg Usuario Ultima Modificacion',
            'regFormularioUltimaModificacion' => 'Reg Formulario Ultima Modificacion',
            'regVersionUltimaModificacion' => 'Reg Version Ultima Modificacion',
        ];
    }
	
	public function getIdTemplete(){
       return $this->hasOne(Templatereporte::className(), ['templateReporteID' => 'templateReporteID']);
    }
}
