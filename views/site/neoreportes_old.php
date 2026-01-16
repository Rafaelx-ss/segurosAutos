<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '0');
ini_set('mysql.connect_timeout', '-1');

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
				'action' => ['site/neoreportes&f='.$_GET['f']],
				'options' => ['method' => 'post', 'class' => 'form-inline float-sm-right', 'onsubmit' => 'cargando()'],				
			]);
			
			$vfi = "";
			$tipoData = "";
			$vSi = 0;
			if(isset($_POST['FechaInicial']) and isset($_POST['tipo'])  and isset($_POST['Siic'])){
				$vfi = 'value="'.$_POST['FechaInicial'].'"';
				$tipoData = 'value="'.$_POST['tipo'].'"';
				$vSi = $_POST['Siic'];
			}
			?>
    		
			<div class="form-group" style="margin-right: 20px;">
				<label>Fecha:</label> &nbsp;&nbsp;&nbsp;
				<input name="FechaInicial" required class="form-control input-sm" type="date" <?php echo $vfi; ?>>				
			</div>
			
			<div class="form-group" style="margin-right: 20px;">
				<label>Tipo reporte:</label> &nbsp;&nbsp;&nbsp;
				<select name="tipo" required class="form-control input-sm">
  					<option value=""> -- Selecciona --</option>
					<?php
					if($tipoData == 'D'){
						echo '<option value="D" selected> Diario</option>
							  <option value="M"> Mensual</option>';
					}else{
						echo '<option value="D"> Diario</option>
							  <option value="M" selected> Mensual</option>';
					}
					?>
					
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
		require_once($apiBase."/clientemigracion/config/llaves.php");
		$FechaInicio="";
		$TipoReportes="";
		$establecimientoID="";
		
		$Username ="";
		$Password ="";
		$RutaApiMysql = "";
		
		echo "<hr>";
			
		echo '<div id="cargando" style="display:none;  color: green; font-size:12px; text-align: center;">
				<img src="'.$baseUrl.'/require/images/cloud-upload.gif" alt="cargando"  style="width: 380px;" /> 
				<input class="timepage" size="5" id="timespent" name="timespent" readonly style="text-align:center;width:200px;font-size:40px;border:1px solid #56aaf3;padding:6px;margin:12px 0 12px 0;">
				<h4>No cierre ni refresque la pagina hasta finalizar el proceso</h4><br>
			</div>';
		echo "<div id='contenidoInfo'>";
		if(isset($_POST['FechaInicial']) and isset($_POST['tipo'])  and isset($_POST['Siic'])){
			
			$FechaInicio = $_POST['FechaInicial'];
			$TipoReportes = $_POST['tipo'];
			$establecimientoID = $_POST['Siic'];
			
					
			
			$existeArchivo =  Yii::$app->db->createCommand('SELECT reporteConVolID,  nombreArchivoReporteConvol  FROM ReporteControlesVolumetricos 	WHERE establecimientoID = "'.$establecimientoID .'" AND periodoReporteConVol = "'.$FechaInicio.'" AND tipoReporteConVol = "'.$TipoReportes.'" AND regEstado = 1	ORDER BY reporteConVolID desc')->queryOne();	
			
			if(isset($existeArchivo['reporteConVolID'])){
				if(isset($_POST['updateFile'])){
					if(isset($_POST['reporteConVolID'])){
						$reporteConVolID = $_POST['reporteConVolID'];
						
						$clApp1 = Yii::$app->db->createCommand("SELECT ConexionesApis.rutaApi, ConexionesApis.usuario, ConexionesApis.password FROM ConexionesApis inner join ConexionesApisEstablecimientos on ConexionesApisEstablecimientos.conexionApiID = ConexionesApis.conexionApiID inner join AplicacionesConexionApi on AplicacionesConexionApi.aplicacionConexionApiID= ConexionesApis.aplicacionConexionApiID where descripcion='BonoboGrupo' and establecimientoID = '".$_POST['Siic']."'")->queryOne();
			
						
						if(isset($clApp1['usuario'])){
							$Username = $clApp1['usuario'];
							openssl_private_decrypt(base64_decode($clApp1['password']), $PasswordGetdec, $llaveprivada);
							$Password = $PasswordGetdec;
							$RutaApiMysql = $clApp1['rutaApi'];
						}

						//echo echo generar el archivo 
						$dataMacAddress = 'd4:61:9d:01:85:18';

						require_once($apiBase."/clientemigracion/libs/ApiHttpClient.php");

						$tiempoTotalInicial = date('Y-m-d H:i:s');

						$mensaje = "";
						$errorData = 0;

						//inicia conexion api 1
						echo "Ruta Api:".$RutaApiMysql;
						ApiHttpClient::Init($RutaApiMysql);
						ApiHttpClient::$UserName = $Username;
						ApiHttpClient::$Password = $Password;
						ApiHttpClient::$MacAddress =  $dataMacAddress;

						$TokenApiPaso1 = "";
						$Token  = ApiHttpClient::SolicitaToken('/api/ApiToken.php');

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

						if($TokenApiPaso1 != ""){


							ApiHttpClient::Init($RutaApiMysql);
							ApiHttpClient::$Token = $TokenApiPaso1;
							ApiHttpClient::$UserName = $Username;
							ApiHttpClient::$Password = $Password;
							ApiHttpClient::$MacAddress =  $dataMacAddress;

							$parametros = json_encode(Array(
											"Token" => $TokenApiPaso1,
											"fechaReporte" =>  $FechaInicio,
											"establecimientoID" => $establecimientoID,
											"MacAddress" => $dataMacAddress
										)); 

							if($TipoReportes  == 'D'){
								$datos = ApiHttpClient::ConsumeApi('/api/ApiConVolReporteDiario.php','GET',$parametros);
							}else{
								$datos = ApiHttpClient::ConsumeApi('/api/ApiConVolReporteMensual.php','GET',$parametros);
							}

							$arraydatos = json_decode($datos,true);
							if(ApiHttpClient::$EstatusHttp == 200){
								if(isset($arraydatos['resultado'])){
									if(isset($arraydatos['datos']['nombreArchivoReporteConVol'])){
										$generado = guarda_archivo($arraydatos['datos']['jsonArchivoReporteConVol'], $arraydatos['datos']['nombreArchivoReporteConVol']);
										//print_r($arraydatos['datos']['nombreArchivoReporteConVol']);
										//print_r($arraydatos['datos']['jsonArchivoReporteConVol']);

										//$generado = true;
										if($generado == true){
											Yii::$app->db->close();
        									Yii::$app->db->open();
											
											$update = "UPDATE ReporteControlesVolumetricos SET regEstado=0 WHERE reporteConVolID='".$reporteConVolID."'";
											Yii::$app->db->createCommand($update)->query();	

											$query = "INSERT INTO ReporteControlesVolumetricos(establecimientoID, tipoReporteConVol, periodoReporteConVol, nombreArchivoReporteConVOl,       versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion,       regFormularioUltimaModificacion, regVersionUltimaModificacion)VALUES('".$establecimientoID."', '".$TipoReportes."', '".$FechaInicio."', '".$arraydatos['datos']['nombreArchivoReporteConVol']."', 1, 1, now(), 1, 1, 1)";

											Yii::$app->db->createCommand($query)->query();	
											echo "<br>";
											echo "<a href='../reportes/".$arraydatos['datos']['nombreArchivoReporteConVol']."'>Descargar Archivo</a>";
										}else{
											 echo '<div class="alert alert-warning" role="alert">
													No pudimos generar el archivo
											</div>';
										}
									}else{
										 echo '<div class="alert alert-warning" role="alert">
													No se obtuvieron los datos para generar el archivo 
											</div>'; 
									}						

								}else{
									 echo '<div class="alert alert-warning" role="alert">
													Ocurrio un error al generar el archivo '.$datos.'
											</div>'; 
								}
							}else{
								 echo '<div class="alert alert-warning" role="alert">
												Ocurrio un error al generar el archivo '.$datos.'
										</div>'; 
							}

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
						 echo '<div class="alert alert-warning" role="alert">
										No encontramos el reporte a actualizar.
								</div>'; 
					}
				}else{
					echo 'El archivo existe desea actualizarlo o ';
				
					echo "<a href='../reportes/".$existeArchivo['nombreArchivoReporteConvol']."' download>Descargar Archivo</a>";
					$form = ActiveForm::begin([
					'id' => 'login-form',
					'action' => ['site/neoreportes&f='.$_GET['f']],
					'options' => ['method' => 'post', 'class' => 'form-inline float-sm-right', 'onsubmit' => 'cargando()'],				
					]);

					echo '<input type="hidden" name="FechaInicial" value="'.$FechaInicio.'" >';
					echo '<input type="hidden" name="tipo" value="'.$TipoReportes.'" >';
					echo '<input type="hidden" name="Siic" value="'.$establecimientoID.'" >';
					echo '<input type="hidden" name="reporteConVolID" value="'.$existeArchivo['reporteConVolID'].'" >';
					echo '<input type="hidden" name="updateFile" value="true" >';

					echo "<br>";
					echo "<br>";
					echo '<div class="form-group" style="margin-top: 10px; margin-left: 5px;">
								<button type="submit" class="btn btn-success " id="btn_export">Actualizar archivo</button>
						</div>';

					ActiveForm::end();
				}
				
			}else{
				$clApp1 = Yii::$app->db->createCommand("SELECT ConexionesApis.rutaApi, ConexionesApis.usuario, ConexionesApis.password FROM ConexionesApis
			 	inner join ConexionesApisEstablecimientos on ConexionesApisEstablecimientos.conexionApiID = ConexionesApis.conexionApiID
				inner join AplicacionesConexionApi on AplicacionesConexionApi.aplicacionConexionApiID= ConexionesApis.aplicacionConexionApiID
				where descripcion='BonoboGrupo' and establecimientoID = '".$_POST['Siic']."'")->queryOne();
			
			
			
			
				if(isset($clApp1['usuario'])){
					$Username = $clApp1['usuario'];
					openssl_private_decrypt(base64_decode($clApp1['password']), $PasswordGetdec, $llaveprivada);
					$Password = $PasswordGetdec;
					$RutaApiMysql = $clApp1['rutaApi'];
				}
				
				//echo echo generar el archivo 
				$dataMacAddress = 'd4:61:9d:01:85:18';
			
				require_once($apiBase."/clientemigracion/libs/ApiHttpClient.php");
			
				$tiempoTotalInicial = date('Y-m-d H:i:s');
				
				$mensaje = "";
				$errorData = 0;
				
				//inicia conexion api 1
				echo "Ruta Api:".$RutaApiMysql;
				ApiHttpClient::Init($RutaApiMysql);
				ApiHttpClient::$UserName = $Username;
				ApiHttpClient::$Password = $Password;
				ApiHttpClient::$MacAddress =  $dataMacAddress;
				
				$TokenApiPaso1 = "";
				$Token  = ApiHttpClient::SolicitaToken('/api/ApiToken.php');
				
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
							
				if($TokenApiPaso1 != ""){
					
					
					ApiHttpClient::Init($RutaApiMysql);
					ApiHttpClient::$Token = $TokenApiPaso1;
					ApiHttpClient::$UserName = $Username;
					ApiHttpClient::$Password = $Password;
					ApiHttpClient::$MacAddress =  $dataMacAddress;
					
					$parametros = json_encode(Array(
									"Token" => $TokenApiPaso1,
									"fechaReporte" =>  $FechaInicio,
									"establecimientoID" => $establecimientoID,
									"MacAddress" => $dataMacAddress
								)); 
					
					if($TipoReportes  == 'D'){
						$datos = ApiHttpClient::ConsumeApi('/api/ApiConVolReporteDiario.php','GET',$parametros);
					}else{
						$datos = ApiHttpClient::ConsumeApi('/api/ApiConVolReporteMensual.php','GET',$parametros);
					}
					
					$arraydatos = json_decode($datos,true);
					if(ApiHttpClient::$EstatusHttp == 200){
						if(isset($arraydatos['resultado'])){
							if(isset($arraydatos['datos']['nombreArchivoReporteConVol'])){
								$generado = guarda_archivo($arraydatos['datos']['jsonArchivoReporteConVol'], $arraydatos['datos']['nombreArchivoReporteConVol']);
								//print_r($arraydatos['datos']['nombreArchivoReporteConVol']);
								//print_r($arraydatos['datos']['jsonArchivoReporteConVol']);

								//$generado = true;
								if($generado == true){
									Yii::$app->db->close();
        							Yii::$app->db->open();
									
									$query = "INSERT INTO ReporteControlesVolumetricos(establecimientoID, tipoReporteConVol, periodoReporteConVol, nombreArchivoReporteConVOl,       versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion,       regFormularioUltimaModificacion, regVersionUltimaModificacion)VALUES('".$establecimientoID."', '".$TipoReportes."', '".$FechaInicio."', '".$arraydatos['datos']['nombreArchivoReporteConVol']."', 1, 1, now(), 1, 1, 1)";

									Yii::$app->db->createCommand($query)->query();	
									echo "<br>";
									echo "<a href='../reportes/".$arraydatos['datos']['nombreArchivoReporteConVol']."'>Descargar Archivo</a>";
								}else{
									 echo '<div class="alert alert-warning" role="alert">
											No pudimos generar el archivo
									</div>';
								}
							}else{
								 echo '<div class="alert alert-warning" role="alert">
											No se obtuvieron los datos para generar el archivo 
									</div>'; 
							}						

						}else{
							 echo '<div class="alert alert-warning" role="alert">
											Ocurrio un error al generar el archivo: '.$datos.'
									</div>'; 
						}
					}else{
						 echo '<div class="alert alert-warning" role="alert">
										Ocurrio un error al generar el archivo: '.$datos.'
								</div>'; 
					}
					
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

<?php
function guarda_archivo($datos, $nombre_archivo){ 
    // serializamos los datos
	$json = $datos;
	// obtenemos el nombre del archivo temporal que va dentro del zip
	$nombre_interno = substr($nombre_archivo,0, strpos($nombre_archivo,".")).'.json';
	// guardamos el json aplicando filtro para quitar nulos
	file_put_contents(Yii::$app->basePath.'/reportes/'.$nombre_interno, preg_replace('/,\s*"[^"]+":null|\||"[^"]+":null,?/', '', $json));
	// creamos el objeto de la clase  zip 
	$zip = new ZipArchive();
    // Creamos y abrimos un archivo zip temporal
	$nombreArchivo = Yii::$app->basePath.'/reportes/'.$nombre_archivo;
    $zip->open( $nombreArchivo, ZipArchive::CREATE);
  
	// Añadimos un archivo en la raid del zip.
	$zip->addFile( Yii::$app->basePath.'/reportes/'.$nombre_interno, $nombre_interno);
	//Añadimos un archivo dentro del directorio que hemos creado
	
	// Una vez añadido los archivos deseados cerramos el zip.
	$zip->close(); 
	// eliminamos el archivo json  temporal 
	unlink(Yii::$app->basePath.'/reportes/'.$nombre_interno);
	// verificamos si se creo con exito el archivo zip
	return file_exists($nombreArchivo);
}
?>
