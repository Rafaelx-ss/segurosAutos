<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "campos".
 *
 * @property int $campoID
 * @property string $nombreCampo
 * @property string $tipoControl
 * @property string $longitud
 * @property bool $campoPK
 * @property bool $campoFK
 * @property string $controlQuery
 * @property bool $visible
 * @property int $orden
 * @property string $tipoCampo
 * @property bool $campoRequerido
 * @property string $textField
 * @property string $valueField
 * @property string $valorDefault
 * @property string $CSS
 * @property int $catalogoID
 * @property int $textoID
 * @property int $catalogoReferenciaID
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 */
class Campos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Campos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['campoPK', 'campoFK', 'visible', 'campoRequerido', 'regEstado'], 'boolean'],
            [['controlQuery', 'CSS'], 'string'],
            [['orden', 'catalogoID', 'textoID', 'catalogoReferenciaID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['regFechaUltimaModificacion'], 'safe'],
            [['nombreCampo', 'tipoCampo', 'textField', 'valueField', 'valorDefault'], 'string', 'max' => 200],
            [['tipoControl'], 'string', 'max' => 100],
            [['longitud'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'campoID' => 'Campo ID',
            'nombreCampo' => 'Nombre Campo',
            'tipoControl' => 'Tipo Control',
            'longitud' => 'Longitud',
            'campoPK' => 'Pk',
            'campoFK' => 'Fk',
            'controlQuery' => 'Control Query',
            'visible' => 'Visible',
            'orden' => 'Orden',
            'tipoCampo' => 'Tipo Campo',
            'campoRequerido' => 'Requerido',
            'textField' => 'Texto select',
            'valueField' => 'Valor select',
            'valorDefault' => 'Valor Default',
            'CSS' => 'Css',
            'catalogoID' => 'Catalogo ID',
            'textoID' => 'Texto ID',
            'catalogoReferenciaID' => 'Catalogo Referencia ID',
            'versionRegistro' => 'Version Registro',
            'regEstado' => 'Reg Estado',
            'regFechaUltimaModificacion' => 'Reg Fecha Ultima Modificacion',
            'regUsuarioUltimaModificacion' => 'Reg Usuario Ultima Modificacion',
            'regFormularioUltimaModificacion' => 'Reg Formulario Ultima Modificacion',
            'regVersionUltimaModificacion' => 'Reg Version Ultima Modificacion',
        ];
    }
	
	public function getIdTextos()
    {
        return $this->hasOne(Textos::className(), ['textoID' => 'textoID']);
    }
	
	
}
