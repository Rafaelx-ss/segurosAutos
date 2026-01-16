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
				<?=  Yii::$app->globals->getTraductor($txtID, Yii::$app->session['idiomaId'], 'Migracion'); ?>				
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
				'action' => ['site/importarfactura&f='.$_GET['f']],
				'options' => ['method' => 'post', 'class' => 'form-inline float-sm-right', 'onsubmit' => 'cargando()'],				
			]);
			
			$vfi = "";
			$vff = "";
			$vSi = 0;
			$proceso = '';
			if(isset($_POST['FechaInicial']) and isset($_POST['FechaFinal'])  and isset($_POST['Siic'])){
				$vfi = 'value="'.$_POST['FechaInicial'].'"';
				$vff = 'value="'.$_POST['FechaFinal'].'"';
				$vSi = $_POST['Siic'];
				
			}
			?>
    		<div class="form-group" style="margin-right: 20px;">
				<label>Fecha Inicial:</label> &nbsp;&nbsp;&nbsp;
				<input name="FechaInicial" required class="form-control input-sm" type="date" <?php echo $vfi; ?>>				
			</div>
			<div class="form-group" style="margin-right: 20px;">
				<label>Fecha Final:</label> &nbsp;&nbsp;&nbsp;
				<input name="FechaFinal" required class="form-control input-sm" type="date" <?php echo $vff; ?>>				
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
			
			$clApp3 = Yii::$app->db->createCommand("SELECT ConexionesApis.rutaApi, ConexionesApis.usuario, ConexionesApis.password FROM ConexionesApis
			 	inner join ConexionesApisEstablecimientos on ConexionesApisEstablecimientos.conexionApiID = ConexionesApis.conexionApiID
				inner join AplicacionesConexionApi on AplicacionesConexionApi.aplicacionConexionApiID= ConexionesApis.aplicacionConexionApiID
				where descripcion='BonoboGrupo' and establecimientoID = '".$_POST['Siic']."'")->queryOne();
			
			$clApp4 = Yii::$app->db->createCommand("SELECT ConexionesApis.rutaApi, ConexionesApis.usuario, ConexionesApis.password FROM ConexionesApis
			 	inner join ConexionesApisEstablecimientos on ConexionesApisEstablecimientos.conexionApiID = ConexionesApis.conexionApiID
				inner join AplicacionesConexionApi on AplicacionesConexionApi.aplicacionConexionApiID= ConexionesApis.aplicacionConexionApiID
				where descripcion='NeoFactGrupo' and establecimientoID = '".$_POST['Siic']."'")->queryOne();
			
			$clApp5 = Yii::$app->db->createCommand("SELECT ConexionesApis.rutaApi, ConexionesApis.usuario, ConexionesApis.password FROM ConexionesApis
			 	inner join ConexionesApisEstablecimientos on ConexionesApisEstablecimientos.conexionApiID = ConexionesApis.conexionApiID
				inner join AplicacionesConexionApi on AplicacionesConexionApi.aplicacionConexionApiID= ConexionesApis.aplicacionConexionApiID
				where descripcion='MobileClientesGrupo' and establecimientoID = '".$_POST['Siic']."'")->queryOne();
			
			
			require_once($apiBase."/clientemigracion/config/llaves.php");
			require_once($apiBase."/clientemigracion/libs/ApiHttpClient.php");
			
			$establecimientoID = $_POST['Siic'];
			
			$RutaApiNeofact = "";
			$PasswordGet = "";
			$UsernameGet = "";
			$ptemp0 = "";
			
			if(isset($clApp4['usuario'])){
				$UsernameGet = $clApp4['usuario'];
				openssl_private_decrypt(base64_decode($clApp4['password']), $PasswordGetdec, $llaveprivada);
				$PasswordGet = $PasswordGetdec;
				$RutaApiNeofact = $clApp4['rutaApi'];
				$ptemp0 = $clApp4['password'];
			}
			echo "NeoFactGrupo: ".$RutaApiNeofact."/api/ApiToken.php <br> ";
			echo "usuario : ".$UsernameGet."<br>";
			if($ptemp0 == ''){
					echo "pass ApiToken : Error en la contraseña<br>";
			}
			
			$UsernamePost = "";
			$PasswordPOst = "";
			$RutaApiBonobo = "";
			$ptemp1 = "";
			
			if(isset($clApp3['usuario'])){
				$UsernamePost = $clApp3['usuario'];
				openssl_private_decrypt(base64_decode($clApp3['password']), $PasswordPOSTdec, $llaveprivada);
				$PasswordPOst = $PasswordPOSTdec;
				$RutaApiBonobo = $clApp3['rutaApi'];
				$ptemp1 = $clApp3['password'];
			}
			echo "BonoboGrupo: ".$RutaApiBonobo." /api/ApiToken.php<br> ";
			echo "usuario : ".$UsernamePost."<br>";
			if($ptemp1 == ''){
					echo "pass ApiToken : Error en la contraseña<br>";
			}
			$UsernamePostSql = "";
			$PasswordPOstSql = "";
			$RutaApiNeofleetSql = "";
			$ptemp2 = "";
			
			if(isset($clApp5['usuario'])){
				$UsernamePostSql = $clApp5['usuario'];
				openssl_private_decrypt(base64_decode($clApp5['password']), $PasswordPOSTsqldec, $llaveprivada);
				$PasswordPOstSql = $PasswordPOSTsqldec;
				$RutaApiNeofleetSql = $clApp5['rutaApi'];
				$ptemp1 = $clApp5['password'];
			}
			echo "MobileClientesGrupo: ".$RutaApiNeofleetSql."/api/ApiToken.php <br>";
			echo "usuario : ".$UsernamePostSql."<br>";
			if($ptemp1 == ''){
					echo "pass ApiToken : Error en la contraseña<br>";
			}
			
			$dataMacAddress = 'd4:61:9d:01:85:18';
			
			$fechaaamostar = $FechaInicioWhile;
			if($_POST['FechaInicial'] <= $_POST['FechaFinal']){				
				//echo "inicia proceso";
				$tiempoTotalInicial = date('Y-m-d H:i:s');
				
				$mensaje = "";
				$errorData = 0;
				
				//inicia conexion api 1								
				ApiHttpClient::Init($RutaApiNeofact);
				ApiHttpClient::$UserName = $UsernameGet;
				ApiHttpClient::$Password = $PasswordGet;
				ApiHttpClient::$MacAddress =  $dataMacAddress;
				$TokenApiPaso1  = "";
				$Token  = ApiHttpClient::SolicitaToken('/api/ApiToken.php');
				try{
					if(ApiHttpClient::$Resultado){
						$TokenApiPaso1 = ApiHttpClient::$Token;
					}else{
						//echo ApiHttpClient::$Resultado;
						$mensaje .= "Token NeoFactGrupo: Ocurrio un error ".ApiHttpClient::$Mensaje." ----> ";
						$errorData++;
					}

					//echo "Token Local: ".$mensaje." ----> ";
				}catch (Exception $e){
					$mensaje .= "Token NeoFactGrupo: ".$this->mensaje = $e->getMessage()." ----> ";
					$errorData++;
				}
				
				//finaliza conexion api 1
				
				//inicia conexion api 2
				ApiHttpClient::Init($RutaApiNeofleetSql);
				ApiHttpClient::$UserName = $UsernamePostSql;
				ApiHttpClient::$Password = $PasswordPOstSql;
				ApiHttpClient::$MacAddress =  $dataMacAddress;
				
				$TokenApiPaso2 = "";
				$Token  = ApiHttpClient::SolicitaToken('/api/ApiToken.php');
				
				try{
					if(ApiHttpClient::$Resultado){
						$TokenApiPaso2 = ApiHttpClient::$Token;
					}else{
							//echo ApiHttpClient::$Resultado;
						$mensaje .= "Token MobileClientesGrupo: Ocurrio un error ".ApiHttpClient::$Mensaje." ----> ";
						$errorData++;
					}
				}catch (Exception $e){
					$mensaje .= "Token MobileClientesGrupo: ".$this->mensaje = $e->getMessage()." ----> ";
					$errorData++;
				}
				//finaliza conexion api 2
				
				
				//inicia conexion api 3
				ApiHttpClient::Init($RutaApiBonobo);
				ApiHttpClient::$UserName = $UsernamePost;
				ApiHttpClient::$Password = $PasswordPOst;
				ApiHttpClient::$MacAddress =  $dataMacAddress;
				
				$TokenApiPaso3 = "";
				$Token  = ApiHttpClient::SolicitaToken('/api/ApiToken.php');
				
				try{
					if(ApiHttpClient::$Resultado){
						$TokenApiPaso3 = ApiHttpClient::$Token;
					}else{
							//echo ApiHttpClient::$Resultado;
						$mensaje .= "Token BonoboGrupo: Ocurrio un error ".ApiHttpClient::$Mensaje." ----> ";
						$errorData++;
					}
				}catch (Exception $e){
					$mensaje .= "Token BonoboGrupo: ".$this->mensaje = $e->getMessage()." ----> ";
					$errorData++;
				}
				//finaliza conexion api 3
				//if($TokenApiPaso1 != "" and $TokenApiPaso2 != "" and $TokenApiPaso3 != "" ){
				if($TokenApiPaso1 != "" and $TokenApiPaso3 != "" ){
					while(strtotime($FechaFinWhile) >= strtotime($FechaInicio)) { 
						//echo $fechaaamostar."<br />"; 
						
						$FechaInicio = date("Ymd 00:00:00", strtotime($fechaaamostar));
						$FechaFin = date("Ymd 23:59:59.997", strtotime($fechaaamostar));		

						$FechaInicioMysql = date("Y-m-d 00:00:00", strtotime($fechaaamostar));
						$FechaFinMysql = date("Y-m-d 23:59:59.997", strtotime($fechaaamostar));
						
						//echo $fechaaamostar;
						
						echo '<div class="alert alert-light" role="alert" style="border: 1px solid #dfdfdf;">';
							//inicia la primera peticion datos
							$errorData = 0;
							$mensaje = "";
							$cabecera = "";
							$tiempoEjecuta = "";
							$tiempoEjecutaInicio = date('Y-m-d H:i:s');
							$total_datos = 0;					
							$cabecera .= "<strong>Consulta del ".$FechaInicioMysql." al ".$FechaFinMysql." para obtener notas Neofact</strong><br>";
							$tiempoEjecuta .= "Fecha inicio : ".$tiempoEjecutaInicio;

							ApiHttpClient::Init($RutaApiNeofact);
							ApiHttpClient::$UserName = $UsernameGet;
							ApiHttpClient::$Password = $PasswordGet;
							ApiHttpClient::$MacAddress =  $dataMacAddress;
							ApiHttpClient::$Token = $TokenApiPaso1;
							
							

							$parametros = json_encode(Array(
										"Token" => $TokenApiPaso1,
										"FechaInicio" =>  $FechaInicio,
										"FechaFin" => $FechaFin,
										"establecimientoID" => $establecimientoID
									)); 
							//print_r($parametros);
							$datos = ApiHttpClient::ConsumeApi('/api/ApiVentaFacturada.php','GET',$parametros);
							$arraydatos = json_decode($datos,true);
							//echo $datos;
							//print_r($arraydatos);
							//exit;
						
							$mDesarrollo = "";
										if(isset($arraydatos['desarrollo'])){
											$mDesarrollo .= "<br>".$arraydatos['desarrollo'];
										}
										
						
							if(ApiHttpClient::$EstatusHttp == 200){
								if(isset($arraydatos['datos']) and !empty($arraydatos['datos']) and !is_null($arraydatos['datos'])){
									//echo count($arraydatos['datos']);
									//echo "<br>";
									//print_r($arraydatos);
									//echo "<br> total de datos unidos ".count($arraydatos['datos']);
										$numDatos = 0;
										if(is_array($arraydatos['datos'])){
											$numDatos = count($arraydatos['datos']);
										}
										
										$total_datos = $numDatos;
										if($numDatos > 2000){
											$totalIcn = 1;
											$countInc = 0;
											$arrayMc = array();

											foreach($arraydatos['datos'] as $rdata){
												if($totalIcn >= $numDatos){
													//echo "envio los datos faltantes <br>";
													ApiHttpClient::Init($RutaApiBonobo);
													ApiHttpClient::$UserName = $UsernamePost;
													ApiHttpClient::$Password = $PasswordPOst;
													ApiHttpClient::$MacAddress =  $dataMacAddress;
													ApiHttpClient::$Token = $TokenApiPaso3;

													$parametrosMetodoPost = json_encode(Array(
																		"Token" => $TokenApiPaso3,
																		"datosCliente" => $arrayMc
																	));
													$numFInal = count($arrayMc);
													$apiFactResp = ApiHttpClient::ConsumeApi('/api/ApiVentaFacturada.php','PUT',$parametrosMetodoPost);
													$arrayApiFac = json_decode($apiFactResp,true);
													
													$mDesarrollo1 = "";
													if(isset($arrayApiFac['desarrollo'])){
														$mDesarrollo1 .= "<br>".$arrayApiFac['desarrollo'];
													}
													
													
													if(ApiHttpClient::$EstatusHttp == 200){
														if(isset($arrayApiFac['mensaje'])){												
															if($arrayApiFac['mensaje'] == 'Datos creados'){
																$mensaje .=  " Datos importados a la nube : ".$arrayApiFac['mensaje'];
																//$errorData++;
															}else{
																$mensaje .=  "Mensaje : ".$arrayApiFac['mensaje'].$mDesarrollo1;
																$errorData++;
																//echo "error data 1".$errorData;
															}

														}else{
															$mensaje .=  "Ocurrio un error al importar los datos final (".$apiFactResp.")".$mDesarrollo1;
															$errorData++;
															//echo "error data 2".$errorData;
														}
													}else{
														$mensaje .=  "Ocurrio un error al importar los datos final (".$apiFactResp.")".$mDesarrollo1;
														$errorData++;
														//echo "error data 3".$errorData;
													}

												}else{
													if($countInc < 2000){
														$arrayMc[] = $rdata;
														$countInc++;	
														//echo "agrego datos al array".$countInc." <br>";
													}else{
														//echo "envio datos ".$countInc;
														ApiHttpClient::Init($RutaApiBonobo);
														ApiHttpClient::$UserName = $UsernamePost;
														ApiHttpClient::$Password = $PasswordPOst;
														ApiHttpClient::$MacAddress =  $dataMacAddress;
														ApiHttpClient::$Token = $TokenApiPaso3;

														$parametrosMetodoPost = json_encode(Array(
																			"Token" => $TokenApiPaso3,
																			"datosCliente" => $arrayMc
																		));

														$apiFactResp = ApiHttpClient::ConsumeApi('/api/ApiVentaFacturada.php','PUT',$parametrosMetodoPost);
														$arrayApiFac = json_decode($apiFactResp,true);
														
														$mDesarrollo2 = "";
														if(isset($arrayApiFac['desarrollo'])){
															$mDesarrollo2 .= "<br>".$arrayApiFac['desarrollo'];
														}
														
														
														if(ApiHttpClient::$EstatusHttp == 200){
															if(isset($arrayApiFac['mensaje'])){												
																if($arrayApiFac['mensaje'] == 'Datos creados'){
																	$mensaje .=  "Datos importados a la nube : ".$arrayApiFac['mensaje']." | ";
																	//$errorData++;
																}else{
																	$mensaje .=  "Mensaje : ".$arrayApiFac['mensaje'].$mDesarrollo2;
																	$errorData++;
																	//echo "error data 4".$errorData;
																}

															}else{
																$mensaje .=  "Ocurrio un error al importar los datos final (".$apiFactResp.")".$mDesarrollo2;
																$errorData++;
																//echo "error data 5".$errorData;
															}
														}else{
															$mensaje .=  "Ocurrio un error al importar los datos final (".$apiFactResp.")".$mDesarrollo2;
															$errorData++;
															//echo "error data 6".$errorData;
														}
														


														$countInc = 0;
														$arrayMc = array();
													}
												}
												$totalIcn++;
											}
										}else{
											//echo "entro aqui";
											ApiHttpClient::Init($RutaApiBonobo);
											ApiHttpClient::$UserName = $UsernamePost;
											ApiHttpClient::$Password = $PasswordPOst;
											ApiHttpClient::$MacAddress =  $dataMacAddress;
											ApiHttpClient::$Token = $TokenApiPaso3;

											$parametrosMetodoPost = json_encode(Array(
																	"Token" => $TokenApiPaso3,
																	"datosCliente" => $arraydatos['datos']
																));

											$apiFactResp = ApiHttpClient::ConsumeApi('/api/ApiVentaFacturada.php','PUT',$parametrosMetodoPost);
											$arrayApiFac = json_decode($apiFactResp,true);
											//print_r($apiFactResp);
											
											$mDesarrollo3 = "";
														if(isset($arrayApiFac['desarrollo'])){
															$mDesarrollo3 .= "<br>".$arrayApiFac['desarrollo'];
														}
											
											
											if(ApiHttpClient::$EstatusHttp == 200){
												if(isset($arrayApiFac['mensaje'])){												
													if($arrayApiFac['mensaje'] == 'Datos creados'){
														$mensaje .=  "Datos importados a la nube : ".$arrayApiFac['mensaje'];
														//$errorData++;
													}else{
														$mensaje .=  "Mensaje : ".$arrayApiFac['mensaje'].$mDesarrollo3;
														$errorData++;
														//echo "error data 7 - ".$errorData." --- ".$mensaje;
													}
												}else{
													$mensaje .=  "Ocurrio un error al importar los datos menor (".$apiFactResp.")".$mDesarrollo3;
													$errorData++;
													//echo "error data 8".$errorData;
												}
											}else{
												$mensaje .=  "Ocurrio un error al importar los datos menor (".$apiFactResp.")".$mDesarrollo3;
												$errorData++;
												//echo "error data 9".$errorData;
											}
										}
								}else{
									$mensaje .= "Error Neofact: Sin datos para el periodo seleccionado (".$datos.")".$mDesarrollo;
									$errorData++;
									//echo "error data 10 -- ".$errorData." --- ".$mensaje;
								}	
							}else{
								$mensaje .= "Error Neofact :  (".$datos.")".$mDesarrollo;
								$errorData++;
								//echo "error data 11".$errorData;
							}	
							//finaliza primera peticion
						
							$tiempoEjecutafinal = date('Y-m-d H:i:s');
							$tiempoEjecuta .= " | Fecha final : ".$tiempoEjecutafinal;

							$date1 = new DateTime($tiempoEjecutaInicio);
							$date2 = new DateTime($tiempoEjecutafinal);
							$diff = $date1->diff($date2);	
							//print_r($diff);

							if($errorData == 0){
								echo '<div class="alert alert-success" role="alert">
											'.$cabecera.'
											'.$tiempoEjecuta.' | total de tiempo transcurrido : '.$diff->h.' horas y '.$diff->i.' minutos y '.$diff->s.' segundos<br>
											Total de datos : '.$total_datos.'<br>
											'.$mensaje.'
									</div>';
							}else{
								echo '<div class="alert alert-warning" role="alert">
											'.$cabecera.'
											'.$tiempoEjecuta.' | total de tiempo transcurrido : '.$diff->h.' horas y '.$diff->i.' minutos y '.$diff->s.' segundos<br>
											Total de datos : '.$total_datos.'<br>
											'.$mensaje.'
									</div>'; 	
							}
						
							//inicia la segunda peticion datos
							$errorData = 0;
							$mensaje = "";
							$cabecera = "";
							$tiempoEjecuta = "";
							$tiempoEjecutaInicio = date('Y-m-d H:i:s');
							$total_datos = 0;					
							$cabecera .= "<strong>Consulta del ".$FechaInicioMysql." al ".$FechaFinMysql." para obtener notas Mobilefleet</strong><br>";
							$tiempoEjecuta .= "Fecha inicio : ".$tiempoEjecutaInicio;
							
						
							
							ApiHttpClient::Init($RutaApiNeofleetSql);
							ApiHttpClient::$UserName = $UsernamePostSql;
							ApiHttpClient::$Password = $PasswordPOstSql;
							ApiHttpClient::$MacAddress =  $dataMacAddress;
							ApiHttpClient::$Token = $TokenApiPaso2;

							$parametrosS2 = json_encode(Array(
										"Token" => $TokenApiPaso2,
										"FechaInicio" =>  $FechaInicio,
										"FechaFin" => $FechaFin,
										"establecimientoID" => $establecimientoID,
										"MacAddress" => $dataMacAddress
									)); 
							//print_r($parametrosS2);
							$datosP2 = ApiHttpClient::ConsumeApi('/api/ApiVentaFacturada.php','GET',$parametrosS2);
							$arraydatosP2 = json_decode($datosP2,true);
						
							//print_r($arraydatosP2);
						
							$mDesarrollo4 = "";
														if(isset($arraydatosP2['desarrollo'])){
															$mDesarrollo4 .= "<br>".$arraydatosP2['desarrollo'];
														}
						
						
							if(ApiHttpClient::$EstatusHttp == 200){
								if(isset($arraydatosP2['datos']) and !empty($arraydatosP2['datos']) and !is_null($arraydatosP2['datos'])){
									//echo count(arraydatosP2['datos']);
									//echo "<br>";
									//print_r($arraydatosP2);
									//echo "<br> total de datos unidos ".count($arraydatosP2['datos']);
										$numDatos2 = 0;
										$numDatos2 = count($arraydatosP2['datos']);
										$total_datos = $total_datos + $numDatos2;
										if($numDatos2 > 2000){
											$totalIcn = 1;
											$countInc = 0;
											$arrayMc = array();

											foreach($arraydatosP2['datos'] as $rdata){
												if($totalIcn >= $numDatos2){
													//echo "envio los datos faltantes <br>";
													ApiHttpClient::Init($RutaApiBonobo);
													ApiHttpClient::$UserName = $UsernamePost;
													ApiHttpClient::$Password = $PasswordPOst;
													ApiHttpClient::$MacAddress =  $dataMacAddress;
													ApiHttpClient::$Token = $TokenApiPaso3;

													$parametrosMetodoPost = json_encode(Array(
																		"Token" => $TokenApiPaso3,
																		"datosCliente" => $arrayMc
																	));
													$numFInal = count($arrayMc);
													$apiFactResp = ApiHttpClient::ConsumeApi('/api/ApiVentaFacturada.php','PUT',$parametrosMetodoPost);
													$arrayApiFac = json_decode($apiFactResp,true);

													$mDesarrollo5 = "";
														if(isset($arrayApiFac['desarrollo'])){
															$mDesarrollo5 .= "<br>".$arrayApiFac['desarrollo'];
														}
													
													if(ApiHttpClient::$EstatusHttp == 200){
														if(isset($arrayApiFac['mensaje'])){												
															if($arrayApiFac['mensaje'] = 'Datos creados'){
																$mensaje .=  " Datos importados a la nube : ".$arrayApiFac['mensaje'];
																//$errorData++;
															}else{
																$mensaje .=  "Mensaje : ".$arrayApiFac['mensaje'].$mDesarrollo5;
																$errorData++;
															}

														}else{
															$mensaje .=  "Ocurrio un error al importar los datos final (".$apiFactResp.")".$mDesarrollo5;
															$errorData++;
														}
													}else{
														$mensaje .=  "Ocurrio un error al importar los datos final (".$apiFactResp.")".$mDesarrollo5;
														$errorData++;
													}

												}else{
													if($countInc < 2000){
														$arrayMc[] = $rdata;
														$countInc++;	
														//echo "agrego datos al array".$countInc." <br>";
													}else{
														//echo "envio datos ".$countInc;
														ApiHttpClient::Init($RutaApiBonobo);
														ApiHttpClient::$UserName = $UsernamePost;
														ApiHttpClient::$Password = $PasswordPOst;
														ApiHttpClient::$MacAddress =  $dataMacAddress;
														ApiHttpClient::$Token = $TokenApiPaso3;

														$parametrosMetodoPost = json_encode(Array(
																			"Token" => $TokenApiPaso3,
																			"datosCliente" => $arrayMc
																		));

														$apiFactResp = ApiHttpClient::ConsumeApi('/api/ApiVentaFacturada.php','PUT',$parametrosMetodoPost);
														$arrayApiFac = json_decode($apiFactResp,true);
														
														$mDesarrollo6 = "";
														if(isset($arrayApiFac['desarrollo'])){
															$mDesarrollo6 .= "<br>".$arrayApiFac['desarrollo'];
														}
														
														
														if(ApiHttpClient::$EstatusHttp == 200){
															if(isset($arrayApiFac['mensaje'])){												
																if($arrayApiFac['mensaje'] == 'Datos creados'){
																	//$mensaje .=  "200 Datos importados a la nube : ".$arrayApiFac['mensaje']." | ";
																	//$errorData++;
																}else{
																	$mensaje .=  "Mensaje : ".$arrayApiFac['mensaje'].$mDesarrollo6;
																	$errorData++;
																}

															}else{
																$mensaje .=  "Ocurrio un error al importar los datos final (".$apiFactResp.")".$mDesarrollo6;
																$errorData++;
															}
														}else{
															$mensaje .=  "Ocurrio un error al importar los datos final (".$apiFactResp.")".$mDesarrollo6;
															$errorData++;
														}


														$countInc = 0;
														$arrayMc = array();
													}
												}
												$totalIcn++;
											}
										}else{
											ApiHttpClient::Init($RutaApiBonobo);
											ApiHttpClient::$UserName = $UsernamePost;
											ApiHttpClient::$Password = $PasswordPOst;
											ApiHttpClient::$MacAddress =  $dataMacAddress;
											ApiHttpClient::$Token = $TokenApiPaso3;

											$parametrosMetodoPost = json_encode(Array(
																	"Token" => $TokenApiPaso3,
																	"datosCliente" => $arraydatosP2['datos']
																));

											$apiFactResp = ApiHttpClient::ConsumeApi('/api/ApiVentaFacturada.php','PUT',$parametrosMetodoPost);
											$arrayApiFac = json_decode($apiFactResp,true);
											//print_r($apiFactResp);
											
											$mDesarrollo7 = "";
														if(isset($arrayApiFac['desarrollo'])){
															$mDesarrollo7 .= "<br>".$arrayApiFac['desarrollo'];
														}
											
											
											if(ApiHttpClient::$EstatusHttp == 200){
												if(isset($arrayApiFac['mensaje'])){												
													if($arrayApiFac['mensaje'] = 'Datos creados'){
														$mensaje .=  "Datos importados a la nube : ".$arrayApiFac['mensaje'];
														//$errorData++;
													}else{
														$mensaje .=  "Mensaje : ".$arrayApiFac['mensaje'].$mDesarrollo7;
														$errorData++;
													}
												}else{
													$mensaje .=  "Ocurrio un error al importar los datos menor (".$apiFactResp.")".$mDesarrollo7;
													$errorData++;
												}
											}else{
												$mensaje .=  "Ocurrio un error al importar los datos : menor (".$apiFactResp.")".$mDesarrollo7;
												$errorData++;
											}
										}
								}else{
									$mensaje .= "Error Mobilefleet: Sin datos para el periodo seleccionado (".$datosP2.")".$mDesarrollo4;
									$errorData++;
								}
							}else{
								$mensaje .= "Error Mobilefleet: (".$datosP2.")".$mDesarrollo4;
								$errorData++;
							}
							//finaliza segunda peticion

						
						
						
						$tiempoEjecutafinal = date('Y-m-d H:i:s');
						$tiempoEjecuta .= " | Fecha final : ".$tiempoEjecutafinal;

						$date1 = new DateTime($tiempoEjecutaInicio);
						$date2 = new DateTime($tiempoEjecutafinal);
						$diff = $date1->diff($date2);	
						//print_r($diff);

						if($errorData == 0){
							echo '<div class="alert alert-success" role="alert">
										'.$cabecera.'
										'.$tiempoEjecuta.' | total de tiempo transcurrido : '.$diff->h.' horas y '.$diff->i.' minutos y '.$diff->s.' segundos<br>
										Total de datos : '.$total_datos.'<br>
										'.$mensaje.'
								</div>';
						}else{
							echo '<div class="alert alert-warning" role="alert">
										'.$cabecera.'
										'.$tiempoEjecuta.' | total de tiempo transcurrido : '.$diff->h.' horas y '.$diff->i.' minutos y '.$diff->s.' segundos<br>
										Total de datos : '.$total_datos.'<br>
										'.$mensaje.'
								</div>'; 	
						}	
						
						echo '</div>';
						
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
					echo "Fecha inicio de ejecucion: ".$tiempoTotalInicial." | Fecha final de ejecucion: ".$tiempoTotalFinal." | Tiempo trancurrido : ".$diffFinal->h." horas y ".$diffFinal->i." minutos y ".$diffFinal->s." segundos";
					
					
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