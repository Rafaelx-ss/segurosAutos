<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "menus".
 *
 * @property int $menuID
 * @property string $nombreMenu
 * @property string $urlPagina
 * @property string $imagen
 * @property int $menuPadre
 * @property int $orden
 * @property int $establecimientoID
 * @property int $versionRegistro
 * @property int $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 */
class Menus extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Menus';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['menuID', 'menuPadre', 'orden', 'versionRegistro', 'regEstado', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['regFechaUltimaModificacion', 'textoID'], 'safe'],
            [['nombreMenu'], 'string', 'max' => 50],
            [['urlPagina', 'imagen'], 'string', 'max' => 500],
            [['menuID'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'menuID' => 'Menu ID',
            'nombreMenu' => 'Nombre Menu',
            'urlPagina' => 'Url Pagina',
            'imagen' => 'Imagen',
			'textoID' => 'textoID',
            'menuPadre' => 'Menu Padre',
            'orden' => 'Orden',
            'versionRegistro' => 'Version Registro',
            'regEstado' => 'Reg Estado',
            'regFechaUltimaModificacion' => 'Reg Fecha Ultima Modificacion',
            'regUsuarioUltimaModificacion' => 'Reg Usuario Ultima Modificacion',
            'regFormularioUltimaModificacion' => 'Reg Formulario Ultima Modificacion',
            'regVersionUltimaModificacion' => 'Reg Version Ultima Modificacion',
        ];
    }
}
