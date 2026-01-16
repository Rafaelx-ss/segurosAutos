<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\models\Usuarios;
use app\models\UsuariosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use sam0786\fpdf\FPDFCarta;



/**
 * UsuariosController implements the CRUD actions for Usuarios model.
 */
class UsuariosController extends Controller
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
		
		$perCons = '';
		$perAlta = '';
		$perElim = '';
		$perEdit = '';
		$perExcel = '';
		$perPdf = '';
		$perdel= '';
		$perPass= '';
		
		$frmSeguridad = 'perfiles';
		
		$permisosBtn = Yii::$app->db->createCommand("SELECT Acciones.accionID, Acciones.nombreAccion, Formularios.nombreFormulario, Formularios.tipoFormularioID, Formularios.urlArchivo FROM Acciones
			inner join AccionesFormularios on Acciones.accionID=AccionesFormularios.accionID 
			inner join Formularios on AccionesFormularios.formularioID=Formularios.formularioID
			inner join PerfilAccionFormulario on AccionesFormularios.accionFormularioID =PerfilAccionFormulario.accionFormularioID
			inner join PerfilesCompuestos on PerfilAccionFormulario.perfilID=PerfilesCompuestos.perfilID
			inner join Usuarios on Usuarios.usuarioID = PerfilesCompuestos.usuarioID
			where Formularios.urlArchivo='perfiles/index' and AccionesFormularios.estadoAccion=1 and AccionesFormularios.regEstado=1 and PerfilAccionFormulario.regEstado=1 and PerfilAccionFormulario.activoPerfilAccionFormulario=1 and Usuarios.usuarioID='".$usuarioIdToken."' group by AccionesFormularios.accionFormularioID")->queryAll();
			
			foreach($permisosBtn as $dataPbtn){
				$urlSeguridad = explode("/", $dataPbtn['urlArchivo']);
				if(isset($urlSeguridad[0])){
					if($frmSeguridad == $urlSeguridad[0]){
						if(isset($dataPbtn['accionID'])){
							if($dataPbtn['accionID'] == '1'){ $perPass = 'passwd'; }	
							if($dataPbtn['accionID'] == '2'){ $perCons = 'index'; }	
							if($dataPbtn['accionID'] == '3'){ $perAlta = 'create'; }	
							if($dataPbtn['accionID'] == '4'){ $perElim = 'deletedata'; $perdel = 'delete'; }
							if($dataPbtn['accionID'] == '5'){ $perEdit = 'update'; }
							if($dataPbtn['accionID'] == '6'){ $perExcel = 'xportexcel'; }
							if($dataPbtn['accionID'] == '7'){ $perPdf = 'xportpdf'; }
						}
					}
				}
			}
		
        return [
			 'access' => [
                'class' => AccessControl::className(),
                'only' => ['create', 'update', 'delete', 'index', 'passwd', 'carta', 'cartaresponsiva', 'inputsave'],
                'rules' => [
                    [
                        'actions' => [$perAlta, $perEdit, $perdel, $perCons, $perElim, $perPass, 'carta', 'cartaresponsiva', 'inputsave'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className()
            ],
        ];
    }

    /**
     * Lists all Usuarios models.
     * @return mixed
     */
	 public function actionInputsave($modulo, $msj, $tipoID)
    {
		 //Yii::$app->user->identity->usuarioID
		 //setBitacora($encabezado, $detalle, $correo, $docto, $user, $idMsg=0)
		 //setEvento($usuario, $tipoID, $componenteID, $formulario, $btID=0, $observ){
        $btID = Yii::$app->globals->setBitacora("Cambio en registros", $msj, 0, "", Yii::$app->user->identity->usuarioID, 1);
		echo  Yii::$app->globals->setEvento(Yii::$app->user->identity->usuarioID, $tipoID, 0, 1, $btID, $msj);
		 
    }
	
	 public function actionCartaresponsiva($id)
    {
        $model = $this->findModel($id);
		
		return $this->render('cartaresponsiva', [
            'model' => $model,
        ]);
    }
	
	public function actionCarta($id){
		ini_set('memory_limit', '-1');   
		ini_set('max_execution_time', '0');  
		set_time_limit(0);
		Yii::$app->response->headers->add('Content-Type', 'application/pdf');
		
		$qryCarta = "Select * from CartasResponsivas where cartaResposivaID='1'";
		$cartaResp = Yii::$app->db->createCommand($qryCarta)->queryOne();
		
		$userData = Yii::$app->db->createCommand('SELECT * FROM Usuarios where usuarioID="'.$id.'"')->queryOne();
		
			$c_11 = 229;
			$c_21 = 229;
			$c_31 = 229;
			
			
			$c_1 = 229;
			$c_2 = 229;
			$c_3 = 229;
			
			
			// Creación del objeto de la clase heredada
			
			//aqui orientacion de la pagina
			
			
				$orientacion = 'P';
				$posFecha = 122;
				$posPagerq = 195;
				$posFooterq = 211;
				$posContenido = 195;
				$posEncabezado = 200;
		
			
			
			$pdf = new FPDFCarta($orientacion, 'mm', 'Letter');
			$pdf->cl_1 = $c_1;
			$pdf->cl_2 = $c_2;
			$pdf->cl_3 = $c_3;
			#Establecemos los márgenes izquierda, arriba y derecha: 
			$pdf->SetMargins(10, 10 , 10); 
			#Establecemos el margen inferior: 
			$pdf->SetAutoPageBreak(true,25);
			$pdf->SetDrawColor($c_1, $c_2, $c_3);	
					
			$pdf->posicionFecha = $posFecha;
			$pdf->posPager = $posPagerq;
			$pdf->posFooter = $posFooterq;
			$pdf->n_sitio = "Nombre del sitio";
			$pdf->sitioPager = $cartaResp['lugarSistema'];
		
			$pdf->AliasNbPages();
			$pdf->AddPage();
		
			$pdf->SetFont('Arial','',14);
			$pdf->Ln(5);
			$pdf->Cell($posEncabezado,6, "Carta Responsiva del sistema ".$cartaResp['nombreSistema'], 0,0,'C');
			$pdf->Ln(15);
		
			$pdf->SetFont('Arial','',10);
			$pdf->MultiCell($posEncabezado,6, utf8_decode("Yo ".$userData['nombreUsuario'].", ".$cartaResp['textoEncabezado']), 0, 'L',0);
			$pdf->Ln(8);
		
			$qryEstablecimiento = "SELECT * FROM Establecimientos where activoEstablecimiento=1 and regEstado=1";
			$establecimiento = Yii::$app->db->createCommand($qryEstablecimiento)->queryAll();
				
			$est = array();
			if(isset($_GET['estab'])){
				$est = $_GET['estab'];
			}else{
				$pdf->Cell(40,6, utf8_decode("Por favor selecciona un establecimiento de la lista"),0,0,'L');
					$pdf->Ln(6);
			}
		
			
		
			foreach($establecimiento as $row){
				if(in_array($row['establecimientoID'], $est)) {
					//print_r($row['establecimientoID'].' - '.$row['razonSocialEstablecimiento'].' -RFC:'.$row['rfcEstablecimiento']);
					$pdf->Cell(40,6, utf8_decode($row['establecimientoID'].' - '.$row['razonSocialEstablecimiento'].' - RFC:'.$row['rfcEstablecimiento']),0,0,'L');
					$pdf->Ln(6);
				}
			}
			$pdf->Ln(6);
			$pdf->Cell(40,6, utf8_decode('Servicio informático : '),0,0,'L');
			
			$urlData = str_replace('?r=site/index','',Url::to(['site/index'], true));
			$pdf->Cell(100,6, utf8_decode($urlData),0,0,'L');
			$pdf->Ln(6);
			$pdf->Cell(40,6, utf8_decode('Usuario : '),0,0,'L');
			$pdf->Cell(100,6, utf8_decode($userData['usuario']),0,0,'L');
			
			$pdf->Ln(10);
			$pdf->MultiCell($posEncabezado,6, utf8_decode($cartaResp['textoPerfiles']), 0, 'L',0);
		
			$arrayNames = array('Perfil', 'Formulario', 'Acción');
			$miCabecera = array();
			$espaciosEle = $posContenido/count($arrayNames);
			foreach($arrayNames as $rName){					
				$dEspEl[] = $espaciosEle;
			}	
			
			$pdf->Ln(10);	
			$pdf->SetTextColor(0, 0, 0);
			$pdf->cabeceraHorizontal($arrayNames, $posContenido);
			$pdf->Ln(7);
			$pdf->SetFont('Arial','',8);
			$pdf->SetWidths($dEspEl);
		
			$strAcciones = "SELECT nombrePerfil, nombreFormulario, nombreAccion FROM PerfilesCompuestos
inner join Perfiles on Perfiles.perfilID = PerfilesCompuestos.perfilID
inner join PermisosFormulariosPerfiles on PermisosFormulariosPerfiles.perfilID = PerfilesCompuestos.perfilID
inner join Formularios on Formularios.formularioID=PermisosFormulariosPerfiles.formularioID
inner join AccionesFormularios on AccionesFormularios.formularioID=Formularios.formularioID
inner join Acciones on Acciones.accionID = AccionesFormularios.accionID
where PerfilesCompuestos.usuarioID='".$id."' and PerfilesCompuestos.activoPermiso=1 and PerfilesCompuestos.regEstado=1 
and PermisosFormulariosPerfiles.activoPermiso=1 and  PermisosFormulariosPerfiles.regEstado=1 
and AccionesFormularios.estadoAccion=1 and AccionesFormularios.regEstado=1 
and Acciones.estadoAccion = 1 and Acciones.regEstado=1 order by nombrePerfil, nombreFormulario, nombreAccion";
		$acciones = Yii::$app->db->createCommand($strAcciones)->queryAll();
		
		
		
		foreach($acciones as $rAccion){
			$pdf->Row(array($rAccion['nombrePerfil'], $rAccion['nombreFormulario'], $rAccion['nombreAccion']));
			//$arLinea[] =$rAccion['nombrePerfil'];	
		}
		
		/*
			$arLinea = array();
			foreach($arrayNames as $rname){
				$arLinea[] = $rowData[$rname['campo']];				
			}
			
			*/
		
			$pdf->AliasNbPages();
			$pdf->AddPage();
		
			$pdf->SetFont('Arial','',14);
			$pdf->Ln(5);
			$pdf->Cell($posEncabezado,6, "Carta Responsiva del sistema ".$cartaResp['nombreSistema'], 0,0,'C');
			$pdf->Ln(15);
		
			$pdf->SetFont('Arial','',10);
				
			$pdf->SetFont('Arial','',10);
			$pdf->MultiCell($posEncabezado,6, utf8_decode($cartaResp['textoFirma']), 0, 'L',0);
			$pdf->Ln(10);
		
			
			$pdf->Cell($posEncabezado,6, "Firma de conformidad", 0,0,'L');
			$pdf->Ln(15);
			$pdf->SetDrawColor(0, 0, 0);
			$pdf->Cell(150,6, "", 0,0,'L');
			$pdf->Cell(30,20, "", 'T,L,R',0,'C');
		
			$pdf->Ln(20);
			$pdf->Cell(90,6, "", 'B',0,'L');
			$pdf->Cell(60,6, "", 0,0,'L');
			$pdf->Cell(30,6, "", 'B, L,R',0,'C');
		
			$pdf->Ln(6);
			$pdf->Cell(90,6, "Nombre y Firma", 0,0,'C');
			$pdf->Cell(60,6, "", 0,0,'L');
			$pdf->Cell(30,6, "Huella", 0,0,'C');
			
			$pdf->Output();	
			exit;
		
	}
	
	
	
	
    public function actionIndex()
    {
        $searchModel = new UsuariosSearch();
      

		if(isset($_GET['clear'])){
			Yii::$app->session['UsuariosSearch'] = '';
			return $this->redirect(['index']);			
		}else{
			$params = Yii::$app->request->queryParams;

			if(count($params) <= 1){		  
				if(isset(Yii::$app->session['UsuariosSearch'])) {
					$params = Yii::$app->session['UsuariosSearch'];
				}else{
					Yii::$app->session['UsuariosSearch'] = $params;
				}
			}else{	
				if(isset(Yii::$app->request->queryParams['UsuariosSearch'])){
					Yii::$app->session['UsuariosSearch'] = $params;
				}else{
					$params = Yii::$app->session['UsuariosSearch'];
				}		
			}
		}
		
		$dataProvider = $searchModel->search($params);
		$dataProvider->pagination->pageSize = Yii::$app->params['npag'];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Usuarios model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Usuarios model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Usuarios();
		
		if($model->load(Yii::$app->request->post())) {
			$fID = Yii::$app->globals->getFormularioToken('perfiles/index');
			$post = Yii::$app->request->post();
			$model->regEstado = 1;
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = $fID;
			$model->AuthKey = '36'.rand(9,10577).'s7';
			$model->codigoRecuperacionPassw = '36'.rand(9,10577).'s7';
			$model->fechaGeneracionCodigoRecuperacionPassw = date('Y-m-d H:i:s');
			$model->passw = password_hash($post['Usuarios']['passw'], PASSWORD_DEFAULT);
			//$model->passw = password_hash($model->AuthKey, PASSWORD_DEFAULT);
			$model->cambioPass = 1;
			$model->fechaActualizaPass = date('Y-m.d');
			
			if($model->save()){
				
				$rauditoria = Yii::$app->globals->setRegistro(3, 'Alta', 'Usuarios', $fID);

				Yii::$app->globals->setRmodifica($rauditoria, 'usuarioID', $model->usuarioID);
				Yii::$app->globals->setRmodifica($rauditoria, 'nombreUsuario', $model->nombreUsuario);
				Yii::$app->globals->setRmodifica($rauditoria, 'usuario', $model->usuario);
				Yii::$app->globals->setRmodifica($rauditoria, 'activoUsuario', $model->activoUsuario);
				Yii::$app->globals->setRmodifica($rauditoria, 'correoUsuario', $model->correoUsuario);
				Yii::$app->globals->setRmodifica($rauditoria, 'intentosValidos', $model->intentosValidos);
				
				
				Yii::$app->db->createCommand("INSERT INTO HistoricosPassword(usuarioID, passwordHistorico, fechaRegistro, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion)VALUES ('".$model->usuarioID."', '".$model->passw."', NOW(), 1, 1, NOW(), 1, 1, 1);")->query();
				
				
				Yii::$app->globals->setBitacora("Alta usuario", "Usuario ".$model->usuario." agregado con exito", 0, "", Yii::$app->user->identity->usuarioID, '23');
				
				if($model->correoUsuario != ""){
					if(filter_var($model->correoUsuario, FILTER_VALIDATE_EMAIL)) {
						$this->Sendmail($model->correoUsuario, $model->nombreUsuario, $model->codigoRecuperacionPassw, $model->usuarioID);
					}
				}
				
				//$this->Sendmail($model->correoUsuario, $model->nombreUsuario, $model->codigoRecuperacionPassw, $model->usuarioID);
				
				return $this->redirect(['update', 'insert' => 'true', 'id' => $model->usuarioID]);
			}
		}
		

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Usuarios model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $version = $model->versionRegistro;		
		if($model->load(Yii::$app->request->post())) {
			$fID = Yii::$app->globals->getFormularioToken('perfiles/index');
			$post = Yii::$app->request->post();
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = $version + 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = $fID;
			
			
			if($model->save()){
				
				$rauditoria = Yii::$app->globals->setRegistro(5, 'Editar', 'Usuarios', $fID);

				Yii::$app->globals->setRmodifica($rauditoria, 'usuarioID', $model->usuarioID);
				Yii::$app->globals->setRmodifica($rauditoria, 'nombreUsuario', $model->nombreUsuario);
				Yii::$app->globals->setRmodifica($rauditoria, 'usuario', $model->usuario);
				Yii::$app->globals->setRmodifica($rauditoria, 'activoUsuario', $model->activoUsuario);
				Yii::$app->globals->setRmodifica($rauditoria, 'correoUsuario', $model->correoUsuario);
				Yii::$app->globals->setRmodifica($rauditoria, 'intentosValidos', $model->intentosValidos);
									
				
				return $this->redirect(['update', 'id' => $model->usuarioID, 'update'=>'true']);
			}
		}

        return $this->render('update', [
            'model' => $model,
        ]);
    }
	
	
	//actualizar contraseña
	public function actionPasswd($id)
    {
        $model = $this->findModel($id);
        $version = $model->versionRegistro;
				
		if($model->load(Yii::$app->request->post())) {
			$fID = Yii::$app->globals->getFormularioToken('perfiles/index');
			$post = Yii::$app->request->post();
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = $version + 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = $fID;
			$model->passw = password_hash($post['Usuarios']['passw'], PASSWORD_DEFAULT);
			$model->fechaActualizaPass = date('Y-m-d');
			$model->cambioPass = 1;
			$model->codigoRecuperacionPassw = '36'.rand(9,10577).'s7';
			
			
			$qryReglas = "Select * from ReglasPassw where regEstado=1 limit 1";
			$permisosPass = Yii::$app->db->createCommand($qryReglas)->queryOne();
			
			if($permisosPass['cantidadPassRepetidos'] > 0){
				$qryCantidad = "Select * from HistoricosPassword where usuarioID='".$id."' order by historicosPasswordID DESC";
				$permisosCantidad = Yii::$app->db->createCommand($qryCantidad)->queryAll();
				
				$total = $permisosPass['cantidadPassRepetidos'];
				$recorrido = 1;
				$guardaDato = true;
				foreach($permisosCantidad as $row){
					if($recorrido <= $total){
						if(password_verify($post['Usuarios']['passw'], $row['passwordHistorico'])) {
							$guardaDato = false;
						} 
					}else{
						Yii::$app->db->createCommand("delete from HistoricosPassword where historicosPasswordID='".$row['historicosPasswordID']."'")->query();
					}
					$recorrido++;
				}
				
			}
			
			if($guardaDato == true){
				if($model->save()){
				
					$rauditoria = Yii::$app->globals->setRegistro(5, 'Editar', 'Usuarios', $fID);

					Yii::$app->globals->setRmodifica($rauditoria, 'usuarioID', $model->usuarioID);
					Yii::$app->db->createCommand("INSERT INTO HistoricosPassword(usuarioID, passwordHistorico, fechaRegistro, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion)VALUES ('".$id."', '".$model->passw."', NOW(), 1, 1, NOW(), 1, 1, 1);")->query();
					
					if(isset($_POST['mailCheck'])){
						if($model->correoUsuario != ''){
							if(filter_var($model->correoUsuario, FILTER_VALIDATE_EMAIL)) {
								$this->Sendmail($model->correoUsuario, $model->nombreUsuario, $model->codigoRecuperacionPassw, $model->usuarioID);
							}
							
						}						
					}

					Yii::$app->globals->setBitacora("Edicion usuario", "Contraseña actualizada del usuario".$model->usuario, 1, "", Yii::$app->user->identity->usuarioID, 24);
					
					return $this->redirect(['passwd', 'id' => $model->usuarioID, 'update'=>'true']);
				}
			}else{
				return $this->redirect(['passwd', 'id' => $model->usuarioID, 'pass'=>'false']);
			}
			
		}

        return $this->render('passwd', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Usuarios model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
   
				
	public function actionDelete(){
				
				
		$true = 0;	
		if ($selection=(array)Yii::$app->request->post('selection')) {
			$fID = Yii::$app->globals->getFormularioToken('perfiles/index');
			
			foreach($selection as $id){
				$model = $this->findModel($id);					
				$model->load(Yii::$app->request->post());	        
				$model->activoUsuario = 1;	
				
				 if($model->save()){
					 
					$rauditoria = Yii::$app->globals->setRegistro(5, 'Editar', 'Usuarios', $fID);
					Yii::$app->globals->setRmodifica($rauditoria, 'usuarioID', $model->usuarioID);
					 
					 $true++;
				 }
			}
		}
		 
        if($true != 0){
            return true;
        }else{
			return false;
		}
    }
	
	function Sendmail($correo, $nombre, $codigo, $id){
        
		$mailer = Yii::$app->mailer;              
		$m = $mailer->compose();
		$subject = "Actualiza tus datos";
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
					<span style="font-size:22px; color: #2c2d2d;"> Le enviamos un cordial saludo '.$nombre.'</span>
					<br><br>	
					Para actualizar su contraseña, presione el siguiente enlace para ser redirigido al sitio de Brentec.
					<br><br>
					<br><br>
					<a href="'.$url.'" target="_blank" class="btn_registro" rel="noopener noreferrer">Actualizar datos</a>
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

    /**
     * Finds the Usuarios model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Usuarios the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Usuarios::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
