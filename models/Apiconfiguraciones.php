<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ApiListaConfiguraciones".
 *
 * @property int $apiListaConfiguracionID
 * @property string $usuarioApiLista
 * @property resource $passwordApiLista
 * @property string $rutaApiLista
 * @property string $identificadorApiLista
 * @property string $tipoSolicitudApiLista
 * @property int $aplicacionID
 * @property bool $versionActual
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 */
class Apiconfiguraciones extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ApiListaConfiguraciones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['passwordApiLista', 'rutaApiLista'], 'string'],
            [['aplicacionID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['versionActual', 'regEstado'], 'boolean'],
            [['regFechaUltimaModificacion'], 'safe'],
            [['usuarioApiLista'], 'string', 'max' => 450],
            [['identificadorApiLista'], 'string', 'max' => 20],
            [['tipoSolicitudApiLista'], 'string', 'max' => 5],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'apiListaConfiguracionID' => 'ID',
            'usuarioApiLista' => 'Usuario',
            'passwordApiLista' => 'Contraseña',
            'rutaApiLista' => 'Ruta Api',
            'identificadorApiLista' => 'Identificador Api',
            'tipoSolicitudApiLista' => 'Tipo Solicitud',
            'aplicacionID' => 'Aplicacion',
            'versionActual' => 'Actual',
            'versionRegistro' => 'Version',
            'regEstado' => 'Reg Estado',
            'regFechaUltimaModificacion' => 'Reg Fecha Ultima Modificacion',
            'regUsuarioUltimaModificacion' => 'Reg Usuario Ultima Modificacion',
            'regFormularioUltimaModificacion' => 'Reg Formulario Ultima Modificacion',
            'regVersionUltimaModificacion' => 'Reg Version Ultima Modificacion',
        ];
    }
	
	public function getIdAplicaciones()
    {
        return $this->hasOne(Aplicaciones::className(), ['aplicacionID' => 'aplicacionID']);
    }
}
