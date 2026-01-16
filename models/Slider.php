<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ConfiguracionesSlider".
 *
 * @property int $configuracionesSliderID
 * @property string $tituloSlider
 * @property string $contenidoSlider
 * @property string $imagenSlider
 * @property int $ordenSlider
 * @property bool $activoConfiguracionesSlider
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 */
class Slider extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ConfiguracionesSlider';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ordenSlider', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['activoConfiguracionesSlider', 'regEstado'], 'boolean'],
            [['regFechaUltimaModificacion'], 'safe'],
            [['tituloSlider'], 'string', 'max' => 450],
            [['contenidoSlider'], 'string', 'max' => 800],
            [['imagenSlider'], 'string', 'max' => 145],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'configuracionesSliderID' => 'ID',
            'tituloSlider' => 'Titulo',
            'contenidoSlider' => 'Contenido',
            'imagenSlider' => 'Imagen',
            'ordenSlider' => 'Orden',
            'activoConfiguracionesSlider' => 'Activo',
            'versionRegistro' => 'Version Registro',
            'regEstado' => 'Reg Estado',
            'regFechaUltimaModificacion' => 'Reg Fecha Ultima Modificacion',
            'regUsuarioUltimaModificacion' => 'Reg Usuario Ultima Modificacion',
            'regFormularioUltimaModificacion' => 'Reg Formulario Ultima Modificacion',
            'regVersionUltimaModificacion' => 'Reg Version Ultima Modificacion',
        ];
    }
}
