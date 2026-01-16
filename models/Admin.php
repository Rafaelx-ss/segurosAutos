<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_admin".
 *
 * @property int $Id_admin
 * @property string $Tipo_user
 * @property string $Nombre_admin
 * @property string $User_admin
 * @property string $Pass_hadmin
 * @property string $Pass_radmin
 * @property string $AuthKey
 * @property string $Status_admin
 */

//esto hay que agregarlo para el login
class Admin extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * {@inheritdoc}
     */
	const ROLE_USER = 'Usuario';
	const ROLE_VIEW = 'Invitado';
  	const ROLE_ADMIN = 'Administrador';
	const ROLE_RH = 'RH';
	const ROLE_VEHICULOS = 'Vehiculos';
	
    public static function tableName()
    {
        return 'tbl_admin';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Tipo_user', 'Nombre_admin', 'User_admin',  'Status_admin'], 'required'],
            [['Tipo_user'], 'string', 'max' => 20],
			['User_admin', 'unique', 'message' => 'El nombre de usuario ya esta en uso.'],
            [['Nombre_admin', 'User_admin', 'Pass_hadmin', 'Pass_radmin', 'AuthKey', 'Status_admin'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id_admin' => 'ID',
            'Tipo_user' => 'Tipo Usuario',
            'Nombre_admin' => 'Nombre',
            'User_admin' => 'Ususario',
            'Pass_hadmin' => 'Pass',
            'Pass_radmin' => 'Contraseña',
            'AuthKey' => 'Key',
            'Status_admin' => 'Status',
        ];
    }
	
	
	public function getAuthKey()
    {
        return $this->AuthKey;
    }
	
	public function validateAuthKey($authKey)
    {
        return $this->AuthKey === $authKey;
    }
	
	public function getId()
    {
        return $this->Id_admin;
    }
	
	 public function getacceso()
    {
        return $this->Tipo_user;
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
        return self::findOne(['User_admin'=>$username]);
    }
	
	public function validatePassword($password)
    {
		$pass = 's'.md5($password).'-07';
        return $this->Pass_hadmin === $pass;
    }
		
}
