<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "CombosAnidados".
 *
 * @property int $comboAnidadoID
 * @property int $catalogoID
 * @property string $tipoCombo
 * @property int $campoIDPadre
 * @property int $campoIDdependiente
 * @property string $controlQuery
 * @property string $queryValue
 * @property string $queryText
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 */
class Combos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'CombosAnidados';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['catalogoID', 'campoIDPadre', 'campoIDdependiente', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['controlQuery'], 'string'],
            [['regEstado'], 'boolean'],
            [['regFechaUltimaModificacion', 'activoCombo', 'parametrosQuery'], 'safe'],
            [['queryValue', 'queryText'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'comboAnidadoID' => 'ID',
            'catalogoID' => 'Catalogo',
            'campoIDPadre' => 'Padre',
            'campoIDdependiente' => 'Dependiente',
            'controlQuery' => 'Control Query',
            'queryValue' => 'Valor',
            'queryText' => 'Texto',
			'parametrosQuery' => 'Dependiente Adicional',
			'activoCombo' => 'Activo',
            'versionRegistro' => 'Version Registro',
            'regEstado' => 'Reg Estado',
            'regFechaUltimaModificacion' => 'Reg Fecha Ultima Modificacion',
            'regUsuarioUltimaModificacion' => 'Reg Usuario Ultima Modificacion',
            'regFormularioUltimaModificacion' => 'Reg Formulario Ultima Modificacion',
            'regVersionUltimaModificacion' => 'Reg Version Ultima Modificacion',
        ];
    }
	
	
	public function getIdPadre(){
       return $this->hasOne(Campos::className(), ['campoID' => 'campoIDPadre'])->alias('c1');
    }
	
	public function getIdDependiente(){
       return $this->hasOne(Campos::className(), ['campoID' => 'campoIDdependiente'])->alias('c2');
    }
}
