<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '0');
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
				'action' => ['site/sqltomysql&f='.$_GET['f']],
				'options' => ['method' => 'post', 'class' => 'form-inline float-sm-right', 'onsubmit' => 'cargando()'],				
			]) 
			?>
    		<div class="form-group" style="margin-right: 20px;">
				<label>Fecha Inicial:</label> &nbsp;&nbsp;&nbsp;
				<input name="FechaInicial" required class="form-control input-sm" type="date">				
			</div>
			<div class="form-group" style="margin-right: 20px;">
				<label>Fecha Final:</label> &nbsp;&nbsp;&nbsp;
				<input name="FechaFinal" required class="form-control input-sm" type="date">				
			</div>
			
			<div class="form-group" style="margin-right: 20px;">
				<label>Establecimiento:</label> &nbsp;&nbsp;&nbsp;
				<select name="Siic" required class="form-control input-sm">
  					<option value=""> -- Selecciona --</option>
					<?php
					$establecimientos =  Yii::$app->db->createCommand('SELECT establecimientoID,aliasEstablecimiento FROM Establecimientos where regEstado=1')->queryAll();
					foreach($establecimientos as $restab){
						echo '<option value="'.$restab['establecimientoID'].'">'.$restab['aliasEstablecimiento'].'</option>';
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
			
			$clApp1 = Yii::$app->db->createCommand("SELECT ConexionesApis.rutaApi, ConexionesApis.usuario, ConexionesApis.password FROM ConexionesApis
			 	inner join ConexionesApisEstablecimientos on ConexionesApisEstablecimientos.conexionApiID = ConexionesApis.conexionApiID
				inner join AplicacionesConexionApi on AplicacionesConexionApi.aplicacionConexionApiID= ConexionesApis.aplicacionConexionApiID
				where descripcion='ConVolSQLServer' and establecimientoID = '".$_POST['Siic']."'")->queryOne();
			
			$clApp2 = Yii::$app->db->createCommand("SELECT ConexionesApis.rutaApi, ConexionesApis.usuario, ConexionesApis.password FROM ConexionesApis
			 	inner join ConexionesApisEstablecimientos on ConexionesApisEstablecimientos.conexionApiID = ConexionesApis.conexionApiID
				inner join AplicacionesConexionApi on AplicacionesConexionApi.aplicacionConexionApiID= ConexionesApis.aplicacionConexionApiID
				where descripcion='MobileClientesGrupo' and establecimientoID = '".$_POST['Siic']."'")->queryOne();
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
				
				$TokenApiPaso1 = "";
				$Token  = ApiHttpClient::SolicitaToken('/api/ApiTokenSQLServer.php');
				
				try{
					if(ApiHttpClient::$Resultado){
						$TokenApiPaso1 = ApiHttpClient::$Token;
					}else{
							//echo ApiHttpClient::$Resultado;
						$mensaje .= "Token SQL: Ocurrio un error ".ApiHttpClient::$Mensaje." ----> ";
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
				
				$TokenApiPaso2 = "";
				$Token  = ApiHttpClient::SolicitaToken('/api/ApiTokenSQLServer.php');
				
				try{
					if(ApiHttpClient::$Resultado){
						$TokenApiPaso2 = ApiHttpClient::$Token;
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
				
				if($TokenApiPaso1 != "" and $TokenApiPaso2 != "" ){
					while(strtotime($FechaFinWhile) >= strtotime($FechaInicio)) { 
						//echo $fechaaamostar."<br />"; 
						
						$FechaInicio = date("Ymd 00:00:00", strtotime($fechaaamostar));
						$FechaFin = date("Ymd 23:59:59", strtotime($fechaaamostar));		

						$FechaInicioMysql = date("Y-m-d 00:00:00", strtotime($fechaaamostar));
						$FechaFinMysql = date("Y-m-d 23:59:59", strtotime($fechaaamostar));

						//inicia la primera peticion RecepcionesCargas
						$errorData = 0;
						$mensaje = "";
						$cabecera = "";
						$tiempoEjecuta = "";
						$tiempoEjecutaInicio = date('Y-m-d H:i:s');
						$total_datos = 0;					
						$cabecera .= "<strong>Consulta del ".$FechaInicioMysql." al ".$FechaFinMysql." para obtener notas</strong><br>";
						$tiempoEjecuta .= "Fecha inicio : ".$tiempoEjecutaInicio;

						$parametros = json_encode(Array(
									"Token" => $TokenApiSql,
									"FechaInicio" =>  $FechaInicio,
									"FechaFin" => $FechaFin,
									"MacAddress" => $dataMacAddress
								)); 
							
						$datos = ApiHttpClient::ConsumeApi('/api/ApiObtenerNotas.php','GET',$parametros);
						$arraydatos = json_decode($datos,true);
						
						if(isset($arraydatos['resultado'])){
							if($arraydatos['mensaje']=='Datos entregados'){
								$mensaje .= "Datos obtenidos exito";
								$mensaje .= " ----> ";
								
								$parametrosMysql = json_encode(Array(
														"Token" => $TokenApiPaso2,
														"datosCliente" => $arraydatos['datos']
												   )); 
								
								//print_r($parametrosMysql);
								if($arraydatos['datos']){
									$total_datos = count($arraydatos['datos']);
								}
								
								$apiFactResp = ApiHttpClient::ConsumeApi('/api/ApiVentasFacturadas.php','POST',$parametrosMysql);
								$arrayApiFac = json_decode($apiFactResp,true);
								
								if(isset($arrayApiFac['mensaje'])){												
									if($arrayApiFac['mensaje'] = 'Datos creados'){
										$mensaje .=  "Datos importados a la nube : ".$arrayApiFac['mensaje'];
										//$errorData++;
									}else{
										$mensaje .=  "Mensaje : ".$arrayApiFac['mensaje'];
										$errorData++;
									}
								}else{
									$mensaje .=  "Ocurrio un error al importar los datos (".$apiFactResp.")";
									$errorData++;
								}
								
								
							}else{
								$mensaje .= "No encontramos datos en esta fecha";
								$errorData++;
							}
						}else{
							$mensaje .= "Error al consultar los datos (".$datos.")";
							$errorData++;
						}


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
										Datos enviados : '.$total_datos.'<br>
										'.$mensaje.'
								</div>';
						}else{
							echo '<div class="alert alert-warning" role="alert">
										'.$cabecera.'
										'.$tiempoEjecuta.' | total de tiempo transcurrido : '.$diff->h.' horas y '.$diff->i.' minutos y '.$diff->s.' segundos<br>
										Datos enviados : '.$total_datos.'<br>
										'.$mensaje.'
								</div>'; 	
						}	
						//finaliza la primera peticion RecepcionesCargas
						
						
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
		
		window.setTimeout('getSecs()',1);
	 }  
	
	startday=new Date();
	clockStart=startday.getTime();
		 
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
