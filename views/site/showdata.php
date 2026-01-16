<?php
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\assets\AppAsset;

use yii\helpers\Url;
$asset = AppAsset::register($this);
$baseUrl = $asset->baseUrl;
/* @var $this yii\web\View */
//Yii::$app->response->redirect(['siniestros/index']);
//$this->title = 'My Yii Application';
$apiBase = Yii::$app->basePath;

require_once($apiBase.'/clientemigracion/lib/database.php');
require_once($apiBase.'/clientemigracion/modelos/ModeloApiConexion.php');
  $txtBanderaApi="";

$mensaje2 = '';
header ('Content-type: text/html; charset=utf-8');

$this->registerJsFile($baseUrl.'/require/js/jquery/jquery-2.1.4.js', ['position' => \yii\web\View::POS_HEAD]);


?>
<script type="text/javascript">
	function seleccionaCK(){
		alert(2);
		$('#ck1').attr('checked', this.checked);
		alert(22);
	}
	</script>
<script type="text/javascript">
$(document).ready(function() {
    $('#div-btn1').on('click', function() {
        $("#central").load('inc/presentation.php');
        return false;
    });
});
</script>
<div class="app-page-title">
	<div class="page-title-wrapper">
    	<div class="page-title-heading">
        	<div class="page-title-icon">
            	<i class="pe-7s-menu icon-gradient bg-malibu-beach"></i>
            </div>
            <div>
				Migración				<div class="page-title-subheading">de datos</div>
            </div>
        </div>
       
     </div>
</div>

<div class="main-card mb-3 card">
	<div class="card-body">	
		
		
        <?php
		
		
		$BanderaDiv="";
