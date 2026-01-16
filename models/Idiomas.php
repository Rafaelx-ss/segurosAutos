<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Idiomas".
 *
 * @property int $idiomaID
 * @property string $iconIdioma
 * @property string $nombreIdioma
 * @property bool $activoIdioma
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 * @property TextosIdiomas[] $textosIdiomas
 */
class Idiomas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Idiomas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['activoIdioma', 'regEstado'], 'boolean'],
            [['versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['regFechaUltimaModificacion'], 'safe'],
            [['iconIdioma'], 'string', 'max' => 45],
            [['nombreIdioma'], 'string', 'max' => 150],
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
            'idiomaID' => 'Idioma ID',
            'iconIdioma' => 'Icon Idioma',
            'nombreIdioma' => 'Nombre Idioma',
            'activoIdioma' => 'Activo Idioma',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTextosIdiomas()
    {
        return $this->hasMany(TextosIdiomas::className(), ['idiomaID' => 'idiomaID']);
    }
}
