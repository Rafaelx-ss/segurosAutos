<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "MenusPOS".
 *
 * @property int $menuPOSID
 * @property string $nombreMenuPOS
 * @property int $tipoPosID
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property TiposPOS $tipoPos
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 * @property PantallasPOS[] $pantallasPOSs
 */
class Menuspos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'MenusPOS';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipoPosID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['regEstado'], 'boolean'],
            [['regFechaUltimaModificacion'], 'safe'],
            [['nombreMenuPOS'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'menuPOSID' => 'Menu Posid',
            'nombreMenuPOS' => 'Nombre Menu Pos',
            'tipoPosID' => 'Tipo Pos ID',
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
 public function getIdTipospos()
    {
       return $this->hasOne(Tipospos::className(), ['tipoPosID' => 'tipoPosID']);
    }


}
