<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
			
			$macCad = shell_exec('cat /sys/class/net/*/address');
			$lines=explode("\n", $macCad);
			$macAddr = 'ND'; 
			
			foreach($lines as $rmac){
				if($rmac != ""){
					$macAddr = $rmac;
					break;
				}
			}
			
			if (!$user){
				Yii::$app->session->setFlash('failure', "Usuario o contraseña incorrectos");
                $this->addError($attribute, 'Incorrect username or password.');
				
				//Yii::$app->db->createCommand("INSERT INTO LogAccesos(usuarioID, fechaAcceso, usuarioIp, usuarioMac, accesoExito, descripcionEvento, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion)VALUES('0', NOW(), '".$_SERVER['SERVER_ADDR']."', '".$macAddr."', 0, '".$this->username.", Usuario incorrecto', 1, 1, NOW(), '1', 1, 1)")->query();
				
				$btID = Yii::$app->globals->setBitacora("Acceso de usuario", "Nombre de usuario no encontrado ".$this->username, 0, "", 0, 1);				
				Yii::$app->globals->setEvento(1, 16, 0, 1, $btID, 'Nombre de usuario no encontrado');
				
			}else{
				$reglas = Yii::$app->db->createCommand('SELECT * FROM ReglasPassw')->queryOne();
				if($user->getIntentos() >= $reglas['maximoIntentosFallidos']){
						Yii::$app->session->setFlash('failure', "Usuario Bloqueado por seguridad, consulte con su administrador");
						$this->addError($attribute, 'Incorrect username or password.');
					
					//Yii::$app->db->createCommand("INSERT INTO LogAccesos(usuarioID, fechaAcceso, usuarioIp, usuarioMac, accesoExito, descripcionEvento, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion)VALUES('".$user->getId()."', NOW(), '".$_SERVER['REMOTE_ADDR']."', '".$macAddr."', 0, 'Usuario Bloqueado', 1, 1, NOW(), '1', 1, 1)")->query();
					$btID = Yii::$app->globals->setBitacora("Acceso de usuario", "Usuario bloqueado ".$this->username, 0, "", $user->getId(), 7);
					Yii::$app->globals->setEvento($user->getId(), 16, 0, 1, $btID, 'Intento de acceso usuario bloqueado');					
					
				}else{
					if($user->getActivo() == 0){
						Yii::$app->session->setFlash('failure', "El usuario esta inactivo, consulte con su administrador");
						$this->addError($attribute, 'Incorrect username or password.');
						
						//Yii::$app->db->createCommand("INSERT INTO LogAccesos(usuarioID, fechaAcceso, usuarioIp, usuarioMac, accesoExito, descripcionEvento, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion)VALUES('".$user->getId()."', NOW(), '".$_SERVER['REMOTE_ADDR']."', '".$macAddr."', 0, 'Usuario Inactivo', 1, 1, NOW(), '1', 1, 1)")->query();
						$btID = Yii::$app->globals->setBitacora("Acceso de usuario", "Usuario inactivo ".$this->username, 0, "", $user->getId(), 3);
						Yii::$app->globals->setEvento($user->getId(), 16, 0, 1, $btID, 'Intento de acceso usuario Inactivo');
						
					}else{
						if(!$user->validatePassword($this->password)) {
							$intentos = $user->getIntentos() + 1;
							Yii::$app->db->createCommand('UPDATE Usuarios SET intentosValidos = "'.$intentos.'" WHERE usuarioID ="'.$user->getId().'"')->query();
							
							//Yii::$app->db->createCommand("INSERT INTO LogAccesos(usuarioID, fechaAcceso, usuarioIp, usuarioMac, accesoExito, descripcionEvento, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion)VALUES('".$user->getId()."', NOW(), '".$_SERVER['REMOTE_ADDR']."', '".$macAddr."', 0, 'Contraseña incorrecta', 1, 1, NOW(), '1', 1, 1)")->query();
							$btID = Yii::$app->globals->setBitacora("Acceso de usuario", "Contraseña incorrecta ".$this->username, 0, "", $user->getId(),4);
							Yii::$app->globals->setEvento($user->getId(), 16, 0, 1, $btID, "Contraseña incorrecta");
							

							Yii::$app->session->setFlash('failure', "Usuario o contraseña incorrectos");
							$this->addError($attribute, 'Incorrect username or password.');
							
							
						}
					}
				}
			}
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
		
        if ($this->validate()) {
			Yii::$app->session->destroy();
			
			$db = Yii::$app->getDb();
			$dbName = $this->getDsnAttribute('dbname', $db->dsn);
			
			
			$user = $this->getUser();
			
			$qryReglas = "Select * from ReglasPassw where regEstado=1 limit 1";
			$permisosPass = Yii::$app->db->createCommand($qryReglas)->queryOne();
			
			$fecha_actualiza = $user->getFecha();
			$fechaActual = date('Y-m-d');
			
			$permisoFalse = false;
			$codigoRecupera = '36'.rand(9,10577).'s7';
			
			
			
			
			
				//dia
				if(isset($permisosPass['duracionActualizaPass']) and isset($permisosPass['codigoRecuperacionPassw'])){
					$dias = $permisosPass['duracionActualizaPass'];
					$fAdd = date("Y-m-d",strtotime($fecha_actualiza."+ ".$dias." days"));
					
					
					if($fechaActual > $fAdd){
						Yii::$app->db->createCommand("UPDATE Usuarios set codigoRecuperacionPassw='".$codigoRecupera."', cambioPass=1  where usuarioID='".$user->getId()."'")->query();
						$permisoFalse = true;

						//$url =  Url::toRoute('site/changepass&token='.md5($codigo).'&folio='.md5($id), true);
					}
					
					
				}
			
			if($user->getCambioPass() == '1'){
				Yii::$app->db->createCommand("UPDATE Usuarios set codigoRecuperacionPassw='".$codigoRecupera."'  where usuarioID='".$user->getId()."'")->query();
				Yii::$app->response->redirect(['site/changepass&token='.md5($codigoRecupera).'&folio='.md5($user->getId())]);
				return false;
			}
				
				
			
			
			if($permisoFalse == false ){			
				
				Yii::$app->session->set('token_id'.$dbName, $user->getId());
				Yii::$app->session->set('idiomaId', 1);
				Yii::$app->session->set('idiomaFlag', 'MX');
				Yii::$app->globals->cargarTraductor(1);
				Yii::$app->globals->cargarError(1);

				//logoBanner, iconoMenu, temaBanner, temaMenu, temaContenido, logoFooter, favIcon, titlePagina, footerPagina, btnAccion, btnSave, btnMenu, tiempoSesion
				$configData = Yii::$app->db->createCommand("SELECT * FROM ConfiguracionesSistema where configuracionesSistemaID='1'")->queryOne();

				$vfile = Yii::$app->basePath."/version";
				$vtxt = "";
				if(file_exists($vfile)){
					$vdata = file($vfile);
					if(isset($vdata[0])){
						$vtxt = $vdata[0];
					}

				}

				Yii::$app->session->set('logoBanner'.$dbName, $configData['logoBanner']);
				Yii::$app->session->set('icoBanner'.$dbName, $configData['iconoMenu']);
				Yii::$app->session->set('temaBanner'.$dbName, $configData['temaBanner']);
				Yii::$app->session->set('temaMenu'.$dbName, $configData['temaMenu']);
				Yii::$app->session->set('titlePagina'.$dbName, $configData['titlePagina']);
				Yii::$app->session->set('footerPagina'.$dbName, $configData['footerPagina'].$vtxt);
				Yii::$app->session->set('logoFooter'.$dbName, $configData['logoFooter']);
				Yii::$app->session->set('favIcon'.$dbName, $configData['favIcon']);
				Yii::$app->session->set('temaContenido'.$dbName, $configData['temaContenido']);
				Yii::$app->session->set('temaBtnAccion', $configData['btnAccion']);
				Yii::$app->session->set('temaBtnSave', $configData['btnSave']);
				Yii::$app->session->set('temaBtnMenu', $configData['btnMenu']);


				Yii::$app->db->createCommand('UPDATE Usuarios SET intentosValidos = "0" WHERE usuarioID ="'.$user->getId().'"')->query();
				$macCad = shell_exec('cat /sys/class/net/*/address');
				$lines=explode("\n", $macCad);
				$macAddr = 'ND'; 

				foreach($lines as $rmac){
					if($rmac != ""){
						$macAddr = $rmac;
						break;
					}
				}
				
				$btID = Yii::$app->globals->setBitacora("Acceso de usuario", "Usuario logueado con exito ".$this->username, 0, "", $user->getId(), 5);
				
				 //Yii::$app->db->createCommand("INSERT INTO LogAccesos(usuarioID, fechaAcceso, usuarioIp, usuarioMac, accesoExito, descripcionEvento, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion)VALUES('".$user->getId()."', NOW(), '".$_SERVER['REMOTE_ADDR']."', '".$macAddr."', 1, 'Usuario acceso correcto', 1, 1, NOW(), '".$user->getId()."', 1, 1)")->query();
				
				//Yii::$app->globals->setEvento($user->getId(), 1, 0);
				//return true;
				//$this->rememberMe ? 2200 : 2200
				//Yii::$app->user->authTimeout = $configData['tiempoSesion'];
				Yii::$app->session->set('tokenID', $user->getId());
				$timeUpdate = 3600*24*30;
				if(isset($configData['tiempoSesion'])){
					$timeUpdate = $configData['tiempoSesion'];
				}
				return Yii::$app->user->login($user, $timeUpdate );
			}else{
				Yii::$app->response->redirect(['site/changepass&token='.md5($codigoRecupera).'&folio='.md5($user->getId())]);
			}
			
        }
        return false;
    }
	
	public function GetClientMac(){
        $macAddr=false;
        $arp='arp -n';
        $lines=explode("\n", $arp);

        foreach($lines as $line){
            $cols=preg_split('/\s+/', trim($line));

            if ($cols[0]==$_SERVER['REMOTE_ADDR']){
                $macAddr=$cols[2];
            }
        }

        return $macAddr;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if($this->_user === false){
            $this->_user = Usuarios::findByUsername($this->username);
        }

        return $this->_user;
    }
	
	public function getDsnAttribute($name, $dsn)
    {
        if (preg_match('/' . $name . '=([^;]*)/', $dsn, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }
}
