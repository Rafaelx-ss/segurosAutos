<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "TextosIdiomas".
 *
 * @property int $textoIdiomaID
 * @property string $texto
 * @property int $textoID
 * @property int $idiomaID
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property Textos $texto0
 * @property Idiomas $idioma
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 */
class Traducciones extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'TextosIdiomas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['textoID', 'idiomaID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['regEstado'], 'boolean'],
            [['regFechaUltimaModificacion'], 'safe'],
            [['texto'], 'string', 'max' => 150],
            [['textoID'], 'exist', 'skipOnError' => true, 'targetClass' => Textos::className(), 'targetAttribute' => ['textoID' => 'textoID']],
            [['idiomaID'], 'exist', 'skipOnError' => true, 'targetClass' => Idiomas::className(), 'targetAttribute' => ['idiomaID' => 'idiomaID']],
            [['regUsuarioUltimaModificacion'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::className(), 'targetAttribute' => ['regUsuarioUltimaModificacion' => 'usuarioID']],
            [['regFormularioUltimaModificacion'], 'exist', 'skipOnError' => true, 'targetClass' => Formularios::className(), 'targetAttribute' => ['regFormularioUltimaModificacion' => 'formularioID']],
            [['regVersionUltimaModificacion'], 'exist', 'skipOnError' => true, 'targetClass' => Versiones::className(), 'targetAttribute' => ['regVersionUltimaModificacion' => 'versionID']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'textoIdiomaID' => 'Texto Idioma ID',
            'texto' => 'Texto',
            'textoID' => 'Texto ID',
            'idiomaID' => 'Idioma ID',
            'versionRegistro' => 'Version Registro',
            'regEstado' => 'Reg Estado',
            'regFechaUltimaModificacion' => 'Reg Fecha Ultima Modificacion',
            'regUsuarioUltimaModificacion' => 'Reg Usuario Ultima Modificacion',
            'regFormularioUltimaModificacion' => 'Reg Formulario Ultima Modificacion',
            'regVersionUltimaModificacion' => 'Reg Version Ultima Modificacion',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTexto0()
    {
        return $this->hasOne(Textos::className(), ['textoID' => 'textoID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdioma()
    {
        return $this->hasOne(Idiomas::className(), ['idiomaID' => 'idiomaID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegUsuarioUltimaModificacion0()
    {
        return $this->hasOne(Usuarios::className(), ['usuarioID' => 'regUsuarioUltimaModificacion']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegFormularioUltimaModificacion0()
    {
        return $this->hasOne(Formularios::className(), ['formularioID' => 'regFormularioUltimaModificacion']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegVersionUltimaModificacion0()
    {
        return $this->hasOne(Versiones::className(), ['versionID' => 'regVersionUltimaModificacion']);
    }
}
