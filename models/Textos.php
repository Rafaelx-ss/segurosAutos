<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Textos".
 *
 * @property int $textoID
 * @property string $nombreTexto
 * @property bool $activoTexto
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property Acciones[] $acciones
 * @property Campos[] $campos
 * @property CamposGrid[] $camposGrs
 * @property Formularios[] $formularios
 * @property PantallasPOS[] $pantallasPOSs
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 * @property TextosIdiomas[] $textosIdiomas
 */
class Textos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Textos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['activoTexto', 'regEstado'], 'boolean'],
            [['versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['tipoTexto', 'regFechaUltimaModificacion'], 'safe'],
            [['nombreTexto'], 'string', 'max' => 150],
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
            'textoID' => 'ID',
			'tipoTexto' => 'Tipo',
            'nombreTexto' => 'Nombre',
            'activoTexto' => 'Activo',
            'versionRegistro' => 'Version',
            'regEstado' => 'Reg Estado',
            'regFechaUltimaModificacion' => 'Fecha Ultima Modificacion',
            'regUsuarioUltimaModificacion' => 'Usuario Ultima Modificacion',
            'regFormularioUltimaModificacion' => 'Formulario Ultima Modificacion',
            'regVersionUltimaModificacion' => 'Version Ultima Modificacion',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcciones()
    {
        return $this->hasMany(Acciones::className(), ['textoID' => 'textoID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCampos()
    {
        return $this->hasMany(Campos::className(), ['textoID' => 'textoID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCamposGrs()
    {
        return $this->hasMany(CamposGrid::className(), ['textoID' => 'textoID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormularios()
    {
        return $this->hasMany(Formularios::className(), ['textoID' => 'textoID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPantallasPOSs()
    {
        return $this->hasMany(PantallasPOS::className(), ['textoID' => 'textoID']);
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
        return $this->hasMany(TextosIdiomas::className(), ['textoID' => 'textoID']);
    }
}
