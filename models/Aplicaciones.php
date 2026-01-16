<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Aplicaciones".
 *
 * @property int $aplicacionID
 * @property string $nombreAplicacion
 * @property bool $activoAplicacion
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property Apis[] $apis
 * @property AplicacionesGrupos[] $aplicacionesGrupos
 * @property MigracionApis[] $migracionApis
 * @property Apis[] $apis0
 * @property MigracionCatalogos[] $migracionCatalogos
 * @property Catalogos[] $catalogos
 * @property MigracionFormularios[] $migracionFormularios
 * @property Formularios[] $formularios
 * @property MigracionMenus[] $migracionMenuses
 * @property Menus[] $menus
 * @property MigracionReportes[] $migracionReportes
 * @property ReportesConfiguraciones[] $reporteConfiguracions
 * @property ModuloSoftwareBrentec[] $moduloSoftwareBrentecs
 * @property Scripts[] $scripts
 */
class Aplicaciones extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Aplicaciones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['activoAplicacion', 'regEstado'], 'boolean'],
            [['versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['regFechaUltimaModificacion'], 'safe'],
            [['nombreAplicacion'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'aplicacionID' => 'Aplicacion ID',
            'nombreAplicacion' => 'Nombre Aplicacion',
            'activoAplicacion' => 'Activo Aplicacion',
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
