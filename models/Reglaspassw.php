<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ReglasPassw".
 *
 * @property int $minimioLongitudPassw
 * @property int $maximoIntentosFallidos
 * @property int $tiempoCaducidadCodigoRecuperacionPassw
 * @property int $tiempoCaducidadInactivadadPassw
 * @property int $contieneMayuscula
 * @property int $contieneMinusculas
 * @property int $contieneCaracteresEspeciales
 * @property int $contieneNumeros
 * @property int $duracionActualizaPass Cantidad de tiempo para actualizar
 * @property int $cantidadPassRepetidos
 * @property int $tiempoAlmacenadoPassword
 * @property bool $contieneRepetidos
 * @property int $cantidadRepetidos
 * @property bool $contieneConsecutivos
 * @property int $cantidadConsecutivos
 * @property int $versionRegistro
 * @property int $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Versiones $regVersionUltimaModificacion0
 */
class Reglaspassw extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ReglasPassw';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['minimioLongitudPassw', 'maximoIntentosFallidos', 'tiempoCaducidadCodigoRecuperacionPassw', 'tiempoCaducidadInactivadadPassw', 'contieneMayuscula', 'contieneMinusculas', 'contieneCaracteresEspeciales', 'contieneNumeros', 'duracionActualizaPass', 'cantidadPassRepetidos', 'tiempoAlmacenadoPassword', 'cantidadRepetidos', 'cantidadConsecutivos', 'versionRegistro', 'regEstado', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['contieneRepetidos', 'contieneConsecutivos'], 'boolean'],
            [['regFechaUltimaModificacion'], 'safe'],
            [['minimioLongitudPassw', 'maximoIntentosFallidos', 'tiempoCaducidadCodigoRecuperacionPassw', 'tiempoCaducidadInactivadadPassw', 'contieneMayuscula', 'contieneCaracteresEspeciales', 'contieneNumeros'], 'unique', 'targetAttribute' => ['minimioLongitudPassw', 'maximoIntentosFallidos', 'tiempoCaducidadCodigoRecuperacionPassw', 'tiempoCaducidadInactivadadPassw', 'contieneMayuscula', 'contieneCaracteresEspeciales', 'contieneNumeros']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'minimioLongitudPassw' => 'Minimio Longitud Passw',
            'maximoIntentosFallidos' => 'Maximo Intentos Fallidos',
            'tiempoCaducidadCodigoRecuperacionPassw' => 'Tiempo Caducidad Codigo Recuperacion Passw',
            'tiempoCaducidadInactivadadPassw' => 'Tiempo Caducidad Inactivadad Passw',
            'contieneMayuscula' => 'Contiene Mayuscula',
            'contieneMinusculas' => 'Contiene Minusculas',
            'contieneCaracteresEspeciales' => 'Contiene Caracteres Especiales',
            'contieneNumeros' => 'Contiene Numeros',
            'duracionActualizaPass' => 'Duracion Actualiza Pass',
            'cantidadPassRepetidos' => 'Cantidad Pass Repetidos',
            'tiempoAlmacenadoPassword' => 'Tiempo Almacenado Password',
            'contieneRepetidos' => 'Contiene Repetidos',
            'cantidadRepetidos' => 'Cantidad Repetidos',
            'contieneConsecutivos' => 'Contiene Consecutivos',
            'cantidadConsecutivos' => 'Cantidad Consecutivos',
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
