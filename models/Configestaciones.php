<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "configestaciones".
 *
 * @property int $configEstacionID
 * @property string $colorBombasc
 * @property int $tamanoBomba
 * @property int $logoIzquierda
 * @property string $logoDerecha
 * @property string $Titulo
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 */
class Configestaciones extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'configestaciones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tamanoBomba', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion', 'numRegistros'], 'integer'],
            [['regEstado'], 'boolean'],
            [['regFechaUltimaModificacion'], 'safe'],
            [['colorBombasc'], 'string', 'max' => 20],
            [['logoDerecha',  'logoIzquierda', 'titulo'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'configEstacionID' => 'ID',
            'colorBombasc' => 'Color bomba Default',
            'tamanoBomba' => 'Tamaño Bomba Default',
            'logoIzquierda' => 'Logo Izquierda',
            'logoDerecha' => 'Logo Derecha',
            'titulo' => 'Titulo',
			'numRegistros' => 'Numero de registros',
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
