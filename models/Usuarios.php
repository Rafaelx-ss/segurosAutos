<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "usuarios".
 *
 * @property int $usuarioID
 * @property string $nombreUsuario
 * @property resource $passw
 * @property string $usuario
 * @property int $activoUsuario
 * @property string $correoUsuario
 * @property string $codigoRecuperacionPassw
 * @property string $fechaGeneracionCodigoRecuperacionPassw
 * @property int $intentosValidos
 * @property int $versionRegistro
 * @property int $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 * @property string $AuthKey
 */
class Usuarios extends \yii\db\ActiveRecord  implements \yii\web\IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Usuarios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
           // [['usuarioID', 'nombreUsuario', 'passw', 'usuario', 'activoUsuario', 'correoUsuario', 'intentosValidos', 'versionRegistro', 'regEstado', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion', 'AuthKey'], 'required'],
            [['activoUsuario', 'intentosValidos', 'versionRegistro', 'regEstado', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['passw'], 'string'],
            [['usuarioID', 'fechaGeneracionCodigoRecuperacionPassw', 'regFechaUltimaModificacion', 'fechaActualizaPass', 'primerLogin', 'cambioPass'], 'safe'],
            [['nombreUsuario', 'usuario', 'correoUsuario', 'codigoRecuperacionPassw'], 'string', 'max' => 250],
            [['AuthKey'], 'string', 'max' => 45],
            [['usuarioID'], 'unique','message' => 'El UsuarioID ya esta en uso.'],
			['usuario', 'unique', 'message' => 'El nombre de usuario ya esta en uso.'],
			//[['passw'], 'match', 'pattern' => '(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%]).{6,20}'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'usuarioID' => 'Usuario ID',
            'nombreUsuario' => 'Nombre Usuario',
            'passw' => 'Passw',
            'usuario' => 'Usuario',
            'activoUsuario' => 'Activo Usuario',
            'correoUsuario' => 'Correo Usuario',
            'codigoRecuperacionPassw' => 'Codigo Recuperacion Passw',
            'fechaGeneracionCodigoRecuperacionPassw' => 'Fecha Generacion Codigo Recuperacion Passw',
            'intentosValidos' => 'Intentos Validos',
            'versionRegistro' => 'Version Registro',
			'cambioPass' => 'Solicitar cambio',
            'regEstado' => 'Reg Estado',
            'regFechaUltimaModificacion' => 'Reg Fecha Ultima Modificacion',
            'regUsuarioUltimaModificacion' => 'Reg Usuario Ultima Modificacion',
            'regFormularioUltimaModificacion' => 'Reg Formulario Ultima Modificacion',
            'regVersionUltimaModificacion' => 'Reg Version Ultima Modificacion',
            'AuthKey' => 'Auth Key',
        ];
    }
	
	public function getCambioPass()
    {
        return $this->cambioPass;
    }
	
	public function getAuthKey()
    {
        return $this->AuthKey;
    }
	
	public function validateAuthKey($authKey)
    {
        return $this->AuthKey === $authKey;
    }
	
	public function getCodigo(){
		return $this->codigoRecuperacionPassw;	
	}
	
	public function getFecha(){
		return $this->fechaActualizaPass;	
	}
	
	public function getId()
    {
        return $this->usuarioID;
    }
	
	public function getActivo()
    {
        return $this->activoUsuario;
    }
	
	public function getIntentos()
    {
        return $this->intentosValidos;
    }
	
		
	public static function findIdentity($id)
    {
		return self::findOne($id);
    }
	
	public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new \yii\base\NoSupportedException();
    }
	
	public static function findByUsername($username)
    {        
        return self::findOne(['usuario'=>$username]);
    }
	
	
	public function validatePassword($password)
    {
		if(password_verify($password, $this->passw)) {
			return true;
		} else {
			return false;
		}
    }
}
