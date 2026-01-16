<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reportesCampos".
 *
 * @property int $reporteCampoID
 * @property int $reporteConfiguracionID
 * @property string $nombreCampo
 * @property bool $visible
 * @property bool $searchVisible
 * @property int $orden
 * @property int $textoID
 * @property string $tipoControl
 * @property string $controlQuery
 * @property string $queryValor
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 */
class Reportescampos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ReportesCampos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reporteConfiguracionID', 'orden', 'textoID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['visible', 'searchVisible', 'regEstado'], 'boolean'],
            [['controlQuery'], 'string'],
            [['regFechaUltimaModificacion', 'aliasTabla', 'sumarCampo'], 'safe'],
            [['nombreCampo', 'tipoControl'], 'string', 'max' => 500],
            [['queryValor'], 'string', 'max' => 800],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'reporteCampoID' => 'ID',
            'reporteConfiguracionID' => 'Reportes ID',
            'nombreCampo' => 'Nombre',
			'aliasTabla' => 'Alias Tabla',
            'visible' => 'Visible',
            'searchVisible' => 'Search Visible',
            'orden' => 'Orden',
            'textoID' => 'Texto',
            'tipoControl' => 'Tipo Control',
			'sumarCampo' => 'Sumar columna',
            'controlQuery' => 'Query',
            'queryValor' => 'Valor',
            'versionRegistro' => 'Version Registro',
            'regEstado' => 'Reg Estado',
            'regFechaUltimaModificacion' => 'Reg Fecha Ultima Modificacion',
            'regUsuarioUltimaModificacion' => 'Reg Usuario Ultima Modificacion',
            'regFormularioUltimaModificacion' => 'Reg Formulario Ultima Modificacion',
            'regVersionUltimaModificacion' => 'Reg Version Ultima Modificacion',
        ];
    }
}
