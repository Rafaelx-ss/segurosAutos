<?php
ini_set('memory_limit', '-1');   
ini_set('max_execution_time', '0');  
set_time_limit(0);

header('Content-Type: text/html; charset=iso-8859-1');
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\assets\AppAsset;

use yii\helpers\Url;
$asset = AppAsset::register($this);
$baseUrl = $asset->baseUrl;


use kartik\datetime\DateTimePicker;

$idForm = '';
if(isset($_GET['f'])){
	$idForm = $_GET['f'];
}

$frmSeguridad = '';
if(isset($_GET['r'])){$frmSeguridad = explode("/", $_GET['r']);}


$permisosBtn = Yii::$app->db->createCommand("SELECT Acciones.accionID, Acciones.nombreAccion, Formularios.nombreFormulario, Formularios.icono,  Formularios.textoID, Formularios.tipoFormularioID, Formularios.urlArchivo, Acciones.imagen FROM Acciones
inner join AccionesFormularios on Acciones.accionID=AccionesFormularios.accionID 
inner join Formularios on AccionesFormularios.formularioID=Formularios.formularioID
inner join PerfilAccionFormulario on AccionesFormularios.accionFormularioID =PerfilAccionFormulario.accionFormularioID
inner join PerfilesCompuestos on PerfilAccionFormulario.perfilID=PerfilesCompuestos.perfilID
inner join Usuarios on Usuarios.usuarioID = PerfilesCompuestos.usuarioID
where md5(Formularios.formularioID)='".$idForm."' and AccionesFormularios.estadoAccion=1 and AccionesFormularios.regEstado=1 and PerfilAccionFormulario.regEstado=1 and PerfilAccionFormulario.activoPerfilAccionFormulario=1 and Usuarios.usuarioID='".Yii::$app->user->identity->usuarioID."' group by AccionesFormularios.accionFormularioID")->queryAll();


$perCons = 0;
$perAlta = 0;
$perElim = 0;
$perEdit = false;
$perElimckh = false;
$perExcel = 0;
$perPdf = 0;
$txtID = 1;
$tituloReporte = "Reporte";
$iconForm = 'pe-7s-lock';

$iAdd = "fa fa-plus";
$iSeacrh = "fa fa-search";
$iDelete = "fa fa-trash";
$iExcel = "fa fa-file-excel";
$iPdf = "fa fa-file-pdf";

foreach($permisosBtn as $dataPbtn){
	$urlSeguridad = explode("/", $dataPbtn['urlArchivo']);	
	if(isset($urlSeguridad[0]) and isset($frmSeguridad[0])){		
		if($frmSeguridad[0] == $urlSeguridad[0]){			
			if(isset($dataPbtn['accionID'])){	
					if($dataPbtn['accionID'] == '2'){ $perCons = 1; $iSeacrh = $dataPbtn['imagen'];}	
					if($dataPbtn['accionID'] == '3'){ $perAlta = 1; $iAdd = $dataPbtn['imagen'];}	
					if($dataPbtn['accionID'] == '4'){ $perElim = 1; $perElimckh = true; $iDelete = $dataPbtn['imagen'];}
					if($dataPbtn['accionID'] == '5'){ $perEdit = true; }
					if($dataPbtn['accionID'] == '6'){ $perExcel = 1; $iExcel = $dataPbtn['imagen'];}
					if($dataPbtn['accionID'] == '7'){ $perPdf = 1; $iPdf = $dataPbtn['imagen'];}
					$txtID = $dataPbtn['textoID'];
					$iconForm = $dataPbtn['icono'];
					$tituloReporte = $dataPbtn['nombreFormulario'];
				}
			}
	}						
}

$qryReporte = "";
$qryCount = "";
$arrayCampos = array();
$arrayfCampos = array();
$arrayPost = array();
$cadQry = "";

$errorPermisos = "false";

$formMenu = Yii::$app->db->createCommand("SELECT * FROM Formularios where md5(formularioID)='".$idForm."'")->queryOne();
?>

<div class="app-page-title">
	<div class="page-title-wrapper">
    	<div class="page-title-heading">
        	<div class="page-title-icon">
            	<i class="<?=  $iconForm ?> icon-gradient bg-malibu-beach"></i>
            </div>
            <div>
				<?=  Yii::$app->globals->getTraductor($<Z, Yii::$app->session['idiomaId'], 'Migracion'); ?>				
				<div class="page-title-subheading"><?= Yii::$app->globals->getTraductor(18, Yii::$app->session['idiomaId'], $formMenu['nombreFormulario']) ?></div>
            </div>
        </div>
        <div class="page-title-actions">
			
			<?php 	
					
			
								
			if($formMenu['tipoMenu'] == 'Submenu'){
			
			echo '<div style="margin-top: -30px; float: right;">';
			$formSubmenus = Yii::$app->db->createCommand("SELECT * FROM Formularios where formID='".$formMenu['formID']."'")->queryAll();
			foreach($formSubmenus as $rsubMenu){
				echo Html::a('<i class="'.$rsubMenu['icono'].'"></i> '.Yii::$app->globals->getTraductor($rsubMenu['textoID'], Yii::$app->session['idiomaId'], $rsubMenu['nombreFormulario']), $url = [$rsubMenu['urlArchivo'].'&f='.md5($rsubMenu['formularioID'])], $options = ['style'=>'border-top-left-radius: 0; border-top-right-radius: 0;', 'class'=>'btn-shadow btn '.Yii::$app->globals->btnMenu().' mr-3 active']);
			}	
			
			echo '</div>';
			echo '<div style="clear: both;"></div>';
			
			
			}
			?>			
		</div>
     </div>
</div>

<div class="main-card mb-3 card">
	<div class="card-body">	
		<div style="clear: both;"></div>
		<div style="text-align: right;">
			<?php
			$form = ActiveForm::begin([
				'id' => 'login-form',
				'action' => ['site/sqltomysql&f='.$_GET['f']],
				'options' => ['method' => 'post', 'class' => 'form-inline float-sm-right', 'onsubmit' => 'cargando()'],				
			]);
				
			$vfi = "";
			$vff = "";
			$vSi = 0;
			if(isset($_POST['FechaInicial']) and isset($_POST['FechaFinal'])  and isset($_POST['Siic'])){
				$vfi = 'value="'.$_POST['FechaInicial'].'"';
				$vff = 'value="'.$_POST['FechaFinal'].'"';
				$vSi = $_POST['Siic'];
			}
			
			?>
    		<div class="form-group" style="margin-right: 20px;">
				<label>Fecha Inicial:</label> &nbsp;&nbsp;&nbsp;
				<input name="FechaInicial" required class="form-control input-sm" type="date" <?php echo $vfi; ?> >				
			</div>
			<div class="form-group" style="margin-right: 20px;">
				<label>Fecha Final:</label> &nbsp;&nbsp;&nbsp;
				<input name="FechaFinal" required class="form-control input-sm" type="date" <?php echo $vff; ?>>				
			</div>
			<?php
			$apiSel = "all";
			$s1 = "";
			$s2 = "";
			$s3 = "";
			$s4 = "";
			$s5 = "";
			$s6 = "";
			$s7 = "";
			$s8 = "";
			if(isset($_POST['apisel'])){
				$apiSel = $_POST['apisel'];
				if($apiSel == 'RecepcionesCargas'){
					$s1 = "selected";
				}else if($apiSel == 'DocumentosCargas'){
					$s2 = "selected";
				}else if($apiSel == 'LecturasTanques'){
					$s3 = "selected";
				}else if($apiSel == 'Jornadas'){
					$s4 = "selected";
				}else if($apiSel == 'Asistencias'){
					$s5 = "selected";
				}else if($apiSel == 'Despachos'){
					$s6 = "selected";
				}else if($apiSel == 'Lecturas'){
					$s7 = "selected";
				}else if($apiSel == 'Volumen'){
					$s8 = "selected";
				}
			}
			?>
			<div class="form-group" style="margin-right: 20px;">
				<label>Api :</label> &nbsp;&nbsp;&nbsp;
				<select name="apisel" required class="form-control input-sm">  	
					<option value="all"> Todas </option>
					<option value="RecepcionesCargas" <?php echo $s1; ?>> RecepcionesCargas </option>
					<option value="DocumentosCargas" <?php echo $s2; ?>> DocumentosCargas </option>
					<option value="LecturasTanques" <?php echo $s3; ?>> LecturasTanques </option>
					<option value="Jornadas" <?php echo $s4; ?>> Jornadas </option>
					<option value="Asistencias" <?php echo $s5; ?>> Asistencias </option>
					<option value="Despachos" <?php echo $s6; ?>> Despachos </option>
					<option value="Lecturas" <?php echo $s7; ?>> Lecturas </option>
					<option value="Volumen" <?php echo $s8; ?>> Volumen </option>
				</select>
			</div>
			
			<div class="form-group" style="margin-right: 20px;">
				<label>Establecimiento:</label> &nbsp;&nbsp;&nbsp;
				<select name="Siic" required class="form-control input-sm">
  					<option value=""> -- Selecciona --</option>
					<?php
					$establecimientos =  Yii::$app->db->createCommand('SELECT establecimientoID,aliasEstablecimiento FROM Establecimientos where regEstado=1')->queryAll();
					foreach($establecimientos as $restab){
						if($vSi == $restab['establecimientoID']){
							echo '<option value="'.$restab['establecimientoID'].'" selected >'.$restab['aliasEstablecimiento'].'</option>';
						}else{
							echo '<option value="'.$restab['establecimientoID'].'">'.$restab['aliasEstablecimiento'].'</option>';
						}
						
					}
					?>
				</select>
			</div>
			
			<br>
			<div class="form-group" style="margin-top: 10px; margin-left: 5px;">
					<button type="submit" class="btn btn-success " id="btn_export">Exportar Datos</button>
			</div>
			<?php ActiveForm::end() ?>
		</div>
		<div style="clear: both;"></div>
		
		<?php
		
		//inicia el formulario
	
		
		
		
		$apiBase = Yii::$app->basePath;
		
		$FechaInicio="";
		$FechaFin="";
		$establecimientoID="";
		$Username ="";
		$Password ="";
		$RutaApiSql = "";
		$RutaApiMysql = "";
			
		$UsernameMysql ="";
		$PasswordMysql ="";
		
		echo "<hr>";
			
		echo '<div id="cargando" style="display:none;  color: green; font-size:12px; text-align: center;">
				<img src="'.$baseUrl.'/require/images/cloud-upload.gif" alt="cargando"  style="width: 380px;" /> 
				<input class="timepage" size="5" id="timespent" name="timespent" readonly style="text-align:center;width:200px;font-size:40px;border:1px solid #56aaf3;padding:6px;margin:12px 0 12px 0;">
				<h4>No cierre ni refresque la pagina hasta finalizar el proceso</h4><br>
			</div>';
		echo "<div id='contenidoInfo'>";
		if(isset($_POST['FechaInicial']) and isset($_POST['FechaFinal'])  and isset($_POST['Siic'])){
			
			$FechaInicioWhile = $_POST['FechaInicial'];
			$FechaFinWhile = $_POST['FechaFinal'];
			$establecimientoID = $_POST['Siic'];
			
			$clApp1 = Yii::$app->db->createCommand("SELECT  ConexionesApis.rutaApi, ConexionesApis.usuario, ConexionesApis.password FROM ConexionesApis
			 	inner join ConexionesApisEstablecimientos on ConexionesApisEstablecimientos.conexionApiID = ConexionesApis.conexionApiID
				inner join AplicacionesConexionApi on AplicacionesConexionApi.aplicacionConexionApiID= ConexionesApis.aplicacionConexionApiID
				where descripcion='ConVolSQLServer' and establecimientoID = '".$_POST['Siic']."'")->queryOne();
			
			$clApp2 = Yii::$app->db->createCommand("SELECT  ConexionesApis.rutaApi, ConexionesApis.usuario, ConexionesApis.password FROM ConexionesApis
			 	inner join ConexionesApisEstablecimientos on ConexionesApisEstablecimientos.conexionApiID = ConexionesApis.conexionApiID
				inner join AplicacionesConexionApi on AplicacionesConexionApi.aplicacionConexionApiID= ConexionesApis.aplicacionConexionApiID
				where descripcion='BonoboLocal' and establecimientoID = '".$_POST['Siic']."'")->queryOne();
			
			
			require_once($apiBase."/clientemigracion/config/llaves.php");
			
			if(isset($clApp1['usuario'])){
				$Username = $clApp1['usuario'];
				openssl_private_decrypt(base64_decode($clApp1['password']), $PasswordGetdec, $llaveprivada);
				$Password = $PasswordGetdec;
				$RutaApiSql = $clApp1['rutaApi'];
			}
			
			if(isset($clApp2['usuario'])){
				$UsernameMysql = $clApp2['usuario'];
				openssl_private_decrypt(base64_decode($clApp2['password']), $PasswordPOSTdec, $llaveprivada);
				$PasswordMysql = $PasswordPOSTdec;
				$RutaApiMysql = $clApp2['rutaApi'];
			}
			
			
			
			$dataMacAddress = 'd4:61:9d:01:85:18';
			
				
			
			
			$fechaaamostar = $FechaInicioWhile;
			if($_POST['FechaInicial'] <= $_POST['FechaFinal']){				
			require_once($apiBase."/clientemigracion/libs/ApiHttpClient.php");
			
				$tiempoTotalInicial = date('Y-m-d H:i:s');
				
				$mensaje = "";
				$errorData = 0;
				
				//inicia conexion api 1
				ApiHttpClient::Init($RutaApiSql);
				ApiHttpClient::$UserName = $Username;
				ApiHttpClient::$Password = $Password;
				ApiHttpClient::$MacAddress =  $dataMacAddress;
				
				//echo "<pre>";
				//print_r(array('UserName'=>$Username,'Password'=>$Password, 'MacAddress'=>$dataMacAddress));
				
				echo "Ruta ConVolSQLServer:".$RutaApiSql."/api/ApiTokenSQLServer.php <br>";
				echo "usuario ApiTokenSQLServer : ".$Username."<br>";
				if($Password == ''){
					echo "pass ApiTokenSQLServer : Error en la contraseña<br>";
				}
				
				$TokenApiSql = "";
				$Token  = ApiHttpClient::SolicitaToken('/api/ApiTokenSQLServer.php');
				
				//echo "aqui esta el error";
				//echo ApiHttpClient::$Resultado;
				try{
					if(ApiHttpClient::$Resultado){
						$TokenApiSql = ApiHttpClient::$Token;
					}else{
						
						$mensaje .= "Token SQL: Ocurrio un error 2 ".ApiHttpClient::$Mensaje." ----> ";
						$errorData++;
					}
				}catch (Exception $e){
					$mensaje .= "Token SQL: ".$this->mensaje = $e->getMessage()." ----> ";
					$errorData++;
				}
				//finaliza conexion api 1
				
				//inicia conexion api 2
				ApiHttpClient::Init($RutaApiMysql);
				ApiHttpClient::$UserName = $UsernameMysql;
				ApiHttpClient::$Password = $PasswordMysql;
				ApiHttpClient::$MacAddress =  $dataMacAddress;
				
				echo "Ruta BonoboLocal Envio: ".$RutaApiMysql."/api/ApiToken.php<br>";
				echo "usuario ApiToken : ".$UsernameMysql."<br>";
				if($PasswordMysql == ''){
					echo "pass ApiToken : Error en la contraseña<br>";
				}
				
				$TokenApiMysql = "";
				$Token  = ApiHttpClient::SolicitaToken('/api/ApiToken.php');
				
				try{
					if(ApiHttpClient::$Resultado){
						$TokenApiMysql = ApiHttpClient::$Token;
					}else{
							//echo ApiHttpClient::$Resultado;
						$mensaje .= "Token MySql: Ocurrio un error ".ApiHttpClient::$Mensaje." ----> ";
						$errorData++;
					}
				}catch (Exception $e){
					$mensaje .= "Token MySql: ".$this->mensaje = $e->getMessage()." ----> ";
					$errorData++;
				}
				//finaliza conexion api 2
				
				if($TokenApiSql != "" and $TokenApiMysql != "" ){
					while(strtotime($FechaFinWhile) >= strtotime($FechaInicio)) { 
						//echo $fechaaamostar."<br />"; 
						
						$FechaInicio = date("Ymd 00:00:00", strtotime($fechaaamostar));
						$FechaFin = date("Ymd 23:59:59.997", strtotime($fechaaamostar));		

						$FechaInicioMysql = date("Y-m-d 00:00:00", strtotime($fechaaamostar));
						$FechaFinMysql = date("Y-m-d 23:59:59.997", strtotime($fechaaamostar));

						//inicia la primera peticion RecepcionesCargas
						if($apiSel=='all' or $apiSel=='RecepcionesCargas'){
							$errorData = "";
							$mensaje = "";
							$mensajePost = "";
							$cabecera = "";
							$tiempoEjecuta = "";
							$tiempoEjecutaPost = "";
							$tiempoEjecutaInicio = date('Y-m-d H:i:s');
							$total_datos = 0;	
							$errorDataPost = "";
							
							$cabecera .= "<strong>Recepciones de Cargas del ".$FechaInicioMysql." al ".$FechaFinMysql." </strong><br>";
							$tiempoEjecuta .= "Fecha inicio : ".$tiempoEjecutaInicio;
							
							ApiHttpClient::Init($RutaApiSql);
							ApiHttpClient::$Token = $TokenApiSql;
							ApiHttpClient::$UserName = $Username;
							ApiHttpClient::$Password = $Password;
							ApiHttpClient::$MacAddress =  $dataMacAddress;

							$parametros = json_encode(Array(
										"Token" => $TokenApiSql,
										"FechaInicio" =>  $FechaInicio,
										"FechaFin" => $FechaFin,
										"MacAddress" => $dataMacAddress
									)); 

							$datos = ApiHttpClient::ConsumeApi('/api/ApiRecepcionesCargasSQLServer.php','GET',$parametros);
							$arraydatos = json_decode($datos,true);
							$tiempoEjecutafinal = date('Y-m-d H:i:s');
							
							$tiempoEjecutaInicioPost = date('Y-m-d H:i:s');
							$tiempoEjecutaPost .= "Fecha inicio : ".$tiempoEjecutaInicioPost;
							
							
							$mDesarrollo = "";
							if(isset($arraydatos['desarrollo'])){
									$mDesarrollo .= "<br>".$arraydatos['desarrollo'];
							}
							
							if(ApiHttpClient::$EstatusHttp == 200){
								if(isset($arraydatos['resultado'])){
									if($arraydatos['datos']){
										$total_datos = count($arraydatos['datos']);
									}
									$errorData = 'alert-success';
									if($total_datos == 0){
										$mensaje .= "Sin datos en la consulta";
										$mensajePost .=  "No hay datos en la petición";
									}else{
										$mensaje .= "Datos obtenidos exito";
										//$mensaje .= " ----> ";
										$errorData = 'alert-success';
										ApiHttpClient::Init($RutaApiMysql);
										ApiHttpClient::$Token = $TokenApiMysql;
										ApiHttpClient::$UserName = $UsernameMysql;
										ApiHttpClient::$Password = $PasswordMysql;
										ApiHttpClient::$MacAddress =  $dataMacAddress;


										$parametrosMysql = json_encode(Array(
																"Token" => $TokenApiMysql,
																"datosCliente" => $arraydatos['datos']
														   )); 

										//print_r($parametrosMysql);

										$apiFactResp = ApiHttpClient::ConsumeApi('/api/ApiRecepcionesCargas.php','POST',$parametrosMysql);
										$arrayApiFac = json_decode($apiFactResp,true);

										$mDesarrollo1 = "";
										if(isset($arrayApiFac['desarrollo'])){
											$mDesarrollo1 .= "<br>".$arrayApiFac['desarrollo'];
										}
							
										
										if(ApiHttpClient::$EstatusHttp == 200){												
											if(isset($arrayApiFac['mensaje'])){
												$mensajePost .=  "Datos importados a la nube : ".$arrayApiFac['mensaje'];
												//$errorData++;
												$errorDataPost = 'alert-success';
											}else{
												$mensajePost .=  "Mensaje : ".$apiFactResp.$mDesarrollo1;
												$errorDataPost = 'alert-warning';
											}
										}else{
											$mensajePost .=  "Ocurrio un error al importar los datos (".$apiFactResp.")".$mDesarrollo1;
											$errorDataPost = 'alert-danger';
										}
									}
									
								}else{
									$mensaje .= "No encontramos datos en esta fecha".$mDesarrollo;
									$errorData = 'alert-warning';
								}
								
								$tiempoEjecutafinalPost = date('Y-m-d H:i:s');
									
								$date11 = new DateTime($tiempoEjecutaInicioPost);
								$date21 = new DateTime($tiempoEjecutafinalPost);
								$diff1 = $date11->diff($date21);	
								$tiempoEjecutaPost .= " | Fecha final : ".$tiempoEjecutafinalPost;
							}else{
								$mensaje .= "Error al consultar los datos (".$datos.")".$mDesarrollo;
								$errorData = 'alert-danger';
								
								$tiempoEjecutafinalPost = date('Y-m-d H:i:s');
								$date11 = new DateTime($tiempoEjecutaInicioPost);
								$date21 = new DateTime($tiempoEjecutafinalPost);
								$diff1 = $date11->diff($date21);
							}


							$tiempoEjecuta .= " | Fecha final : ".$tiempoEjecutafinal;

							$date1 = new DateTime($tiempoEjecutaInicio);
							$date2 = new DateTime($tiempoEjecutafinal);
							$diff = $date1->diff($date2);	
							
							
							
							//print_r($diff);

							
							echo '<div class="alert alert-light" role="alert" style="border: 1px solid #dfdfdf;">
											'.$cabecera.'
										<div class="alert '.$errorData.'" role="alert">
											<span style="font-size:18px;">Consulta de datos</span><br>
											'.$tiempoEjecuta.' | total de tiempo transcurrido : '.$diff->h.' horas y '.$diff->i.' minutos y '.$diff->s.' segundos<br>
											Total de datos : '.$total_datos.'<br>
											'.$mensaje.'
										</div>
										
										<div class="alert '.$errorDataPost.'" role="alert">
											<span style="font-size:18px;">Envio de datos</span><br>
											'.$tiempoEjecutaPost.' | total de tiempo transcurrido : '.$diff1->h.' horas y '.$diff1->i.' minutos y '.$diff1->s.' segundos<br>
											Total de datos : '.$total_datos.'<br>
											'.$mensajePost.'
										</div>
								 </div>';
							
						}
						//finaliza la primera peticion RecepcionesCargas
						
						
						//inicia la primera peticion DocumentosCargas
						if($apiSel=='all' or $apiSel=='DocumentosCargas'){
							$errorData = "";
							$mensaje = "";
							$mensajePost = "";
							$cabecera = "";
							$tiempoEjecuta = "";
							$tiempoEjecutaPost = "";
							$tiempoEjecutaInicio = date('Y-m-d H:i:s');
							$total_datos = 0;	
							$errorDataPost = "";
							
							$cabecera .= "<strong>Documentos Cargas del ".$FechaInicioMysql." al ".$FechaFinMysql."</strong><br>";
							$tiempoEjecuta .= "Fecha inicio : ".$tiempoEjecutaInicio;
							
							ApiHttpClient::Init($RutaApiSql);
							ApiHttpClient::$Token = $TokenApiSql;
							ApiHttpClient::$UserName = $Username;
							ApiHttpClient::$Password = $Password;
							ApiHttpClient::$MacAddress =  $dataMacAddress;
							$parametros = json_encode(Array(
										"Token" => $TokenApiSql,
										"FechaInicio" =>  $FechaInicio,
										"FechaFin" => $FechaFin,
										"MacAddress" => $dataMacAddress
									)); 

							$datos = ApiHttpClient::ConsumeApi('/api/ApiDocumentosCargasSQLServer.php','GET',$parametros);
							$arraydatos = json_decode($datos,true);
							
							$tiempoEjecutafinal = date('Y-m-d H:i:s');
							
							$tiempoEjecutaInicioPost = date('Y-m-d H:i:s');
							$tiempoEjecutaPost .= "Fecha inicio : ".$tiempoEjecutaInicioPost;
							
										$mDesarrollo2 = "";
										if(isset($arraydatos['desarrollo'])){
											$mDesarrollo2 .= "<br>".$arraydatos['desarrollo'];
										}
							
							if(ApiHttpClient::$EstatusHttp == 200){
								if(isset($arraydatos['resultado'])){
									if($arraydatos['datos']){
										$total_datos = count($arraydatos['datos']);
									}
									$errorData = 'alert-success';
									if($total_datos == 0){
										$mensaje .= "Sin datos en la consulta";
										$mensajePost .=  "No hay datos en la petición";
									}else{
										
										$mensaje .= "Datos obtenidos exito";
										//$mensaje .= " ----> ";
										$errorData = 'alert-success';

										ApiHttpClient::Init($RutaApiMysql);
										ApiHttpClient::$Token = $TokenApiMysql;
										ApiHttpClient::$UserName = $UsernameMysql;
										ApiHttpClient::$Password = $PasswordMysql;
										ApiHttpClient::$MacAddress =  $dataMacAddress;
										$parametrosMysql = json_encode(Array(
																"Token" => $TokenApiMysql,
																"datosCliente" => $arraydatos['datos']
														   )); 



										$apiFactResp = ApiHttpClient::ConsumeApi('/api/ApiDocumentosCargas.php','POST',$parametrosMysql);
										$arrayApiFac = json_decode($apiFactResp,true);

										$mDesarrollo3 = "";
										if(isset($arrayApiFac['desarrollo'])){
											$mDesarrollo3 .= "<br>".$arrayApiFac['desarrollo'];
										}
										
										if(ApiHttpClient::$EstatusHttp == 200){												
											if(isset($arrayApiFac['mensaje'])){
												$mensajePost .=  "Datos importados a la nube : ".$arrayApiFac['mensaje'];
												//$errorData++;
												$errorDataPost = 'alert-success';
											}else{
												$mensajePost .=  "Mensaje : ".$apiFactResp.$mDesarrollo3;
												$errorDataPost = 'alert-warning';
											}
										}else{
											$mensajePost .=  "Ocurrio un error al importar los datos (".$apiFactResp.")".$mDesarrollo3;
											$errorDataPost = 'alert-danger';
										}
									}
									
									
								}else{
									$mensaje .= "No encontramos datos en esta fecha".$mDesarrollo2;
									$errorData = 'alert-warning';
								}
								
								$tiempoEjecutafinalPost = date('Y-m-d H:i:s');
									
								$date11 = new DateTime($tiempoEjecutaInicioPost);
								$date21 = new DateTime($tiempoEjecutafinalPost);
								$diff1 = $date11->diff($date21);	
								$tiempoEjecutaPost .= " | Fecha final : ".$tiempoEjecutafinalPost;
							}else{
								$mensaje .= "Error al consultar los datos (".$datos.")".$mDesarrollo2;
								$errorData = 'alert-danger';
								
								$tiempoEjecutafinalPost = date('Y-m-d H:i:s');
								$date11 = new DateTime($tiempoEjecutaInicioPost);
								$date21 = new DateTime($tiempoEjecutafinalPost);
								$diff1 = $date11->diff($date21);
							}


							$tiempoEjecuta .= " | Fecha final : ".$tiempoEjecutafinal;

							$date1 = new DateTime($tiempoEjecutaInicio);
							$date2 = new DateTime($tiempoEjecutafinal);
							$diff = $date1->diff($date2);	
							
							
							
							//print_r($diff);

							
							echo '<div class="alert alert-light" role="alert" style="border: 1px solid #dfdfdf;">
											'.$cabecera.'
										<div class="alert '.$errorData.'" role="alert">
											<span style="font-size:18px;">Consulta de datos</span><br>
											'.$tiempoEjecuta.' | total de tiempo transcurrido : '.$diff->h.' horas y '.$diff->i.' minutos y '.$diff->s.' segundos<br>
											Total de datos : '.$total_datos.'<br>
											'.$mensaje.'
										</div>
										
										<div class="alert '.$errorDataPost.'" role="alert">
											<span style="font-size:18px;">Envio de datos</span><br>
											'.$tiempoEjecutaPost.' | total de tiempo transcurrido : '.$diff1->h.' horas y '.$diff1->i.' minutos y '.$diff1->s.' segundos<br>
											Total de datos : '.$total_datos.'<br>
											'.$mensajePost.'
										</div>
								 </div>';
						}
						//finaliza la primera peticion DocumentosCargas
						
						
						//inicia la primera peticion LecturasTanques
						if($apiSel=='all' or $apiSel=='LecturasTanques'){
							$errorData = "";
							$mensaje = "";
							$mensajePost = "";
							$cabecera = "";
							$tiempoEjecuta = "";
							$tiempoEjecutaPost = "";
							$tiempoEjecutaInicio = date('Y-m-d H:i:s');
							$total_datos = 0;	
							$errorDataPost = "";
							
							$cabecera .= "<strong>LecturasTanques-ExistenciasTanques del ".$FechaInicioMysql." al ".$FechaFinMysql."</strong><br>";
							$tiempoEjecuta .= "Fecha inicio : ".$tiempoEjecutaInicio;
							
							ApiHttpClient::Init($RutaApiSql);
							ApiHttpClient::$UserName = $Username;
							ApiHttpClient::$Password = $Password;
							ApiHttpClient::$MacAddress =  $dataMacAddress;
							ApiHttpClient::$Token = $TokenApiSql;
							$parametros = json_encode(Array(
										"Token" => $TokenApiSql,
										"FechaInicio" =>  $FechaInicio,
										"FechaFin" => $FechaFin,
										"MacAddress" => $dataMacAddress
									)); 

							$datos = ApiHttpClient::ConsumeApi('/api/ApiLecturasTanques.php','GET',$parametros);
							$arraydatos = json_decode($datos,true);
							
							$tiempoEjecutafinal = date('Y-m-d H:i:s');
							
							$tiempoEjecutaInicioPost = date('Y-m-d H:i:s');
							$tiempoEjecutaPost .= "Fecha inicio : ".$tiempoEjecutaInicioPost;
							
							$mDesarrollo4 = "";
										if(isset($arraydatos['desarrollo'])){
											$mDesarrollo4 .= "<br>".$arraydatos['desarrollo'];
										}
							
							if(ApiHttpClient::$EstatusHttp == 200){
								if(isset($arraydatos['resultado'])){
									
									if($arraydatos['datos']){
										$total_datos = count($arraydatos['datos']);
									}
									$errorData = 'alert-success';
									if($total_datos == 0){
										$mensaje .= "Sin datos en la consulta";
										$mensajePost .=  "No hay datos en la petición";
									}else{
										
										$mensaje .= "Datos obtenidos exito";
										//$mensaje .= " ----> ";
										$errorData = 'alert-success';

										ApiHttpClient::Init($RutaApiMysql);
										ApiHttpClient::$Token = $TokenApiMysql;
										ApiHttpClient::$UserName = $UsernameMysql;
										ApiHttpClient::$Password = $PasswordMysql;
										ApiHttpClient::$MacAddress =  $dataMacAddress;
										$parametrosMysql = json_encode(Array(
																"Token" => $TokenApiMysql,
																"datosCliente" => $arraydatos['datos']
														   )); 

										if($arraydatos['datos']){
											$total_datos = count($arraydatos['datos']);
										}

										$apiFactResp = ApiHttpClient::ConsumeApi('/api/ApiExistenciasTanques.php','POST',$parametrosMysql);
										$arrayApiFac = json_decode($apiFactResp,true);

										$mDesarrollo5 = "";
										if(isset($arrayApiFac['desarrollo'])){
											$mDesarrollo5 .= "<br>".$arrayApiFac['desarrollo'];
										}
										
										if(ApiHttpClient::$EstatusHttp == 200){												
											if(isset($arrayApiFac['mensaje'])){
												$mensajePost .=  "Datos importados a la nube : ".$arrayApiFac['mensaje'];
												//$errorData++;
												$errorDataPost = 'alert-success';
											}else{
												$mensajePost .=  "Mensaje : ".$apiFactResp.$mDesarrollo5;
												$errorDataPost = 'alert-warning';
											}
										}else{
											$mensajePost .=  "Ocurrio un error al importar los datos (".$apiFactResp.")".$mDesarrollo5;
											$errorDataPost = 'alert-danger';
										}
									}									
									
								}else{
									$mensaje .= "No encontramos datos en esta fecha".$mDesarrollo4;
									$errorData = 'alert-warning';
								}
								
								$tiempoEjecutafinalPost = date('Y-m-d H:i:s');
									
								$date11 = new DateTime($tiempoEjecutaInicioPost);
								$date21 = new DateTime($tiempoEjecutafinalPost);
								$diff1 = $date11->diff($date21);	
								$tiempoEjecutaPost .= " | Fecha final : ".$tiempoEjecutafinalPost;
							}else{
								$mensaje .= "Error al consultar los datos (".$datos.")".$mDesarrollo4;
								$errorData = 'alert-danger';
								
								$tiempoEjecutafinalPost = date('Y-m-d H:i:s');
								$date11 = new DateTime($tiempoEjecutaInicioPost);
								$date21 = new DateTime($tiempoEjecutafinalPost);
								$diff1 = $date11->diff($date21);
							}


							$tiempoEjecuta .= " | Fecha final : ".$tiempoEjecutafinal;

							$date1 = new DateTime($tiempoEjecutaInicio);
							$date2 = new DateTime($tiempoEjecutafinal);
							$diff = $date1->diff($date2);	
							
							
							
							//print_r($diff);

							
							echo '<div class="alert alert-light" role="alert" style="border: 1px solid #dfdfdf;">
											'.$cabecera.'
										<div class="alert '.$errorData.'" role="alert">
											<span style="font-size:18px;">Consulta de datos</span><br>
											'.$tiempoEjecuta.' | total de tiempo transcurrido : '.$diff->h.' horas y '.$diff->i.' minutos y '.$diff->s.' segundos<br>
											Total de datos : '.$total_datos.'<br>
											'.$mensaje.'
										</div>
										
										<div class="alert '.$errorDataPost.'" role="alert">
											<span style="font-size:18px;">Envio de datos</span><br>
											'.$tiempoEjecutaPost.' | total de tiempo transcurrido : '.$diff1->h.' horas y '.$diff1->i.' minutos y '.$diff1->s.' segundos<br>
											Total de datos : '.$total_datos.'<br>
											'.$mensajePost.'
										</div>
								 </div>';
						}
						//finaliza la primera peticion LecturasTanques
						
						//inicia la primera peticion Jornadas
						if($apiSel=='all' or $apiSel=='Jornadas'){
							$errorData = "";
							$mensaje = "";
							$mensajePost = "";
							$cabecera = "";
							$tiempoEjecuta = "";
							$tiempoEjecutaPost = "";
							$tiempoEjecutaInicio = date('Y-m-d H:i:s');
							$total_datos = 0;	
							$errorDataPost = "";
							
							$cabecera .= "<strong>Jornadas del ".$FechaInicioMysql." al ".$FechaFinMysql."</strong><br>";
							$tiempoEjecuta .= "Fecha inicio : ".$tiempoEjecutaInicio;
							
							
							ApiHttpClient::Init($RutaApiSql);
							ApiHttpClient::$UserName = $Username;
							ApiHttpClient::$Password = $Password;
							ApiHttpClient::$MacAddress =  $dataMacAddress;
							ApiHttpClient::$Token = $TokenApiSql;
							$parametros = json_encode(Array(
										"Token" => $TokenApiSql,
										"FechaInicio" =>  $FechaInicio,
										"FechaFin" => $FechaFin,
										"MacAddress" => $dataMacAddress
									)); 

							$datos = ApiHttpClient::ConsumeApi('/api/ApiJornadasSQLServer.php','GET',$parametros);
							$arraydatos = json_decode($datos,true);
							
							$tiempoEjecutafinal = date('Y-m-d H:i:s');
							
							$tiempoEjecutaInicioPost = date('Y-m-d H:i:s');
							$tiempoEjecutaPost .= "Fecha inicio : ".$tiempoEjecutaInicioPost;
							
							$mDesarrollo6 = "";
										if(isset($arraydatos['desarrollo'])){
											$mDesarrollo6 .= "<br>".$arraydatos['desarrollo'];
										}
							
							if(ApiHttpClient::$EstatusHttp == 200){
								if(isset($arraydatos['resultado'])){
									if($arraydatos['datos']){
										$total_datos = count($arraydatos['datos']);
									}
									$errorData = 'alert-success';$errorData = 'alert-success';
									if($total_datos == 0){
										$mensaje .= "Sin datos en la consulta";
										$mensajePost .=  "No hay datos en la petición";
									}else{
										
										$mensaje .= "Datos obtenidos exito";
										//$mensaje .= " ----> ";
										$errorData = 'alert-success';

										ApiHttpClient::Init($RutaApiMysql);
										ApiHttpClient::$Token = $TokenApiMysql;
										ApiHttpClient::$UserName = $UsernameMysql;
										ApiHttpClient::$Password = $PasswordMysql;
										ApiHttpClient::$MacAddress =  $dataMacAddress;
										$parametrosMysql = json_encode(Array(
																"Token" => $TokenApiMysql,
																"datosCliente" => $arraydatos['datos']
														   )); 

										if($arraydatos['datos']){
											$total_datos = count($arraydatos['datos']);
										}

										$apiFactResp = ApiHttpClient::ConsumeApi('/api/ApiJornadas.php','POST',$parametrosMysql);
										$arrayApiFac = json_decode($apiFactResp,true);

										$mDesarrollo7 = "";
										if(isset($arrayApiFac['desarrollo'])){
											$mDesarrollo7 .= "<br>".$arrayApiFac['desarrollo'];
										}
										
										
										if(ApiHttpClient::$EstatusHttp == 200){												
											if(isset($arrayApiFac['mensaje'])){
												$mensajePost .=  "Datos importados a la nube : ".$arrayApiFac['mensaje'];
												//$errorData++;
												$errorDataPost = 'alert-success';
											}else{
												$mensajePost .=  "Mensaje : ".$apiFactResp.$mDesarrollo7;
												$errorDataPost = 'alert-warning';
											}
										}else{
											$mensajePost .=  "Ocurrio un error al importar los datos (".$apiFactResp.")".$mDesarrollo7;
											$errorDataPost = 'alert-danger';
										}
									}									
									
								}else{
									$mensaje .= "No encontramos datos en esta fecha".$mDesarrollo6;
									$errorData = 'alert-warning';
								}
								
								$tiempoEjecutafinalPost = date('Y-m-d H:i:s');
									
								$date11 = new DateTime($tiempoEjecutaInicioPost);
								$date21 = new DateTime($tiempoEjecutafinalPost);
								$diff1 = $date11->diff($date21);	
								$tiempoEjecutaPost .= " | Fecha final : ".$tiempoEjecutafinalPost;
							}else{
								$mensaje .= "Error al consultar los datos (".$datos.")".$mDesarrollo6;
								$errorData = 'alert-danger';
								
								$tiempoEjecutafinalPost = date('Y-m-d H:i:s');
								$date11 = new DateTime($tiempoEjecutaInicioPost);
								$date21 = new DateTime($tiempoEjecutafinalPost);
								$diff1 = $date11->diff($date21);
							}


							$tiempoEjecuta .= " | Fecha final : ".$tiempoEjecutafinal;

							$date1 = new DateTime($tiempoEjecutaInicio);
							$date2 = new DateTime($tiempoEjecutafinal);
							$diff = $date1->diff($date2);	
							
							
							
							//print_r($diff);

							
							echo '<div class="alert alert-light" role="alert" style="border: 1px solid #dfdfdf;">
											'.$cabecera.'
										<div class="alert '.$errorData.'" role="alert">
											<span style="font-size:18px;">Consulta de datos</span><br>
											'.$tiempoEjecuta.' | total de tiempo transcurrido : '.$diff->h.' horas y '.$diff->i.' minutos y '.$diff->s.' segundos<br>
											Total de datos : '.$total_datos.'<br>
											'.$mensaje.'
										</div>
										
										<div class="alert '.$errorDataPost.'" role="alert">
											<span style="font-size:18px;">Envio de datos</span><br>
											'.$tiempoEjecutaPost.' | total de tiempo transcurrido : '.$diff1->h.' horas y '.$diff1->i.' minutos y '.$diff1->s.' segundos<br>
											Total de datos : '.$total_datos.'<br>
											'.$mensajePost.'
										</div>
								 </div>';
						}
						//finaliza la primera peticion Jornadas
						
						//inicia la primera peticion Asistencias
						if($apiSel=='all' or $apiSel=='Asistencias'){
							$errorData = "";
							$mensaje = "";
							$mensajePost = "";
							$cabecera = "";
							$tiempoEjecuta = "";
							$tiempoEjecutaPost = "";
							$tiempoEjecutaInicio = date('Y-m-d H:i:s');
							$total_datos = 0;	
							$errorDataPost = "";
							
							$cabecera .= "<strong>Asistencias del ".$FechaInicioMysql." al ".$FechaFinMysql."</strong><br>";
							$tiempoEjecuta .= "Fecha inicio : ".$tiempoEjecutaInicio;
							
							
							ApiHttpClient::Init($RutaApiSql);
							ApiHttpClient::$UserName = $Username;
							ApiHttpClient::$Password = $Password;
							ApiHttpClient::$MacAddress =  $dataMacAddress;
							ApiHttpClient::$Token = $TokenApiSql;

							$parametros = json_encode(Array(
										"Token" => $TokenApiSql,
										"FechaInicio" =>  $FechaInicio,
										"FechaFin" => $FechaFin,
										"MacAddress" => $dataMacAddress
									)); 

							$datos = ApiHttpClient::ConsumeApi('/api/ApiAsistenciasSQLServer.php','GET',$parametros);
							$arraydatos = json_decode($datos,true);
							
							$tiempoEjecutafinal = date('Y-m-d H:i:s');
							
							$tiempoEjecutaInicioPost = date('Y-m-d H:i:s');
							$tiempoEjecutaPost .= "Fecha inicio : ".$tiempoEjecutaInicioPost;
							
							$mDesarrollo8 = "";
										if(isset($arraydatos['desarrollo'])){
											$mDesarrollo8 .= "<br>".$arraydatos['desarrollo'];
										}
							
							if(ApiHttpClient::$EstatusHttp == 200){
								if(isset($arraydatos['resultado'])){
									if($arraydatos['datos']){
										$total_datos = count($arraydatos['datos']);
									}
									$errorData = 'alert-success';
									if($total_datos == 0){
										$mensaje .= "Sin datos en la consulta";
										$mensajePost .=  "No hay datos en la petición";
									}else{
										
										$mensaje .= "Datos obtenidos exito";
										//$mensaje .= " ----> ";
										$errorData = 'alert-success';

										ApiHttpClient::Init($RutaApiMysql);
										ApiHttpClient::$Token = $TokenApiMysql;
										ApiHttpClient::$UserName = $UsernameMysql;
										ApiHttpClient::$Password = $PasswordMysql;
										ApiHttpClient::$MacAddress =  $dataMacAddress;
										$parametrosMysql = json_encode(Array(
																"Token" => $TokenApiMysql,
																"datosCliente" => $arraydatos['datos']
														   )); 

										if($arraydatos['datos']){
											$total_datos = count($arraydatos['datos']);
										}

										$apiFactResp = ApiHttpClient::ConsumeApi('/api/ApiAsistencias.php','POST',$parametrosMysql);
										$arrayApiFac = json_decode($apiFactResp,true);

										$mDesarrollo9 = "";
										if(isset($arrayApiFac['desarrollo'])){
											$mDesarrollo9 .= "<br>".$arrayApiFac['desarrollo'];
										}
										
										if(ApiHttpClient::$EstatusHttp == 200){												
											if(isset($arrayApiFac['mensaje'])){
												$mensajePost .=  "Datos importados a la nube : ".$arrayApiFac['mensaje'];
												//$errorData++;
												$errorDataPost = 'alert-success';
											}else{
												$mensajePost .=  "Mensaje : ".$apiFactResp.$mDesarrollo9;
												$errorDataPost = 'alert-warning';
											}
										}else{
											$mensajePost .=  "Ocurrio un error al importar los datos (".$apiFactResp.")".$mDesarrollo9;
											$errorDataPost = 'alert-danger';
										}
									}
									
									
								}else{
									$mensaje .= "No encontramos datos en esta fecha".$mDesarrollo8;
									$errorData = 'alert-warning';
								}
								
								$tiempoEjecutafinalPost = date('Y-m-d H:i:s');
									
								$date11 = new DateTime($tiempoEjecutaInicioPost);
								$date21 = new DateTime($tiempoEjecutafinalPost);
								$diff1 = $date11->diff($date21);	
								$tiempoEjecutaPost .= " | Fecha final : ".$tiempoEjecutafinalPost;
							}else{
								$mensaje .= "Error al consultar los datos (".$datos.")".$mDesarrollo8;
								$errorData = 'alert-danger';
								
								$tiempoEjecutafinalPost = date('Y-m-d H:i:s');
								$date11 = new DateTime($tiempoEjecutaInicioPost);
								$date21 = new DateTime($tiempoEjecutafinalPost);
								$diff1 = $date11->diff($date21);
							}


							$tiempoEjecuta .= " | Fecha final : ".$tiempoEjecutafinal;

							$date1 = new DateTime($tiempoEjecutaInicio);
							$date2 = new DateTime($tiempoEjecutafinal);
							$diff = $date1->diff($date2);	
							
							
							
							//print_r($diff);

							
							echo '<div class="alert alert-light" role="alert" style="border: 1px solid #dfdfdf;">
											'.$cabecera.'
										<div class="alert '.$errorData.'" role="alert">
											<span style="font-size:18px;">Consulta de datos</span><br>
											'.$tiempoEjecuta.' | total de tiempo transcurrido : '.$diff->h.' horas y '.$diff->i.' minutos y '.$diff->s.' segundos<br>
											Total de datos : '.$total_datos.'<br>
											'.$mensaje.'
										</div>
										
										<div class="alert '.$errorDataPost.'" role="alert">
											<span style="font-size:18px;">Envio de datos</span><br>
											'.$tiempoEjecutaPost.' | total de tiempo transcurrido : '.$diff1->h.' horas y '.$diff1->i.' minutos y '.$diff1->s.' segundos<br>
											Total de datos : '.$total_datos.'<br>
											'.$mensajePost.'
										</div>
								 </div>';	
						}
						//finaliza la primera peticion Asistencias
						
						//inicia la primera peticion Despachos
						if($apiSel=='all' or $apiSel=='Despachos'){
							$errorData = "";
							$mensaje = "";
							$mensajePost = "";
							$cabecera = "";
							$tiempoEjecuta = "";
							$tiempoEjecutaPost = "";
							$tiempoEjecutaInicio = date('Y-m-d H:i:s');
							$total_datos = 0;	
							$errorDataPost = "";
							
							$cabecera .= "<strong>Despachos del ".$FechaInicioMysql." al ".$FechaFinMysql."</strong><br>";
							$tiempoEjecuta .= "Fecha inicio : ".$tiempoEjecutaInicio;
							
							
							ApiHttpClient::Init($RutaApiSql);
							ApiHttpClient::$UserName = $Username;
							ApiHttpClient::$Password = $Password;
							ApiHttpClient::$MacAddress =  $dataMacAddress;
							ApiHttpClient::$Token = $TokenApiSql;
							$parametros = json_encode(Array(
										"Token" => $TokenApiSql,
										"FechaInicio" =>  $FechaInicio,
										"FechaFin" => $FechaFin,
										"MacAddress" => $dataMacAddress
									)); 

							$datos = ApiHttpClient::ConsumeApi('/api/ApiDespachos.php','GET',$parametros);
							$arraydatos = json_decode($datos,true);
							
							$tiempoEjecutafinal = date('Y-m-d H:i:s');
							
							$tiempoEjecutaInicioPost = date('Y-m-d H:i:s');
							$tiempoEjecutaPost .= "Fecha inicio : ".$tiempoEjecutaInicioPost;
							
							$mDesarrollo10 = "";
										if(isset($arraydatos['desarrollo'])){
											$mDesarrollo10 .= "<br>".$arraydatos['desarrollo'];
										}
							
							
							if(ApiHttpClient::$EstatusHttp == 200){
								if(isset($arraydatos['resultado'])){
									if($arraydatos['datos']){
										$total_datos = count($arraydatos['datos']);
									}
									$errorData = 'alert-success';
									if($total_datos == 0){
										$mensaje .= "Sin datos en la consulta";
										$mensajePost .=  "No hay datos en la petición";
									}else{
										
										$mensaje .= "Datos obtenidos exito";
										//$mensaje .= " ----> ";
										$errorData = 'alert-success';

										ApiHttpClient::Init($RutaApiMysql);
										ApiHttpClient::$Token = $TokenApiMysql;
										ApiHttpClient::$UserName = $UsernameMysql;
										ApiHttpClient::$Password = $PasswordMysql;
										ApiHttpClient::$MacAddress =  $dataMacAddress;
										$parametrosMysql = json_encode(Array(
																"Token" => $TokenApiMysql,
																"FechaInicio" =>  $FechaInicioMysql,
																"FechaFin" => $FechaFinMysql,
																"SIIC"  => $establecimientoID,
																"datosCliente" => $arraydatos['datos']
														   )); 

										if($arraydatos['datos']){
											$total_datos = count($arraydatos['datos']);
										}

										$apiFactResp = ApiHttpClient::ConsumeApi('/api/ApiVentasCombustibles.php','POST',$parametrosMysql);
										$arrayApiFac = json_decode($apiFactResp,true);

										$mDesarrollo11 = "";
										if(isset($arrayApiFac['desarrollo'])){
											$mDesarrollo11 .= "<br>".$arrayApiFac['desarrollo'];
										}
										
										
										if(ApiHttpClient::$EstatusHttp == 200){												
											if(isset($arrayApiFac['mensaje'])){
												$mensajePost .=  "Datos importados a la nube : ".$arrayApiFac['mensaje'];
												//$errorData++;
												$errorDataPost = 'alert-success';
											}else{
												$mensajePost .=  "Mensaje : ".$apiFactResp.$mDesarrollo11;
												$errorDataPost = 'alert-warning';
											}
										}else{
											$mensajePost .=  "Ocurrio un error al importar los datos (".$apiFactResp.")".$mDesarrollo11;
											$errorDataPost = 'alert-danger';
										}
									}
									
									
								}else{
									$mensaje .= "No encontramos datos en esta fecha".$mDesarrollo10;
									$errorData = 'alert-warning';
								}
								
								$tiempoEjecutafinalPost = date('Y-m-d H:i:s');
									
								$date11 = new DateTime($tiempoEjecutaInicioPost);
								$date21 = new DateTime($tiempoEjecutafinalPost);
								$diff1 = $date11->diff($date21);	
								$tiempoEjecutaPost .= " | Fecha final : ".$tiempoEjecutafinalPost;
							}else{
								$mensaje .= "Error al consultar los datos (".$datos.")".$mDesarrollo10;
								$errorData = 'alert-danger';
								
								$tiempoEjecutafinalPost = date('Y-m-d H:i:s');
								$date11 = new DateTime($tiempoEjecutaInicioPost);
								$date21 = new DateTime($tiempoEjecutafinalPost);
								$diff1 = $date11->diff($date21);
							}


							$tiempoEjecuta .= " | Fecha final : ".$tiempoEjecutafinal;

							$date1 = new DateTime($tiempoEjecutaInicio);
							$date2 = new DateTime($tiempoEjecutafinal);
							$diff = $date1->diff($date2);	
							
							
							
							//print_r($diff);

							
							echo '<div class="alert alert-light" role="alert" style="border: 1px solid #dfdfdf;">
											'.$cabecera.'
										<div class="alert '.$errorData.'" role="alert">
											<span style="font-size:18px;">Consulta de datos</span><br>
											'.$tiempoEjecuta.' | total de tiempo transcurrido : '.$diff->h.' horas y '.$diff->i.' minutos y '.$diff->s.' segundos<br>
											Total de datos : '.$total_datos.'<br>
											'.$mensaje.'
										</div>
										
										<div class="alert '.$errorDataPost.'" role="alert">
											<span style="font-size:18px;">Envio de datos</span><br>
											'.$tiempoEjecutaPost.' | total de tiempo transcurrido : '.$diff1->h.' horas y '.$diff1->i.' minutos y '.$diff1->s.' segundos<br>
											Total de datos : '.$total_datos.'<br>
											'.$mensajePost.'
										</div>
								 </div>';	
						}
						//finaliza la primera peticion Despachos
						
						
						//inicia la primera peticion lecturas
						if($apiSel=='all' or $apiSel=='Lecturas'){
							$errorData = "";
							$mensaje = "";
							$mensajePost = "";
							$cabecera = "";
							$tiempoEjecuta = "";
							$tiempoEjecutaPost = "";
							$tiempoEjecutaInicio = date('Y-m-d H:i:s');
							$total_datos = 0;	
							$errorDataPost = "";
							
							$cabecera .= "<strong>lecturas del ".$FechaInicioMysql." al ".$FechaFinMysql."</strong><br>";
							$tiempoEjecuta .= "Fecha inicio : ".$tiempoEjecutaInicio;
							
							
							ApiHttpClient::Init($RutaApiSql);
							ApiHttpClient::$UserName = $Username;
							ApiHttpClient::$Password = $Password;
							ApiHttpClient::$MacAddress =  $dataMacAddress;
							ApiHttpClient::$Token = $TokenApiSql;
							$parametros = json_encode(Array(
										"Token" => $TokenApiSql,
										"Fecha" =>  $FechaInicio,
										"MacAddress" => $dataMacAddress
									)); 

							$datos = ApiHttpClient::ConsumeApi('/api/ApiLecturasInicio.php','GET',$parametros);
							$arraydatos = json_decode($datos,true);
							
							$tiempoEjecutafinal = date('Y-m-d H:i:s');
							
							$tiempoEjecutaInicioPost = date('Y-m-d H:i:s');
							$tiempoEjecutaPost .= "Fecha inicio : ".$tiempoEjecutaInicioPost;
							
							$mDesarrollo12 = "";
										if(isset($arraydatos['desarrollo'])){
											$mDesarrollo12 .= "<br>".$arraydatos['desarrollo'];
										}
							
							
							if(ApiHttpClient::$EstatusHttp == 200){
								if(isset($arraydatos['resultado'])){
									if($arraydatos['datos']){
										$total_datos = count($arraydatos['datos']);
									}
									$errorData = 'alert-success';
									if($total_datos == 0){
										$mensaje .= "Sin datos en la consulta";
										$mensajePost .=  "No hay datos en la petición";
									}else{
										$mensaje .= "Datos obtenidos exito";
										//$mensaje .= " ----> ";
										$errorData = 'alert-success';

										ApiHttpClient::Init($RutaApiMysql);
										ApiHttpClient::$Token = $TokenApiMysql;
										ApiHttpClient::$UserName = $UsernameMysql;
										ApiHttpClient::$Password = $PasswordMysql;
										ApiHttpClient::$MacAddress =  $dataMacAddress;
										$parametrosMysql = json_encode(Array(
																"Token" => $TokenApiMysql,
																"FechaInicio" =>  $FechaInicioMysql,
																"FechaFin" => $FechaFinMysql,
																"datosCliente" => $arraydatos['datos']
														   )); 

										if($arraydatos['datos']){
											$total_datos = count($arraydatos['datos']);
										}

										$apiFactResp = ApiHttpClient::ConsumeApi('/api/ApiActualizaLecturas.php','POST',$parametrosMysql);
										$arrayApiFac = json_decode($apiFactResp,true);

										$mDesarrollo13 = "";
										if(isset($arrayApiFac['desarrollo'])){
											$mDesarrollo13 .= "<br>".$arrayApiFac['desarrollo'];
										}
										
										if(ApiHttpClient::$EstatusHttp == 200){												
											if(isset($arrayApiFac['mensaje'])){
												$mensajePost .=  "Datos importados a la nube : ".$arrayApiFac['mensaje'];
												//$errorData++;
												$errorDataPost = 'alert-success';
											}else{
												$mensajePost .=  "Mensaje : ".$apiFactResp.$mDesarrollo13;
												$errorDataPost = 'alert-warning';
											}
										}else{
											$mensajePost .=  "Ocurrio un error al importar los datos (".$apiFactResp.")".$mDesarrollo13;
											$errorDataPost = 'alert-danger';
										}
									}
									
									
								}else{
									$mensaje .= "No encontramos datos en esta fecha".$mDesarrollo12;
									$errorData = 'alert-warning';
								}
								
								$tiempoEjecutafinalPost = date('Y-m-d H:i:s');
									
								$date11 = new DateTime($tiempoEjecutaInicioPost);
								$date21 = new DateTime($tiempoEjecutafinalPost);
								$diff1 = $date11->diff($date21);	
								$tiempoEjecutaPost .= " | Fecha final : ".$tiempoEjecutafinalPost;
							}else{
								$mensaje .= "Error al consultar los datos (".$datos.")".$mDesarrollo12;
								$errorData = 'alert-danger';
								
								$tiempoEjecutafinalPost = date('Y-m-d H:i:s');
								$date11 = new DateTime($tiempoEjecutaInicioPost);
								$date21 = new DateTime($tiempoEjecutafinalPost);
								$diff1 = $date11->diff($date21);
							}


							$tiempoEjecuta .= " | Fecha final : ".$tiempoEjecutafinal;

							$date1 = new DateTime($tiempoEjecutaInicio);
							$date2 = new DateTime($tiempoEjecutafinal);
							$diff = $date1->diff($date2);	
							
							
							
							//print_r($diff);

							
							echo '<div class="alert alert-light" role="alert" style="border: 1px solid #dfdfdf;">
											'.$cabecera.'
										<div class="alert '.$errorData.'" role="alert">
											<span style="font-size:18px;">Consulta de datos</span><br>
											'.$tiempoEjecuta.' | total de tiempo transcurrido : '.$diff->h.' horas y '.$diff->i.' minutos y '.$diff->s.' segundos<br>
											Total de datos : '.$total_datos.'<br>
											'.$mensaje.'
										</div>
										
										<div class="alert '.$errorDataPost.'" role="alert">
											<span style="font-size:18px;">Envio de datos</span><br>
											'.$tiempoEjecutaPost.' | total de tiempo transcurrido : '.$diff1->h.' horas y '.$diff1->i.' minutos y '.$diff1->s.' segundos<br>
											Total de datos : '.$total_datos.'<br>
											'.$mensajePost.'
										</div>
								 </div>';	
						}
						//finaliza la primera peticion lecturas
						
						
						//inicia la primera peticion volumen
						if($apiSel=='all' or $apiSel=='Volumen'){
							$errorData = "";
							$mensaje = "";
							$mensajePost = "";
							$cabecera = "";
							$tiempoEjecuta = "";
							$tiempoEjecutaPost = "";
							$tiempoEjecutaInicio = date('Y-m-d H:i:s');
							$total_datos = 0;	
							$errorDataPost = "";
							
							$cabecera .= "<strong>volumenes del ".$FechaInicioMysql." al ".$FechaFinMysql."</strong><br>";
							$tiempoEjecuta .= "Fecha inicio : ".$tiempoEjecutaInicio;
							
							
							ApiHttpClient::Init($RutaApiSql);
							ApiHttpClient::$UserName = $Username;
							ApiHttpClient::$Password = $Password;
							ApiHttpClient::$MacAddress =  $dataMacAddress;
							ApiHttpClient::$Token = $TokenApiSql;
							$parametros = json_encode(Array(
										"Token" => $TokenApiSql,
										"FechaInicio" =>  $FechaInicio,
										"FechaFin" => $FechaFin,
										"MacAddress" => $dataMacAddress
									)); 

							$datos = ApiHttpClient::ConsumeApi('/api/ApiVolumenTanques.php','GET',$parametros);
							$arraydatos = json_decode($datos,true);
							
							$tiempoEjecutafinal = date('Y-m-d H:i:s');
							
							$tiempoEjecutaInicioPost = date('Y-m-d H:i:s');
							$tiempoEjecutaPost .= "Fecha inicio : ".$tiempoEjecutaInicioPost;
							
							$mDesarrollo14 = "";
										if(isset($arraydatos['desarrollo'])){
											$mDesarrollo14 .= "<br>".$arraydatos['desarrollo'];
										}
							
							
							if(ApiHttpClient::$EstatusHttp == 200){
								if(isset($arraydatos['resultado'])){
									if($arraydatos['datos']){
										$total_datos = count($arraydatos['datos']);
									}
									$errorData = 'alert-success';
									if($total_datos == 0){
										$mensaje .= "Sin datos en la consulta";
										$mensajePost .=  "No hay datos en la petición";
									}else{
										$mensaje .= "Datos obtenidos exito";
										//$mensaje .= " ----> ";
										$errorData = 'alert-success';

										ApiHttpClient::Init($RutaApiMysql);
										ApiHttpClient::$Token = $TokenApiMysql;
										ApiHttpClient::$UserName = $UsernameMysql;
										ApiHttpClient::$Password = $PasswordMysql;
										ApiHttpClient::$MacAddress =  $dataMacAddress;
										$parametrosMysql = json_encode(Array(
																"Token" => $TokenApiMysql,
																"FechaInicio" =>  $FechaInicioMysql,
																"FechaFin" => $FechaFinMysql,
																"datosCliente" => $arraydatos['datos']
														   )); 

										if($arraydatos['datos']){
											$total_datos = count($arraydatos['datos']);
										}

										$apiFactResp = ApiHttpClient::ConsumeApi('/api/ApiActualizaVolumen.php','POST',$parametrosMysql);
										$arrayApiFac = json_decode($apiFactResp,true);

										$mDesarrollo15 = "";
										if(isset($arrayApiFac['desarrollo'])){
											$mDesarrollo15 .= "<br>".$arrayApiFac['desarrollo'];
										}
										
										
										if(ApiHttpClient::$EstatusHttp == 200){												
											if(isset($arrayApiFac['mensaje'])){
												$mensajePost .=  "Datos importados a la nube : ".$arrayApiFac['mensaje'];
												//$errorData++;
												$errorDataPost = 'alert-success';
											}else{
												$mensajePost .=  "Mensaje : ".$apiFactResp.$mDesarrollo15;
												$errorDataPost = 'alert-warning';
											}
										}else{
											$mensajePost .=  "Ocurrio un error al importar los datos (".$apiFactResp.")".$mDesarrollo15;
											$errorDataPost = 'alert-danger';
										}
									}
									
									
								}else{
									$mensaje .= "No encontramos datos en esta fecha".$mDesarrollo14;
									$errorData = 'alert-warning';
								}
								
								$tiempoEjecutafinalPost = date('Y-m-d H:i:s');
									
								$date11 = new DateTime($tiempoEjecutaInicioPost);
								$date21 = new DateTime($tiempoEjecutafinalPost);
								$diff1 = $date11->diff($date21);	
								$tiempoEjecutaPost .= " | Fecha final : ".$tiempoEjecutafinalPost.$mDesarrollo14;
							}else{
								$mensaje .= "Error al consultar los datos (".$datos.")";
								$errorData = 'alert-danger';
								
								$tiempoEjecutafinalPost = date('Y-m-d H:i:s');
								$date11 = new DateTime($tiempoEjecutaInicioPost);
								$date21 = new DateTime($tiempoEjecutafinalPost);
								$diff1 = $date11->diff($date21);
							}


							$tiempoEjecuta .= " | Fecha final : ".$tiempoEjecutafinal;

							$date1 = new DateTime($tiempoEjecutaInicio);
							$date2 = new DateTime($tiempoEjecutafinal);
							$diff = $date1->diff($date2);	
							
							
							
							//print_r($diff);

							
							echo '<div class="alert alert-light" role="alert" style="border: 1px solid #dfdfdf;">
											'.$cabecera.'
										<div class="alert '.$errorData.'" role="alert">
											<span style="font-size:18px;">Consulta de datos</span><br>
											'.$tiempoEjecuta.' | total de tiempo transcurrido : '.$diff->h.' horas y '.$diff->i.' minutos y '.$diff->s.' segundos<br>
											Total de datos : '.$total_datos.'<br>
											'.$mensaje.'
										</div>
										
										<div class="alert '.$errorDataPost.'" role="alert">
											<span style="font-size:18px;">Envio de datos</span><br>
											'.$tiempoEjecutaPost.' | total de tiempo transcurrido : '.$diff1->h.' horas y '.$diff1->i.' minutos y '.$diff1->s.' segundos<br>
											Total de datos : '.$total_datos.'<br>
											'.$mensajePost.'
										</div>
								 </div>';		
						}
						//finaliza la primera peticion volumen



						//echo $mensaje;
						if(strtotime($FechaFinWhile) != strtotime($fechaaamostar)) { 						
							$fechaaamostar = date("Y-m-d", strtotime($fechaaamostar . " + 1 day")); 
						}else{ 
							break; 
						} 
					}
					
					$tiempoTotalFinal = date('Y-m-d H:i:s');
					$dateInicial = new DateTime($tiempoTotalInicial);
					$dateFinal = new DateTime($tiempoTotalFinal);
					$diffFinal = $dateInicial->diff($dateFinal);	
					echo "Fecha inicio ".$tiempoTotalInicial." | Fecha final ".$tiempoTotalFinal." | Tiempo trancurrido : ".$diffFinal->h." horas y ".$diffFinal->i." minutos y ".$diffFinal->s." segundos";
					
				}else{
						if($errorData == 0){
								echo '<div class="alert alert-success" role="alert">										
										'.$mensaje.'
								</div>';
						}else{
								 echo '<div class="alert alert-warning" role="alert">
										'.$mensaje.'
								</div>'; 	
						}
				}			
				
			
			}else{
				echo '<div style="text-align: center;">La fecha de inicio debe de ser mayor o igual a la fecha final</div>';
			}
		}else{
			echo '<div style="text-align: center;">Debes ingresar los datos del formulario para iniciar el proceso de exportacion</div>';
		}
		echo "</div>";
		?>
	</div>
</div>
<script>
	 function cargando(){
		document.getElementById('cargando').style.display = 'block';
		document.getElementById('contenidoInfo').style.display = 'none';
		document.getElementById('btn_export').disabled = true;
		
		startday=new Date();
		clockStart=startday.getTime();
		window.setTimeout('getSecs()',1);
	 }  
	
	
		 
	function initStopwatch(){
		var myTime=new Date();
		return((myTime.getTime()-clockStart)/1000);
	}
		 
	function getSecs(){
		var tSecs=Math.round(initStopwatch());
		var iSecs=tSecs%60;
		var iMins=Math.round((tSecs-30)/60);
		var sSecs=""+((iSecs>9)?iSecs:"0"+iSecs);
		var sMins=""+((iMins>9)?iMins:"0"+iMins);
		document.getElementById('timespent').value=sMins+":"+sSecs;
		window.setTimeout('getSecs()',1000);
	}
		 
		
</script>
