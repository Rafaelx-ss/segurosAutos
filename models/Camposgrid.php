<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "camposgrid".
 *
 * @property int $campoGridID
 * @property string $nombreCampo
 * @property bool $visible
 * @property int $orden
 * @property int $textoID
 * @property int $catalogoID
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 */
class Camposgrid extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'CamposGrid';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['visible', 'regEstado'], 'boolean'],
            [['orden', 'textoID', 'catalogoID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['regFechaUltimaModificacion', 'catalogoReferenciaID', 'tipoControl', 'textField', 'valueField', 'searchVisible', 'valorDefault', 'controlQuery', 'queryValor', 'searchQuery'], 'safe'],
            [['nombreCampo'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'campoGridID' => 'Campo Grid ID',
            'nombreCampo' => 'Nombre Campo',
            'visible' => 'Visible',
            'orden' => 'Orden',
            'textoID' => 'Texto ID',
			'tipoControl' => 'Tipo Control',
            'catalogoID' => 'Catalogo ID',
			'catalogoReferenciaID' => 'Catalogo de referencia',
			'textField' => 'Texto select', 
			'valueField' => 'Valor Select',
			'searchVisible' => 'Busqueda',
            'versionRegistro' => 'Version Registro',
            'regEstado' => 'Reg Estado',
            'regFechaUltimaModificacion' => 'Reg Fecha Ultima Modificacion',
            'regUsuarioUltimaModificacion' => 'Reg Usuario Ultima Modificacion',
            'regFormularioUltimaModificacion' => 'Reg Formulario Ultima Modificacion',
            'regVersionUltimaModificacion' => 'Reg Version Ultima Modificacion',
			'valorDefault'=>'Valores Select consulta',
			'controlQuery' => 'Control Query',
			'searchQuery' => 'Search Query',
			'queryValor' => 'Valores Query (separados por ,)',
        ];
    }
	
	public function getIdTextos()
    {
        return $this->hasOne(Textos::className(), ['textoID' => 'textoID']);
    }
}