if (isset($_POST["txtBandera"])){
	//
	$txtBanderaApi=$_POST["txtBandera"];
	echo $Username=Yii::$app->session['Username']."";
	$ValorBandera="";

	if($_POST["BanderaDiv"]=="DESCARGA_COMPLETA"){
		require_once($apiBase."/clientemigracion/descargasfull.php");
	}
	else{
		require_once($apiBase."/clientemigracion/descargas.php");
		//echo $Valor;
		if($_POST["BanderaDiv"]<>""){
		$BanderaDiv= "#".$_POST["BanderaDiv"];
		}
	}
}
else{
	//echo "NADA";
}
$NombreAccion="Importar";
if(Yii::$app->session['TIPOMETODO']=="POST"){
	$NombreAccion="Exportar";
}
$arrayMetodosPOSTPermitidos = array("ApiTextos", "ApiMenus", "ApiCatalogos", "ApiCampos", "ApiCamposGrid", "ApiFormularios", "ApiCombosAnidados", "ApiAcciones", 
"ApiAccionesFormularios", "ApiMenusFormularios",
"ApiTextosBonobo", "ApiMenusBonobo", "ApiCatalogosBonobo", "ApiCamposBonobo", "ApiCamposGridBonobo", "ApiFormulariosBonobo", "ApiCombosAnidadosBonobo", 
"ApiAccionesBonobo", "ApiAccionesFormulariosBonobo", "ApiMenusFormulariosBonobo",
"ApiTextosGrupo", "ApiMenusGrupo", "ApiCatalogosGrupo", "ApiCamposGrupo", "ApiCamposGridGrupo", "ApiFormulariosGrupo", "ApiCombosAnidadosGrupo", 
"ApiAccionesGrupo", "ApiAccionesFormulariosGrupo", "ApiMenusFormulariosGrupo", 
"ApiJornadas","ApiAsistencias","ApiVentasCombustibles","ApiVentasFormasDePago","ApiVentasProductos","ApiVentasDetallesImpuestos",
"ApiTemplatesReportes","ApiReportesConfiguraciones","ApiReportesCampos","ApiApis","ApiScripts","ApiDetalleScripts");
//"ApiPerfiles", "ApiPerfilesCompuestos", "ApiPermisosMenus", "ApiPermisosFormulariosPerfiles", "ApiPerfilAccionFormulario"

		//echo $_SERVER["REQUEST_URI"];
		$form = ActiveForm::begin([
				'id' => 'formulario1',
				'action' => ['site/showdata&f='.$_GET['f'].$BanderaDiv],
				'options' => ['method' => 'post'],
			]);
		?>
		
		<div class="container">
			<main role="main" class="pb-3">



			<?php

			$host= $_SERVER["HTTP_HOST"];
			$url= $_SERVER["REQUEST_URI"];
			$contDatosMostrados=0;
			//echo "http://" . $host ."-". $url;
			
			
			
			if (isset($_POST["txtBandera"])){
				////////////////////////////////////////////////////////////////////////////////////
				//////////////////////////////  SI EXISTE REQUEST //////////////////////////////////
				////////////////////////////////////////////////////////////////////////////////////
				
			}
			else{
				////////////////////////////////////////////////////////////////////////////////////
				///////////////////////////  SI NO EXISTE REQUEST //////////////////////////////////
				////////////////////////////////////////////////////////////////////////////////////
			}	
				
				$database = new Database();
				$conn = $database->getConnection();
				$conexion = new ConexionApi($database);

				$UserName="";
				if($conexion->ObtenerDatos(1))
				{
					
					$Username = $conexion->Dataset['UsuarioApiLista'];
					$Password = $conexion->Dataset['PassApiLista'];
					$RutaApi = $conexion->Dataset['RutaApiLista'];
					
					//$Username = "masterBrentec";
					//$Password = "Brentec2020";
					require_once($apiBase."/clientemigracion/ApiHttpClient.php");
					ApiHttpClient::Init($RutaApi);
					
					if(!isset(Yii::$app->session['Token'])){
						

						ApiHttpClient::$UserName = $Username;
						ApiHttpClient::$Password = $Password;
						//ApiHttpClient::$MacAddress =  '80:30:49:e7:47:ff';

						$token = ApiHttpClient::SolicitaToken('/api/ApiToken.php');

						try{
							if(ApiHttpClient::$Resultado)
							{
								Yii::$app->session->set('Token', $token);
								Yii::$app->session->set('MacAddress', ApiHttpClient::$MacAddress);
								$mensaje = ApiHttpClient::$Mensaje . " Token: " . ApiHttpClient::$Token;
							}
							else
							{
								//echo ApiHttpClient::$Resultado;
								$mensaje = ApiHttpClient::$Mensaje;
							}
						}   
						catch (Exception $e){
							echo $this->mensaje = $e->getMessage();
						}
					}
					
					$tipoLista="A";
					$aplicacionID=0;
					if(Yii::$app->session['TIPOSOLICITUD'] == "E"){
						$establecimientoID= Yii::$app->session['IDENTIFICADOR'];
						$GrupoEstablecimientoID=0;
						$tipoLista="";
					}
					else{
						$establecimientoID= 0;
						$GrupoEstablecimientoID= Yii::$app->session['IDENTIFICADOR'];
					}
					//$aplicacionID= Yii::$app->session['APLICACION_ID'];
					
					//echo "AAA".$conexion->Dataset['UsuarioApiLista']."<br />";
					//echo $UserName."<br />";
					//echo Yii::$app->session['IDENTIFICADOR']."<br />";
					echo "<b>Usuario: </b>";
					echo $conexion->Dataset['UsuarioApiLista'];
					$tempo= Yii::$app->session['TIPOMETODO']; 
					$tipoMetodo=Yii::$app->session['TIPOMETODO'];
					
					$permisoMaster="9";
					$permisoGrupo="9";
					$permisoEstablecimiento="9";
					if(Yii::$app->session['IDENTIFICADOR'] == "0" and $conexion->Dataset['UsuarioApiLista'] == "masterBrentec"  ){
						
						if(Yii::$app->session['TIPOMETODO']=="POST"){
							$aplicacionID=Yii::$app->session['APLICACION_ID'];
							echo "<br /><b>Aplicacion:</b> ".$aplicacionID;
						}
						elseif(Yii::$app->session['TIPOSOLICITUD']=="M"){
							$aplicacionID=1;
							echo "<br /><b>Aplicacion:</b> ".$aplicacionID;
						}
					}elseif(Yii::$app->session['IDENTIFICADOR'] == "0"){
						$aplicacionID=99999;
						echo "SIN DATOS PARA EL IDENTIFICADOR SELECCIONADO <BR />";
						echo  Html::a(' Regresar', $url = ['site/migracion&f='.$_GET['f']], $options = ['class'=>'btn-shadow btn btn-primary mr-3 active']);;
					}
					
					
					$tipoTemp= Yii::$app->session['TIPOSOLICITUD'];
					//echo Yii::$app->session['TIPOSOLICITUD']."MAARRCCOOO";
					
					if($tipoTemp=="M"){
						$permisoMaster="1";
						echo " (Master)";
					}
					if($tipoTemp=="G"){
						$permisoGrupo="1";
						echo " (Grupo)";
					}
					if($tipoTemp=="E"){
						$permisoEstablecimiento="1";
						echo " (Establecimiento)";
					}
					/*
					if($tipoTemp=="G"){
						$permisoGrupo="1";
					}*/
					// echo Yii::$app->session['Token'];
					if($aplicacionID <> 99999){
						$parametros = json_encode(Array(
							"Token" => Yii::$app->session['Token'],
							"aplicacionID" => $aplicacionID,
							"establecimientoID" => $establecimientoID,
							"grupoestablecimientoID" => $GrupoEstablecimientoID,
							"tipoLista" => $tipoLista,
							"tipoMetodo" => $tipoMetodo,
							"permisoMaster" => $permisoMaster,
							"permisoGrupo" => $permisoGrupo,
							"permisoEstablecimiento" => $permisoEstablecimiento,
							"MacAddress" => Yii::$app->session['MacAddress']
						)); 
						// print_r($parametros);
						$datos = ApiHttpClient::ConsumeApi('/api/ApiListadoApis.php','GET',$parametros);
						echo " | Aplicacion : ".$aplicacionID." | ";
						echo "establecimientoID : <strong>".$establecimientoID."</strong> | ";
						echo "grupoestablecimientoID : ".$GrupoEstablecimientoID." <br><br><br>";
						//echo $parametros;
						$arraydatos = json_decode($datos,true);
					   //$arraydatos = array();
					   /*echo $_SESSION["TIPOSOLICITUD"]."<br/>";
					   echo $aplicacionID." A<br/>";
					   echo $establecimientoID." E<br/>";
					   echo $GrupoEstablecimientoID." G<br/>";*****/
					   $mensaje2="";
						if(isset($arraydatos['resultado']))
						{
							//$sesion
							$coleccion = $arraydatos["datos"];
							$contarArreglo=0;
							try{
								$contarArreglo = count($coleccion);
							}
							catch (Exception $e){
								$contarArreglo=0;
							}



							try{
								$cont=1;
								echo ' <div class="row">
											<div class="col-lg-6 margin-tb">
												<div class="pull-left">
													<h2>'.$NombreAccion.' ' . str_replace('Api','',$txtBanderaApi) . '</h2>
													<a class="btn btn-primary" onclick="$(\'#BanderaDiv\').val(\'DESCARGA_COMPLETA\'); document.forms[\'formulario1\'].submit();"  href="javascript:;" 
														style="display:none;">'.$NombreAccion.' seleccionados</a>
												</div>

											</div>
											<div class="col-lg-6 margin-tb">
												<div class="pull-right">';
												echo  Html::a(' Recargar', $url = ['site/showdata&f='.$_GET['f']], $options = ['class'=>'btn-shadow btn btn-primary mr-3 active']);

												echo  Html::a(' Inicio', $url = ['site/migracion&f='.$_GET['f']], $options = ['class'=>'btn-shadow btn btn-primary mr-3 active']);;
										echo '	</div>
											</div>
										</div>
										'.$mensaje2.'
										<input type="hidden" id="txtBandera" name="txtBandera" value="" />
										<input type="hidden" id="BanderaDiv" name="BanderaDiv" value="" />
										<a id="'. $cont .'"></a> 
										<table class="table table-bordered">
											<thead>
												<tr>
													<th>No.</th>
													<th align="center"><label><input type="checkbox" onclick="for (var i = 1; i < '.$contarArreglo.'; i++) {$(\'#ck\' + i).attr(\'checked\', this.checked);};" name="cb-autos" value="gusta"> Seleccionar Todos</label></th>
													<th>Nombre Registro</th>
													<th>Acción</th>
													<th>Resultado</th>
												</tr>
											</thead>
											<tbody>';
											//echo Yii::$app->session['TIPOSOLICITUD'];
								//print_r($coleccion);
								if(!isset(Yii::$app->session['logMigracion'])){
									Yii::$app->session->set("logMigracion", "");
								}
								//echo "<pre>";
								//print_r(Yii::$app->session['logMigracion']);
								foreach($coleccion as $item)
								{
									//if (trim($item['nombreApi']) != "ApiListadoApis" and $item['nombreApi'] <> "ApiFacturasCentrales" and $item['nombreApi'] <> "ApiMigracionCatalogosGrupo" and $item['nombreApi'] <> "ApiMigracionFormulariosGrupo" and $item['nombreApi'] <> "ApiMigracionMenusGrupo"){
										//if(($item['nombreApi'] <> "ApiMigracionCatalogosBonobo" and $item['nombreApi'] <> "ApiMigracionFormulariosBonobo" and $item['nombreApi'] <> "ApiMigracionMenusBonobo" and $item['nombreApi'] <> "ApiApis" and $item['nombreApi'] <> "ApiUsuariosAPI" and $item['nombreApi'] <> "ApiListadoEquiposSeguros" and $item['nombreApi'] <> "ApiMetodosApis" and $item['nombreApi'] <> "ApiMetodosUsuariosApis") || Yii::$app->session['TIPOSOLICITUD']<>"E"){	
											if(trim($item['nombreApi']) != "ApiApis"){
												$nombreTabla= str_replace('Api','',$item['nombreApi']);
											}
											else{
												$nombreTabla= "Apis";
											}
											$RESULTADO_BOTON="";
											$compara=str_replace('Api','',$txtBanderaApi);
											
											if (trim($compara) == trim($nombreTabla)){ // || trim($nombreTabla)=="Api"
												//$style="style='background-color: #0F9F1F; color:#FFF;'";
												$RESULTADO_BOTON= Yii::$app->session['RESULTADO_BOTON'];
											}else{
												$style="";
											}
									
											if(isset(Yii::$app->session['logMigracion'][$item['nombreApi']])){
												//echo "si existe ".$item['nombreApi'];
												//print_r(Yii::$app->session['logMigracion'][$item['nombreApi']]);
												//print_r(Yii::$app->session['logMigracion']);
												//echo Yii::$app->session[$item['nombreApi']."_color"]."-";
												//echo Yii::$app->session[$item['nombreApi']."_error"]."-";
												if(isset(Yii::$app->session['logMigracion'][$item['nombreApi']]['color'])){
													if(Yii::$app->session['logMigracion'][$item['nombreApi']]['color'] == 'verde'){
														$style="style='background-color: #cfff9c; color:#404040;'";
													}
													
													if(Yii::$app->session['logMigracion'][$item['nombreApi']]['color'] == 'rojo'){
														$style="style='background-color: #fe7b7b; color:#404040;'";
													}
													
													if(Yii::$app->session['logMigracion'][$item['nombreApi']]['color'] == 'naranja'){
														$style="style='background-color: #fdedd9; color:#404040;'";
													}											
													
												}
												
												
												if(isset(Yii::$app->session['logMigracion'][$item['nombreApi']]['mensajeCorto'])){
													if(Yii::$app->session['logMigracion'][$item['nombreApi']]['mensajeCorto'] != ""){
														$RESULTADO_BOTON= Yii::$app->session['logMigracion'][$item['nombreApi']]['mensajeCorto'];
													}
												}
											}
											//background-color: #0F9F1F; color:#FFF;

											if(trim($item['nombreApi']) != "ApiListadoApis"){
												if(trim($item['nombreApi']) == "ApiApis" || trim($item['nombreApi']) == "ApiMetodosApis" || trim($item['nombreApi']) == "ApiMetodosUsuariosApis" || trim($item['nombreApi']) == "ApiUsuariosAPI"){
													$nombreApitxt= trim($item['nombreApi']);
												}
												else{
													$nombreApitxt= trim($item['nombreApi']);
												}
												$boton="<a class='btn btn-primary' id='" . $cont ."' onclick='$(\"#formulario1\").attr(\"action\", \"#" . ($cont-1) ."\"); $(\"#txtBandera\").val(\"" . $nombreApitxt ." \"); $(\"#BanderaDiv\").val(\"" . $nombreApitxt ." \"); document.forms[\"formulario1\"].submit();'  href='javascript:;' >".$NombreAccion." </a>";
											}
											else{
												$boton = Html::a(' '.$NombreAccion.'', $url = ['site/descargaapis&f='.$_GET['f']], $options = ['class'=>'btn-shadow btn btn-primary mr-3 active']);
												//$boton="<a class='btn btn-primary' href='descargaapis.php' >Importar </a>";
											}
											//in_array(trim($nombreApitxt), $arrayMetodosPOSTPermitidos) and 
											if ((Yii::$app->session['TIPOMETODO']=="POST") or (Yii::$app->session['TIPOMETODO']<>"POST")) {

												$onclick="";
												echo        "<tr " . $style . " id='".trim($item['nombreApi'])."'>
														<td>{$cont}</td>
														<td align='center'><label><input type='checkbox' id='ck" . $cont . "' name='ck" . $cont . "' 
																					class='ck" . $cont . "' value='{$nombreApitxt}'></label></td>
														<td>{$nombreApitxt}<br />
														<label style='font-size:11px;'>{$item['direccionServidor']}</label>
														</td>
														<td>" . $boton . "</td>
														<td> " . $RESULTADO_BOTON."<br>";
														if(isset(Yii::$app->session['logMigracion'][$item['nombreApi']])){
															echo '<a href="#'.$cont.'" onclick="openModalReporte(\''.$item['nombreApi'].'\');">
															Ver Detalles
															</a>';
														}
														
												echo "	</td>
													</tr>";
												$contDatosMostrados++;
												$cont= $cont+1;
											}
										//}
									//}
											/*$item['aplicacionGrupoID']
											$item['nombreAplicacionGrupo']
											$item['aplicacionID']
											$item['nombreAplicacion']
											$item['direccionServidor']
											$item['nombreApi']}*/
								}
								echo     '
										</tbody>
										</table>';
							}
							catch (Exception $e){
								echo "<b>Sin Permisos para Importar/Exportar</b><BR />";
							}
							
						}else{
							if(isset($arraydatos['mensaje'])){
								echo "Mensaje: ".$arraydatos['mensaje'];
							}else{
								echo "No se realizo la consulta para descargar las apis";
							}

						}
					}

				}else{
					$mensaje= "Error al obtener los datos";
				}
			//}
			
			
				
				echo "<br>Total filas visibles:".$contDatosMostrados;
				Yii::$app->session->set('REGISTROS_VISIBLES', $contDatosMostrados);
				
			ActiveForm::end();
			?>
  
 	</div>
</div>


<div class="modal" id="reporteErroresMigracion">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Reporte de errores</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- Modal body -->
      <div class="modal-body" id="bodyReporteMigracionError">
		  Reportes de errores : <br>
      </div>
      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
      </div>

    </div>
  </div>
</div>
		
<script>
	function openModalReporte(api){
		console.log(api);
		$.ajax({
			url:"<?php echo Url::to(['site/geterrormg']); ?>",
			type: "GET",
			data:"api="+api,
			success: function(opciones){
				console.log(opciones);
				 $("#bodyReporteMigracionError").html(opciones);
			}
		})
		
		$('#reporteErroresMigracion').appendTo("body").modal('show');
	}
</script>	
