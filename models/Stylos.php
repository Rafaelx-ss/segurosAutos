<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ConfiguracionesSistema".
 *
 * @property int $configuracionesSistemaID
 * @property string $logoLogin
 * @property string $logoBanner
 * @property string $iconoMenu
 * @property string $temaBanner
 * @property string $temaMenu
 * @property string $temaContenido
 * @property bool $activoConfiguracionesSistema
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 */
class Stylos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ConfiguracionesSistema';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['activoConfiguracionesSistema', 'regEstado'], 'boolean'],
            [['versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['regFechaUltimaModificacion', 'logoFooter', 'favIcon', 'titlePagina', 'footerPagina', 'btnAccion', 'btnSave', 'btnMenu', 'tiempoSesion'], 'safe'],
            [['logoLogin', 'logoBanner', 'iconoMenu', 'temaBanner', 'temaMenu', 'temaContenido'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'configuracionesSistemaID' => 'ID',
            'logoLogin' => 'Logo Inicio',
            'logoBanner' => 'Logo Banner',
            'iconoMenu' => 'Icono Menu',
            'temaBanner' => 'Tema Banner',
            'temaMenu' => 'Tema Menu',
            'temaContenido' => 'Tema Contenido',
            'activoConfiguracionesSistema' => 'Activo',
            'versionRegistro' => 'Version Registro',
            'regEstado' => 'Reg Estado',
            'regFechaUltimaModificacion' => 'Reg Fecha Ultima Modificacion',
            'regUsuarioUltimaModificacion' => 'Reg Usuario Ultima Modificacion',
            'regFormularioUltimaModificacion' => 'Reg Formulario Ultima Modificacion',
            'regVersionUltimaModificacion' => 'Reg Version Ultima Modificacion',
			'logoFooter'=>'Logo footer', 
			'favIcon'=>'favIcon', 
			'titlePagina'=>'Titulo Pagina', 
			'footerPagina'=>'copyright Footer',
			'btnAccion' => 'Boton Acciones', 
			'btnSave'=>'Boton guardar', 
			'btnMenu'=>'Boton Menus',
			'tiempoSesion' => 'Tiempo de la sesion (segundos)',
        ];
    }
}
