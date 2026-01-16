<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "TemplatesReportes".
 *
 * @property int $templateReporteID
 * @property string $nombreTemplateReporte
 * @property string $logoTemplateReporte
 * @property string $encabezadoTemplateReporte
 * @property string $pieTemplateReporteL1
 * @property string $pieTemplateReporteL2
 * @property string $pieTemplateReporteL3
 * @property string $colorLinea
 * @property string $colorTituloTabla
 * @property string $colorTituloTexto
 * @property string $colorTextoFooter
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 */
class Templatereporte extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'TemplatesReportes';
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
            [['nombreTemplateReporte', 'pieTemplateReporteL1', 'pieTemplateReporteL2', 'pieTemplateReporteL3'], 'string', 'max' => 500],
            [['logoTemplateReporte'], 'string', 'max' => 450],
            [['encabezadoTemplateReporte'], 'string', 'max' => 300],
            [['colorLinea', 'colorTituloTabla', 'colorTituloTexto', 'colorTextoFooter'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'templateReporteID' => 'ID',
            'nombreTemplateReporte' => 'Nombre',
            'logoTemplateReporte' => 'Logo',
            'encabezadoTemplateReporte' => 'Encabezado',
            'pieTemplateReporteL1' => 'Pie Linea 1',
            'pieTemplateReporteL2' => 'Pie Linea 2',
            'pieTemplateReporteL3' => 'Pie Linea 3',
            'colorLinea' => 'Color Linea',
            'colorTituloTabla' => 'Color Titulo Tabla',
            'colorTituloTexto' => 'Color Titulo Texto',
            'colorTextoFooter' => 'Color Texto Footer',
            'versionRegistro' => 'Version Registro',
            'regEstado' => 'Reg Estado',
            'regFechaUltimaModificacion' => 'Reg Fecha Ultima Modificacion',
            'regUsuarioUltimaModificacion' => 'Reg Usuario Ultima Modificacion',
            'regFormularioUltimaModificacion' => 'Reg Formulario Ultima Modificacion',
            'regVersionUltimaModificacion' => 'Reg Version Ultima Modificacion',
        ];
    }
}
