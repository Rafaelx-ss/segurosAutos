<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\helpers\Url;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
	
	
    public function behaviors()
    {
		$usuarioIdToken = '';
		if(isset(Yii::$app->user->identity->usuarioID)){
			$usuarioIdToken = Yii::$app->user->identity->usuarioID;
		}

		
		$ventasfacturacion = "";
		$sqltomysql = "";
		$volumetricos = "";
		$neoreportes = "";
		$importarfactura = "";
		$importarError = "";
		
		if(isset($_GET['f']) and isset($_GET['r'])){
			$frmSeguridad = explode("/", $_GET['r']);
			
			$permisosBtn = Yii::$app->db->createCommand("SELECT Acciones.accionID, Acciones.nombreAccion, Formularios.nombreFormulario, Formularios.tipoFormularioID, Formularios.urlArchivo FROM Acciones
			inner join AccionesFormularios on Acciones.accionID=AccionesFormularios.accionID 
			inner join Formularios on AccionesFormularios.formularioID=Formularios.formularioID
			inner join PerfilAccionFormulario on AccionesFormularios.accionFormularioID =PerfilAccionFormulario.accionFormularioID
			inner join PerfilesCompuestos on PerfilAccionFormulario.perfilID=PerfilesCompuestos.perfilID
			inner join Usuarios on Usuarios.usuarioID = PerfilesCompuestos.usuarioID
			where md5(Formularios.formularioID)='".$_GET['f']."' and AccionesFormularios.estadoAccion=1 and AccionesFormularios.regEstado=1 and PerfilAccionFormulario.regEstado=1 and PerfilAccionFormulario.activoPerfilAccionFormulario=1 and Usuarios.usuarioID='".$usuarioIdToken."' group by AccionesFormularios.accionFormularioID")->queryAll();
			
			foreach($permisosBtn as $dataPbtn){
				$urlSeguridad = explode("/", $dataPbtn['urlArchivo']);
				if(isset($urlSeguridad[0]) and isset($frmSeguridad[0])){
					if($frmSeguridad[0] == $urlSeguridad[0]){
						if(isset($dataPbtn['accionID'])){
							if($dataPbtn['accionID'] == '2'){ 
								$ventasfacturacion = "ventasfacturacion";
								$sqltomysql = "sqltomysql";
								$volumetricos = "volumetricos";
								$neoreportes = "neoreportes";
								$importarfactura = "importarfactura";
								$importarError = "codigoerror";
							}	
							
						}
					}
				}		
			}	
		}
						
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'index', 'timeout', 'iframe', 'ventasfacturacion', 'sqltomysql', 'volumetricos'],
                'rules' => [
                    [
                        'actions' => ['logout', 'index', 'iframe', 'timeout', 'pageone', 'migracion', 'descargaapis', 'showdata', 'descargapaises', $ventasfacturacion, $sqltomysql, $volumetricos, $neoreportes, $importarfactura, $importarError, 'ifactura', 'exportfiles', 'geterrormg', 'reset', 'getestablecimiento', 'report', 'recupera'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
					[
                        'actions' => ['changepass'],
                        'allow' => true,
                        'roles' => ['*'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }
	
	public function actionRecupera(){
		
		$this->layout = '/recupera';
		
		if(isset($_POST['send'])){
			$correo = $_POST['correo'];
			$user = $_POST['user'];
			
			$qryUser = "Select * from Usuarios where correoUsuario='".$correo."' and usuario='".$user."' and activoUsuario=1";
			$userData = Yii::$app->db->createCommand($qryUser)->queryOne();
			
			if(isset($userData['usuarioID'])){
				$codigoRecuperacionPassw = '36'.rand(9,10577).'s7';
				Yii::$app->db->createCommand("UPDATE Usuarios set codigoRecuperacionPassw='".$codigoRecuperacionPassw."' where usuarioID='".$userData['usuarioID']."'")->query();
				
				Yii::$app->globals->setBitacora("Solicitud restablecimiento contraseña", "Se envia correo para actualizar la contraseña", 1, "", $user, 9);
				
				$this->Sendmail($userData['correoUsuario'], $userData['nombreUsuario'], $codigoRecuperacionPassw, $userData['usuarioID']);
				return $this->redirect(['site/recupera',  'datos' => 'true']);
			}else{
				Yii::$app->globals->setBitacora("Solicitud restablecimiento contraseña", "Solicitud invalida para cambiar contraseña ".$correo, 0, "", $user, 9);
				return $this->redirect(['site/recupera',  'datos' => 'false']);
			}
		}
		
        return $this->render('recupera');
	}
	
	public function actionChangepass($token, $folio){
		
		$this->layout = '/pass';
		
		$qryUser = "Select * from Usuarios where MD5(codigoRecuperacionPassw)='".$_GET['token']."' and MD5(usuarioID)='".$_GET['folio']."'";
		$userData = Yii::$app->db->createCommand($qryUser)->queryOne();
		
		//print_r($userData);
		
		if(isset($userData['usuarioID'])){
			$guardaDato = true;
			if(isset($_POST['send'])){
				$pass = $_POST['passwd'];
				$userID = $userData['usuarioID'];
				$passHash = password_hash($pass, PASSWORD_DEFAULT);				
				
				$qryReglas = "Select * from ReglasPassw where regEstado=1 limit 1";
				$permisosPass = Yii::$app->db->createCommand($qryReglas)->queryOne();

				if($permisosPass['cantidadPassRepetidos'] > 0){
					$qryCantidad = "Select * from HistoricosPassword where usuarioID='".$userID."' order by historicosPasswordID DESC";
					$permisosCantidad = Yii::$app->db->createCommand($qryCantidad)->queryAll();

					$total = $permisosPass['cantidadPassRepetidos'];
					$recorrido = 1;
					//$guardaDato = true;
					foreach($permisosCantidad as $row){
						if($recorrido <= $total){
							if(password_verify($pass, $row['passwordHistorico'])) {
								$guardaDato = false;
							} 
						}else{
							Yii::$app->db->createCommand("delete from HistoricosPassword where historicosPasswordID='".$row['historicosPasswordID']."'")->query();
						}
						$recorrido++;
					}

				}
				
				if($guardaDato == true){
					
					$codigoRecuperacionPassw = '36'.rand(9,10577).'s7';
				
					$update = Yii::$app->db->createCommand("Update Usuarios set codigoRecuperacionPassw='".$codigoRecuperacionPassw."', passw='".$passHash."', cambioPass=0, fechaActualizaPass=NOW() where usuarioID='".$userID."'")->query();

					if($update){
						$btID = Yii::$app->globals->setBitacora("Actualizacion de contraseña", "Se actualizo la contraseña", 0, "", $userID, 8);
						
						Yii::$app->db->createCommand('INSERT INTO RegistroAuditoriaAplicaciones(accionID, tablaModificada, fechaBitacora, formularioID, usuarioID, establecimientoID, versionAplicacionID, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion, bitacoraID) VALUES ("5", "Usuarios", NOW(), "1", "'.$userID.'", "1", "1", "1", 1, NOW(), "'.$userID.'", "1",  "1", "'.$btID.'")')->query();
						$id = Yii::$app->db->getLastInsertID();
						
						Yii::$app->db->createCommand('INSERT INTO RegistroAuditoriaAplicacionesDetalle(registroAuditoriaAplicacionID, campo, valor) VALUES ("'.$id.'", "usuarioID", "'.$userID.'")')->query();
						
						
						Yii::$app->db->createCommand("INSERT INTO HistoricosPassword(usuarioID, passwordHistorico, fechaRegistro, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion)VALUES ('".$userID."', '".$passHash."', NOW(), 1, 1, NOW(), 1, 1, 1);")->query();
						
						//Yii::$app->globals->setBitacora("Cambio de contraseña", "Se actualiza la contraseña correctamente", 0, "", $userID);
						
						return $this->redirect(['site/login', 'pass' => 'true']);
					}else{
						return $this->redirect(['site/changepass', 'token'=>$token, 'folio'=>$folio, 'update' => 'false']);
					}
				}else{
					return $this->redirect(['site/changepass', 'token'=>$token, 'folio'=>$folio, 'pass' => 'false']);
				}
			}
		}else{
			return $this->redirect(['site/login',  'hash' => 'false']);
		}		
		
        return $this->render('passwd');
	}
	
	
	
	public function actionTestmail(){
        
		$mailer = Yii::$app->mailer;              
		$m = $mailer->compose();
		$subject = "Bienvenido";
		//$url =  Url::toRoute('site/changepass&token='.md5($model->codigoRecuperacionPassw).'&folio='.md5($model->usuarioID), true);
		$url =  Url::toRoute('site/changepass&token='.md5(1).'&folio='.md5(2), true);
		$body = '<html>
				<head>
				<meta charset="utf-8">
				<title>Brentec</title>
				<style>
					body{
						font-family: Helvetica, " sans-serif"; 
					}
					.btn_registro{
						padding: 10px 40px 10px 40px; 
						border-radius:30px; 
						background: transparent; 
						border:2px solid #000; 
						color: #000; 
						text-decoration: none;
						font-weight: bold;
					}

					.btn_registro:hover, .btn_registro:active{
						padding: 10px 40px 10px 40px; 
						border-radius:30px; 
						background: #000; 
						border:2px solid #000; 
						color: #FFF; 
						text-decoration: none;
						font-weight: bold;
					}
				</style>
				</head>
				<body>
				<div style="text-align: center; width: 100%; height: 55px; padding-top: 10px; padding-bottom: 5px;">
					<img src="http://ws.brentec.mx/sites/correo/logo_brentec.png" width="190px" alt="Logo" />
				</div>
				<img src="http://ws.brentec.mx/sites/correo/shadow-basica.png" width="100%"  alt="Logo" />
				<br><br>
				<div style="text-align: center;">
					<span style="font-size:22px; color: #2c2d2d;"> Bienvenido a la familia Brentec</span>
					<br><br>	
					Le enviamos un coordial saludo Nombre usuario, para poder finalizar su registro requerimos de la actualización de sus datos, presione el siguiente enlace para ser redirigido al sitio de Brentec.
					<br><br>
					<br><br>
					<a href="'.$url.'" target="_blank" class="btn_registro" rel="noopener noreferrer">Actualizar datos de registro</a>
					<br><br>
					Gracias !!
					<br><br>
					<br><br><br><br><br>
					<span style="color: #BBBBBB; font-size: 12px;">Mexico &copy; '.date('Y').'  Brentec. Todos los derechos reservados.</span>
				</div>

				<script type="text/javascript">	
						var d = new Date();
						 document.getElementById("date_year").innerHTML = d.getFullYear();
				</script>
				</body>
				</html>';
		
		$m->setTo('sam07_86@hotmail.com')
			->setFrom('notificaciones@ms.brentec.mx')
			->setSubject($subject)
			->setTextBody($subject)
			->setHtmlBody($body)
			->send();
		exit;
    }
	
	
	public function actionReport(){
        return $this->render('report');
    }
	
	
	
	//reset apiscripts
	public function actionReset(){
        return $this->render('reset');
    }
	
	//get establecimientos
	public function actionGetestablecimiento()
    {
		if(isset($_GET['id'])){
			$consulta = "SELECT establecimientoID,aliasEstablecimiento FROM Establecimientos where regEstado=1 and grupoID=".$_GET['id'];

			$sql = Yii::$app->db->createCommand($consulta)->queryAll();		
			$select = '<option value="0"> -- Selecciona --</option>';

			foreach($sql as $row){
				$select .= '<option value="'.$row['establecimientoID'].'">'.$row['aliasEstablecimiento'].'</option>';
			}
		}else{
			$select = '<option value="0"> -- Selecciona --</option>';
		}		
		
		echo $select;
	}
	
	
	public function actionGeterrormg($api){
       if(isset(Yii::$app->session['logMigracion'][$api])){
			if(isset(Yii::$app->session['logMigracion'][$api]['mensajeCorto'])){
				$mensaje = "";
				
				$mensaje .= "Error de ejecucion : ".$api."<br><br>";
				if(Yii::$app->session['logMigracion'][$api]['mensajeCorto'] != ""){
					$mensaje .=	Yii::$app->session['logMigracion'][$api]['mensajeCorto']."<br>";
				}
				
				if(Yii::$app->session['logMigracion'][$api]['mensaje2'] != ""){
					$mensaje .=	Yii::$app->session['logMigracion'][$api]['mensaje2']."<br>";	
				}
				$mensaje .= "<br>";
				if(Yii::$app->session['logMigracion'][$api]['error'] != ""){
					//$mensaje .=	Yii::$app->session['logMigracion'][$api]['error']."<br>";	
				}
				
				print_r($mensaje);
			}else{
				 echo "No encontramos el mensaje del error, ejecuta de nuevo el api";
			}
		}else{
		   echo "No encontramos el mensaje del error, ejecuta de nuevo el api";
	   }
    }
	//copiar archivos
	public function actionExportfiles(){
        return $this->render('exportfiles');
    }
	
	public function actionCampos(){
		
		//$command = Yii::$app->db->createCommand('Select claveAccion as clave, estadoAccion, accionID from accionesformularios')->queryAll();
		//$command = Yii::$app->db->schema->getTable('Select claveAccion as clave, estadoAccion, accionID from accionesformularios')->columns;
		//array_keys($command)
		//print_r($command);
		 $sql = 'Select count(accionFormularioID) as totalData, claveAccion as clave, estadoAccion, accionID from accionesformularios';
		 $command = Yii::$app->db->createCommand($sql);

		 //$command->bindParam(PDO::FETCH_ASSOC);

		$rows = $command->queryAll();
		foreach($rows as $data){
			$nameColum = array_keys($data);
			$saveName = "";
			foreach($nameColum as $naC){
				$saveName .= $naC.",";
			}
			echo $saveName;
			exit;
		}
		//print_r($rows);

	}
	//show decarga paises
	public function actionDescargapaises(){
        return $this->render('descargapaises');
    }
	
	
	//show data
	public function actionShowdata(){
        return $this->render('showdata');
    }
	
	//codigo de errores
	public function actionCodigoerror(){
        return $this->render('codigoerror');
    }
	
	
	
	//descarga apis
	public function actionDescargaapis(){
        return $this->render('descargaapis');
    }
	
	//migracion 
	public function actionMigracion(){
        return $this->render('migracion');
    }
	
	
	//ventasfacturacion
	public function actionVentasfacturacion(){
        return $this->render('ventasfacturacion');
    }
	
	//sqltomysql
	public function actionSqltomysql(){
        return $this->render('sqltomysql');
    }
	
	
	//volumetricos
	public function actionVolumetricos(){
        return $this->render('volumetricos');
    }
	
	//reportes facturas diarias y mensuales
	public function actionNeoreportes(){
        return $this->render('neoreportes');
    }
	
	//reportes notas
	public function actionIfactura(){
        return $this->render('ifactura');
    }
	
	//reportes notas
	public function actionImportarfactura(){
        return $this->render('importarfactura');
    }
	
	
	
    /**
     * {@inheritdoc}
     */
	public function actionTimeout(){
		
		if(isset(Yii::$app->session['tokenID'])){
			$userN = "";
			$userData = Yii::$app->db->createCommand("SELECT * FROM Usuarios where usuarioID='".Yii::$app->session['tokenID']."'")->queryOne();
			if(isset($userData['usuario'])){
				$userN = $userData['usuario'];
			}
			
			$btID = Yii::$app->globals->setBitacora("Acceso de usuario", "Sesion finalizada inactividad del usuario ".$userN, 0, "", Yii::$app->session['tokenID'],11);
			Yii::$app->globals->setEvento(1, 16, 0, 1, $btID, 'Sesion finalizada inactividad del usuario'.$userN);
			Yii::$app->session->remove('tokenID');
		}else{
			$btID = Yii::$app->globals->setBitacora("Acceso de usuario", "Sesion finalizada por inactividad de la sesion", 0, "", 0, 11);
			Yii::$app->globals->setEvento(1, 16, 0, 1, $btID, 'Sesion finalizada por inactividad de la sesion');
		}
		
		Yii::$app->user->logout();
		return $this->render('site/login');
		//return $this->goHome();
		
	}
	
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

	 public function actionIframe()
    {
        return $this->render('iframe');
    }
	
    /**
     * Displays homepage.
     *
     * @return string
     */
	//pagina uno de prueba 
	 public function actionPageone()
    {
        return $this->render('pageone');
    }
	
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
		$this->layout = '/login';
				
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {			
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
		$userN = "";
		if(isset(Yii::$app->session['tokenID'])){
			
			$userData = Yii::$app->db->createCommand("SELECT * FROM Usuarios where usuarioID='".Yii::$app->session['tokenID']."'")->queryOne();
			if(isset($userData['usuario'])){
				$userN = $userData['usuario'];
			}
		}
		$btID = Yii::$app->globals->setBitacora("Acceso de usuario", "El usuario ".$userN." cerro la sesion ", 0, "", Yii::$app->user->identity->usuarioID,10);
        Yii::$app->user->logout();		
        return $this->goHome();
    }
	
	public function actionExit()
    {
		$userN = "";
		if(isset(Yii::$app->session['tokenID'])){
			
			$userData = Yii::$app->db->createCommand("SELECT * FROM Usuarios where usuarioID='".Yii::$app->session['tokenID']."'")->queryOne();
			if(isset($userData['usuario'])){
				$userN = $userData['usuario'];
			}
		}
		
		$btID = Yii::$app->globals->setBitacora("Acceso de usuario", "El usuario ".$userN." cerro la sesion ", 0, "", Yii::$app->user->identity->usuarioID,10);
		Yii::$app->user->logout();
      	return $this->render('site/login');
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
	
	function Sendmail($correo, $nombre, $codigo, $id){
        
		$mailer = Yii::$app->mailer;              
		$m = $mailer->compose();
		$subject = "Solicitud cambio de contraseña";
		//$url =  Url::toRoute('site/changepass&token='.md5($model->codigoRecuperacionPassw).'&folio='.md5($model->usuarioID), true);
		$url =  Url::toRoute('site/changepass&token='.md5($codigo).'&folio='.md5($id), true);
		$body = '<html>
				<head>
				<meta charset="utf-8">
				<title>Brentec</title>
				<style>
					body{
						font-family: Helvetica, " sans-serif"; 
					}
					.btn_registro{
						padding: 10px 40px 10px 40px; 
						border-radius:30px; 
						background: transparent; 
						border:2px solid #000; 
						color: #000; 
						text-decoration: none;
						font-weight: bold;
					}

					.btn_registro:hover, .btn_registro:active{
						padding: 10px 40px 10px 40px; 
						border-radius:30px; 
						background: #000; 
						border:2px solid #000; 
						color: #FFF; 
						text-decoration: none;
						font-weight: bold;
					}
				</style>
				</head>
				<body>
				<div style="text-align: center; width: 100%; height: 55px; padding-top: 10px; padding-bottom: 5px;">
					<img src="http://ws.brentec.mx/sites/correo/logo_brentec.png" width="190px" alt="Logo" />
				</div>
				<img src="http://ws.brentec.mx/sites/correo/shadow-basica.png" width="100%"  alt="Logo" />
				<br><br>
				<div style="text-align: center;">
					<span style="font-size:22px; color: #2c2d2d;">Le enviamos un cordial saludo '.$nombre.' </span>
					<br><br>	
					Para poder finalizar la actualización de su contraseña, presione el siguiente enlace para ser redirigido al sitio de Brentec.
					<br><br>
					<br><br>
					<a href="'.$url.'" target="_blank" class="btn_registro" rel="noopener noreferrer">Actualizar mi contraseña</a>
					<br><br>
					¡Gracias!
					<br><br>
					<br><br><br><br><br>
					<span style="color: #BBBBBB; font-size: 12px;">Mexico &copy; '.date('Y').'  Brentec. Todos los derechos reservados.</span>
				</div>

				<script type="text/javascript">	
						var d = new Date();
						 document.getElementById("date_year").innerHTML = d.getFullYear();
				</script>
				</body>
				</html>';
		
		$m->setTo($correo)
			->setFrom('notificaciones@ms.brentec.mx')
			->setSubject($subject)
			->setTextBody($subject)
			->setHtmlBody($body)
			->send();
		
		return  true;
    }
}
