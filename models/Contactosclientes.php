<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ContactosClientes".
 *
 * @property int $contactoClienteID
 * @property string $contactoClienteNombre
 * @property int $clienteID
 * @property string $email
 * @property string $telefono
 * @property string $celular
 * @property int $tipoContactoID
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property Clientes $cliente
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 * @property TiposContactosEstablecimientos $tipoContacto
 */
class Contactosclientes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ContactosClientes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['clienteID', 'tipoContactoID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['regEstado'], 'boolean'],
            [['regFechaUltimaModificacion'], 'safe'],
            [['contactoClienteNombre'], 'string', 'max' => 250],
            [['email'], 'string', 'max' => 255],
            [['telefono', 'celular'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'contactoClienteID' => 'Contacto Cliente ID',
            'contactoClienteNombre' => 'Contacto Cliente Nombre',
            'clienteID' => 'Cliente ID',
            'email' => 'Email',
            'telefono' => 'Telefono',
            'celular' => 'Celular',
            'tipoContactoID' => 'Tipo Contacto ID',
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
