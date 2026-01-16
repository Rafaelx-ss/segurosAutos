<?php
//use Yii;
Yii::$app->session->set('RESULTADO_BOTON', "");

$mensajeCorto = "";
$mensajeUser = "";
$mensajeDesarrollo = "";
$cabeceraError = "";


$cabeceraError .= "usuarioApiID : ".Yii::$app->session['USUARIOSAPILISTA'];

try{
	
	//require 'lib/database.php';
	//echo $txtBanderaApi;
	require_once(dirname(__DIR__).'/clientemigracion/modelos/ModeloApiListado.php');
	//require 'modelos/ModeloApiMigracion.php';
	$file = 'Modelo'.trim($txtBanderaApi).'.php';
	$incluir= dirname(__DIR__).'/clientemigracion/modelos/Modelo'.trim($txtBanderaApi).'.php';
	
	
	if (file_exists($incluir)) {
		require $incluir;
        $database = new Database();
        $conn = $database->getConnection();            
		$listadoApi = new ListadoApi($database);
		
		//inicia listados api
		if(trim($txtBanderaApi) != "ListadoApi"){
				if (trim($txtBanderaApi) == "ApiApis"){
					$objeto="Apis";
				}
				elseif(trim($txtBanderaApi) == "ApiMetodosApis"){
					$objeto="MetodosApis";
				}
				elseif(trim($txtBanderaApi) == "ApiMetodosUsuariosApis"){
					$objeto="MetodosUsuariosApis";
				}
				elseif(trim($txtBanderaApi) == "ApiUsuariosAPI"){
					$objeto="UsuariosAPI";
				}
				elseif(trim($txtBanderaApi) == "ApiServidoresApis"){
					$objeto="ServidoresApis";
				}
				elseif(trim($txtBanderaApi) == "ApiConexionesApis"){
					$objeto="ConexionesApis";
				}
				elseif(trim($txtBanderaApi) == "ApiConexionesApisEstablecimientos"){
					$objeto="ConexionesApisEstablecimientos";
				}
				elseif (trim($txtBanderaApi) == "ApiApisGrupo"){
					$objeto="ApisGrupo";
				}
				elseif(trim($txtBanderaApi) == "ApiMetodosApisGrupo"){
					$objeto="MetodosApisGrupo";
				}
				elseif(trim($txtBanderaApi) == "ApiMetodosUsuariosApisGrupo"){
					$objeto="MetodosUsuariosApisGrupo";
				}
				elseif(trim($txtBanderaApi) == "ApiUsuariosAPIGrupo"){
					$objeto="UsuariosAPIGrupo";
				}
				elseif(trim($txtBanderaApi) == "ApiAplicacionesConexionApi"){
					$objeto="AplicacionesConexionApi";
				}
				elseif(trim($txtBanderaApi) == "ApiMigracionApisBonobo"){
					$objeto="MigracionApisBonobo";
				}
				else{
					$objeto= str_replace('Api','',trim($txtBanderaApi));
				}
				//return;
				$migracion = new $objeto($database);
		}
			
		$exito = false;
		$validaExistenciaApiLocal=false;
		try{
			if($listadoApi->ObtenerDatos($txtBanderaApi)){
				$validaExistenciaApiLocal = true;
			}
		}catch (Exception $e){
			$validaExistenciaApiLocal = false;
			$mensajeDesarrollo .= "obtener datos : ".$txtBanderaApi.": ".$listadoApi->ObtenerDatos($txtBanderaApi)."<br>";
		}
			
		//print_r("error:".$validaExistenciaApiLocal."|".$listadoApi->ObtenerDatos($txtBanderaApi));
		
		if($validaExistenciaApiLocal){
			//print_r("error de envio s:".$arraydatos['resultado']." m:".Yii::$app->session['TIPOMETODO']);
			$servidor = $listadoApi->Dataset['direccionServidor'];
			// $servidor = "http://10.128.5.230/ApiPapion";
			//echo $servidor = "http://localhost:8080/ApiCongo";
			//$servidor = "http://ws.brentec.mx/sites/ApiBonoboGalpuebla";
				
            $RutaApi = $listadoApi->Dataset['rutaApi'];
			$mensajeUser .= "<br /><b>Direccion Descarga:</b> ".$servidor.$RutaApi;
            require_once(dirname(__DIR__).'/clientemigracion/ApiHttpClient.php');
            ApiHttpClient::Init($servidor);
            ApiHttpClient::$Token = Yii::$app->session['Token'];
            ApiHttpClient::$MacAddress = Yii::$app->session['MacAddress'];
				
				//echo "AKI".$RutaApi."--".$objeto;
			$IdParametro=0;
			$IdGrupoParametro=0;
			$aplicacionID=0;
				
				//echo $Username=Yii::$app->session['Username'];
			if(trim($objeto) == "GruposEstablecimientos" and Yii::$app->session['TIPOSOLICITUD']=="G"){
				$IdParametro= Yii::$app->session['IDENTIFICADOR'];
				if($Username == "masterBrentec"){
					$IdParametro= 0;
				}
			}
			
			
			if(trim($objeto) == "Establecimientos" || trim($objeto) == "DireccionesEstablecimientos" || trim($objeto) == "AplicacionessGruposEstablecimientos" || trim($objeto) == "UsuariosAPI" || trim($objeto) == "MetodosUsuariosApis" || trim($objeto) == "ContactosEstablecimientos" || trim($objeto) == "TiposContactosEstablecimientos" || trim($objeto) == "ConexionesApisEstablecimientos" || trim($objeto) == "EstablecimientosConexionesBD"){
				$IdGrupoParametro= Yii::$app->session['IDENTIFICADOR'];
			}
				
			$mensajeUser .= "<br /><b>Identificador:</b> ";
			$mensajeUser .= Yii::$app->session['IDENTIFICADOR'];
				 
			$aplicacionID= Yii::$app->session['APLICACION_ID'];
				
                                  //echo "<br /><b>Inicio ConsumoAPI</b> ";
				
				//echo trim($objeto);
				
                $parametros = json_encode(Array(
                    "Token" => ApiHttpClient::$Token,
					"aplicacionID" => $aplicacionID,
                    "Id" => $IdParametro,
					"grupoID" => $IdGrupoParametro,
					"establecimientoID" => Yii::$app->session['IDENTIFICADOR'],
					"usuarioApiID" => Yii::$app->session['USUARIOSAPILISTA'],
					"TipoSolicitud" => Yii::$app->session['TIPOSOLICITUD'],
                    "MacAddress" => ApiHttpClient::$MacAddress
                )); 
                $datos = ApiHttpClient::ConsumeApi($RutaApi,'GET',$parametros);
                $arraydatos = json_decode($datos,true);
			
				//$RegistrosErrores .= $arraydatos['resultado'];
				
				if(isset($arraydatos['mensaje'])){
					 $mensajeUser .= "<br>Mensaje del api de conexion : <strong>".$arraydatos['mensaje']."</strong><br>";
				}
				$coleccion = "";
				if(isset($arraydatos['resultado']) or Yii::$app->session['TIPOMETODO']=="POST"){
					
					try{
						try{
							$coleccion = $arraydatos["datos"];
							//$mensajeGeterror .= "<br>coleccion de datos :".$coleccion;
                        }catch(Exception $ex){
                            $mensajeDesarrollo .= "<br /><b>Api no encontrada, revisar archivos fisicos y configuraciones:</b>". $ex->getMessage()."<br>";
                        }
						
						//inicia la coleccion de datos
						if($coleccion != "" or Yii::$app->session['TIPOMETODO']=="POST"){
							if(Yii::$app->session['TIPOMETODO']=="POST"){
								$coleccion = array("");
							}
							
							if(count($coleccion) > 0){
								//$mensajeGeterror="";
								$validaEjecucion= true;
								if(Yii::$app->session['TIPOMETODO']=="POST"){ 
									$dato1=0;
										
										try{
											
											$datosInsertados2 = $migracion->ObtenerDatos($dato1, "REGRESA_DATOS");
											// print_r($datosInsertados2);
											// echo "--".'P446';exit;
										}catch (Exception $e){
											$datosInsertados2 = array();
											$mensajeDesarrollo .= "<br>Error al obtener los datos : ".$e->getMessage()."<br>";
											// echo 55;exit;
										}
									
									// 	echo count($datosInsertados2)."<br /><br /><pre>";
									// print_r($datosInsertados2);
									// exit;
									try{
										$contadorReturn=0;
										$contadorExitos=0;
										$contadorErrores=0;
										//echo "<br /><b>Fin ObtenerDatos</b> ";
										
										if(count($datosInsertados2)>0){
											echo "<br /><label class='lblVerMensaje' style='cursor:pointer;' onclick='$(\".lblVerMensaje\").hide();$(\".lblOcultarMensaje\").show();$(\".divMensajeReturn\").show();' >Ver Resultado</label>
											<label class='lblOcultarMensaje' style='cursor:pointer; display:none;'onclick='$(\".lblVerMensaje\").show();$(\".lblOcultarMensaje\").hide();$(\".divMensajeReturn\").hide();' >Ocultar</label>
											<div id='divMensajeReturn' class='divMensajeReturn' style='display:none;'>";
											$mensajeUser .= "Total de filas consultadas: ".count($datosInsertados2)."<br />";
											$parametrosR = array();
											$temporalTextosIdiomas = array();
											$parametrosIdiomas = Array(
												"Token" => ApiHttpClient::$Token,
												"datosCliente" => null
											);
											foreach($datosInsertados2 as $item4){
												if($objeto=="Textos" or $objeto=="TextosBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"textoID" => $item4['textoID'],
														"tipoTexto" => $item4['tipoTexto'],
														"nombreTexto" => $item4['nombreTexto'],
														"activoTexto" => $item4['activoTexto'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}
												elseif($objeto=="Menus" or $objeto=="MenusBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"menuID" => $item4['menuID'],
														"nombreMenu" => $item4['nombreMenu'],
														"urlPagina" => $item4['urlPagina'],
														"imagen" => $item4['imagen'],
														"menuPadre" => $item4['menuPadre'],
														"orden" => $item4['orden'],
														"textoID" => $item4['textoID'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}
												elseif($objeto=="Catalogos" or $objeto=="CatalogosBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"catalogoID" => $item4['catalogoID'],
														"nombreCatalogo" => $item4['nombreCatalogo'],
														"nombreModelo" => $item4['nombreModelo'],
														"activoCatalogo" => $item4['activoCatalogo'],
														"sqlQuery" => $item4['sqlQuery'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}
												elseif($objeto=="Campos" or $objeto=="CamposBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"campoID" => $item4['campoID'],
														"nombreCampo" => $item4['nombreCampo'],
														"tipoControl" => $item4['tipoControl'],
														"longitud" => $item4['longitud'],
														"campoPK" => $item4['campoPK'],
														"campoFK" => $item4['campoFK'],
														"controlQuery" => $item4['controlQuery'],
														"visible" => $item4['visible'],
														"orden" => $item4['orden'],
														"tipoCampo" => $item4['tipoCampo'],
														"campoRequerido" => $item4['campoRequerido'],
														"textField" => $item4['textField'],
														"valueField" => $item4['valueField'],
														"valorDefault" => $item4['valorDefault'],
														"CSS" => $item4['CSS'],
														"catalogoID" => $item4['catalogoID'],
														"textoID" => $item4['textoID'],
														"catalogoReferenciaID" => $item4['catalogoReferenciaID'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}if($objeto=="CamposGrid" or $objeto=="CamposGridBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"campoGridID" => $item4['campoGridID'],
														"nombreCampo" => $item4['nombreCampo'],
														"visible" => $item4['visible'],
														"searchVisible" => $item4['searchVisible'],
														"orden" => $item4['orden'],
														"textoID" => $item4['textoID'],
														"tipoControl" => $item4['tipoControl'],
														"catalogoID" => $item4['catalogoID'],
														"catalogoReferenciaID" => $item4['catalogoReferenciaID'],
														"textField" => $item4['textField'],
														"valueField" => $item4['valueField'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"valorDefault" => $item4['valorDefault'],
														"controlQuery" => $item4['controlQuery'],
                                                        "searchQuery"=> $item4['searchQuery'],
                                                        "queryValor"=> $item4['queryValor'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}if($objeto=="ConfiguracionesSistema" or $objeto=="ConfiguracionesSistemaBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"configuracionesSistemaID" => $item4['configuracionesSistemaID'],
														"logoLogin" => $item4['logoLogin'],
														"logoBanner" => $item4['logoBanner'],
														"iconoMenu" => $item4['iconoMenu'],
														"temaBanner" => $item4['temaBanner'],
                                                                                                                "temaMenu" => $item4['temaMenu'],
														"temaContenido" => $item4['temaContenido'],
														"activoConfiguracionesSistema" => $item4['activoConfiguracionesSistema'],
                                                                                                                "versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
                                                        "logoFooter" => $item4['logoFooter'],
                                                        "favIcon" => $item4['favIcon'],
                                                        "titlePagina" => $item4['titlePagina'],
                                                        "footerPagina" => $item4['footerPagina'],
                                                        "btnAccion" => $item4['btnAccion'],
                                                        "btnSave" => $item4['btnSave'],
                                                        "btnMenu" => $item4['btnMenu'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
                                                    }
													if($objeto=="ConfiguracionesSlider" or $objeto=="ConfiguracionesSliderBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"configuracionesSliderID" => $item4['configuracionesSliderID'],
														"tituloSlider" => $item4['tituloSlider'],
														"contenidoSlider" => $item4['contenidoSlider'],
                                                        "imagenSlider" => $item4['imagenSlider'],
														"ordenSlider" => $item4['ordenSlider'],
														"activoConfiguracionesSlider" => $item4['activoConfiguracionesSlider'],
                                                        "versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}if($objeto=="CombosAnidados" or $objeto=="CombosAnidadosBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"comboAnidadoID" => $item4['comboAnidadoID'],
														"catalogoID" => $item4['catalogoID'],
														"campoIDPadre" => $item4['campoIDPadre'],
														"campoIDdependiente" => $item4['campoIDdependiente'],
														"controlQuery" => $item4['controlQuery'],
														"queryValue" => $item4['queryValue'],
														"queryText" => $item4['queryText'],
														"parametrosQuery" => $item4['parametrosQuery'],
														"versionRegistro" => $item4['versionRegistro'],
														"activoCombo" => $item4['activoCombo'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}if($objeto=="Formularios" or $objeto=="FormulariosBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"formularioID" => $item4['formularioID'],
														"tipoMenu" => $item4['tipoMenu'],
														"formID" => $item4['formID'],
														"nombreFormulario" => $item4['nombreFormulario'],
														"urlArchivo" => $item4['urlArchivo'],
														"estadoFormulario" => $item4['estadoFormulario'],
														"orden" => $item4['orden'],
														"icono" => $item4['icono'],
														"menuID" => $item4['menuID'],
														"aplicacionID" => $item4['aplicacionID'],
														"catalogoID" => $item4['catalogoID'],
														"textoID" => $item4['textoID'],
														"tipoFormularioID" => $item4['tipoFormularioID'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}if($objeto=="TextosIdiomas" or $objeto=="TextosIdiomasBonobo"){
													// if(!isset($parametrosIdiomas['datosCliente'])) {
													// 	$parametrosIdiomas['datosCliente'] = array();
													// }
													$temporalTextosIdiomas[] = array(
														"textoIdiomaID" => $item4['textoIdiomaID'],
														"texto" => $item4['texto'], 
														"textoID" => $item4['textoID'],
														"idiomaID" => $item4['idiomaID'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													);
													// $parametrosTextosIdionas = json_encode(Array(
													// 	"Token" => ApiHttpClient::$Token,
													// 	"textoIdiomaID" => $item4['textoIdiomaID'],
													// 	"texto" => $item4['texto'],
													// 	"textoID" => $item4['textoID'],
													// 	"idiomaID" => $item4['idiomaID'],
													// 	"versionRegistro" => $item4['versionRegistro'],
													// 	"regEstado" => $item4['regEstado'],
													// 	"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
													// 	"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
													// 	"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
													// 	"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
													// 	"MacAddress" => ApiHttpClient::$MacAddress
													// ));
												}if($objeto=="Acciones" or $objeto=="AccionesBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"accionID" => $item4['accionID'],
														"nombreAccion" => $item4['nombreAccion'],
														"imagen" => $item4['imagen'],
														"estadoAccion" => $item4['estadoAccion'],
														"textoID" => $item4['textoID'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}if($objeto=="AccionesFormularios" or $objeto=="AccionesFormulariosBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"accionFormularioID" => $item4['accionFormularioID'],
														"claveAccion" => $item4['claveAccion'],
														"estadoAccion" => $item4['estadoAccion'],
														"accionID" => $item4['accionID'],
														"formularioID" => $item4['formularioID'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}if($objeto=="PerfilAccionFormulario" or $objeto=="PerfilAccionFormularioBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"PerfilAccionFormularioID" => $item4['PerfilAccionFormularioID'],
														"perfilID" => $item4['perfilID'],
														"accionFormularioID" => $item4['accionFormularioID'],
														"activoPerfilAccionFormulario" => $item4['activoPerfilAccionFormulario'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}if($objeto=="PermisosMenus" or $objeto=="PermisosMenusBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"permisosMenusID" => $item4['permisosMenusID'],
														"perfilID" => $item4['perfilID'],
														"menuID" => $item4['menuID'],
														"orden" => $item4['orden'],
														"activoMenusFormularios" => $item4['activoMenusFormularios'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}if($objeto=="MenusFormularios" or $objeto=="MenusFormulariosBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"menusFormulariosID" => $item4['menusFormulariosID'],
														"formularioID" => $item4['formularioID'],
														"menuID" => $item4['menuID'],
														"ordenMenuFormulario" => $item4['ordenMenuFormulario'],
														"activoMenusForumlarios" => $item4['activoMenusForumlarios'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}if($objeto=="PermisosFormulariosPerfiles" or $objeto=="PermisosFormulariosPerfilesBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"permisoFormularioID" => $item4['permisoFormularioID'],
														"perfilID" => $item4['perfilID'],
														"formularioID" => $item4['formularioID'],
														"activoPermiso" => $item4['activoPermiso'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}if($objeto=="Perfiles" or $objeto=="PerfilesBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"perfilID" => $item4['perfilID'],
														"nombrePerfil" => $item4['nombrePerfil'],
														"activoPerfil" => $item4['activoPerfil'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}if($objeto=="PerfilesCompuestos" or $objeto=="PerfilesCompuestosBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"perfilCompuestoID" => $item4['perfilCompuestoID'],
														"usuarioID" => $item4['usuarioID'],
														"perfilID" => $item4['perfilID'],
														"activoPermiso" => $item4['activoPermiso'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}if($objeto=="Usuarios" or $objeto=="UsuariosBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"usuarioID" => $item4['usuarioID'],
														"nombreUsuario" => $item4['nombreUsuario'],
														"passw" => $item4['passw'],
														"usuario" => $item4['usuario'],
														"activoUsuario" => $item4['activoUsuario'],
														"correoUsuario" => $item4['correoUsuario'],
														"codigoRecuperacionPassw" => $item4['codigoRecuperacionPassw'],
														"fechaGeneracionCodigoRecuperacionPassw" => $item4['fechaGeneracionCodigoRecuperacionPassw'],
														"intentosValidos" => $item4['intentosValidos'],
														"AuthKey" => $item4['AuthKey'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}if($objeto=="Jornadas" or $objeto=="JornadasBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"jornadaID" => $item4['jornadaID'],
														"jornadaFecha" => $item4['jornadaFecha'],
														"jornadaFechaInicio" => $item4['jornadaFechaInicio'],
														"jornadaFechaFin" => $item4['jornadaFechaFin'],
														"jornadaFechaEjecucion" => $item4['jornadaFechaEjecucion'],
														"jornadaEstado" => $item4['jornadaEstado'],
														"jornadaTipoCorte" => $item4['jornadaTipoCorte'],
														"jornadaModoOperacion" => $item4['jornadaModoOperacion'],
														"turnoID" => $item4['turnoID'],
														"estadoReplica" => $item4['estadoReplica'],
														"establecimientoID" => $item4['establecimientoID'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}if($objeto=="Asistencias" or $objeto=="AsistenciasBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"asistenciaID" => $item4['asistenciaID'],
														"asistenciaFechaInicio" => $item4['asistenciaFechaInicio'],
														"asistenciaFechaFin" => $item4['asistenciaFechaFin'],
														"asistenciaEstado" => $item4['asistenciaEstado'],
														"bombaNumero" => $item4['bombaNumero'],
														"jornadaID" => $item4['jornadaID'],
														"empleadoID" => $item4['empleadoID'],
														"estadoReplica" => $item4['estadoReplica'],
														"establecimientoID" => $item4['establecimientoID'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}if($objeto=="VentasCombustibles" or $objeto=="VentasCombustiblesBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"ventaCombustibleID" => $item4['ventaCombustibleID'],
														"ventaCombustibleFolioInterfaz" => $item4['ventaCombustibleFolioInterfaz'],
														"ventaCombustibleFecha" => $item4['ventaCombustibleFecha'],
														"ventaCombustibleVolumen" => $item4['ventaCombustibleVolumen'],
														"ventaCombustibleImporte" => $item4['ventaCombustibleImporte'],
														"ventaCombustiblePrecioVenta" => $item4['ventaCombustiblePrecioVenta'],
														"ventaCombustibleIVAVenta" => $item4['ventaCombustibleIVAVenta'],
														"ventaCombustibleFactorIVA" => $item4['ventaCombustibleFactorIVA'],
														"ventaCombustibleIEPSVenta" => $item4['ventaCombustibleIEPSVenta'],
														"ventaCombustibleFactorIEPS" => $item4['ventaCombustibleFactorIEPS'],
														"ventaCombustibleLecturaElectronica" => $item4['ventaCombustibleLecturaElectronica'],
														"ventaCombustibleEstado" => $item4['ventaCombustibleEstado'],
														"ventaCombustibleRemisionado" => $item4['ventaCombustibleRemisionado'],
														"tipoDespachoID" => $item4['tipoDespachoID'],
														"facturado" => $item4['facturado'],
														"facturado2" => $item4['facturado2'],
														"facturado3" => $item4['facturado3'],
														"uuid" => $item4['uuid'],
														"serie" => $item4['serie'],
														"folio" => $item4['folio'],
														"empleadoID" => $item4['empleadoID'],
														"asistenciaID" => $item4['asistenciaID'],
														"jornadaID" => $item4['jornadaID'],
														"mangueraNumero" => $item4['mangueraNumero'],
														"bombaNumero" => $item4['bombaNumero'],
														"establecimientoID" => $item4['establecimientoID'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"fechahorainicio" => $item4['fechahorainicio'],
														"fechahorafin" => $item4['fechahorafin'],
														"volumenInicial" => $item4['volumenInicial'],
														"volumenFinal" => $item4['volumenFinal'],
														"temperatura" => $item4['temperatura'],
														"presionAbsoluta" => $item4['presionAbsoluta'],
														"productoID" => $item4['productoID'],
														"tanqueNumero" => $item4['tanqueNumero'],
														"estadoReplica" => $item4['estadoReplica'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}if($objeto=="VentasFormasDePago" or $objeto=="VentasFormasDePagoBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"ventaFormaDePagoID" => $item4['ventaFormaDePagoID'],
														"ventaCombustibleID" => $item4['ventaCombustibleID'],
														"ventaProductoID" => $item4['ventaProductoID'],
														"establecimientoID" => $item4['establecimientoID'],
														"formaPagoID" => $item4['formaPagoID'],
														"tipoMovimientoID" => $item4['tipoMovimientoID'],
														"importeFormaPago" => $item4['importeFormaPago'],
														"subTotal" => $item4['subTotal'],
														"cantidadProducto" => $item4['cantidadProducto'],
														"tipoFacturacion" => $item4['tipoFacturacion'],
														"estadoReplica" => $item4['estadoReplica'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}if($objeto=="VentasProductos" or $objeto=="VentasProductosBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"ventaProductoID" => $item4['ventaProductoID'],
														"almacenID" => $item4['almacenID'],
														"establecimientoID" => $item4['establecimientoID'],
														"ventaNumero" => $item4['ventaNumero'],
														"ventaFecha" => $item4['ventaFecha'],
														"ventaSubTotal" => $item4['ventaSubTotal'],
														"ventaIVA" => $item4['ventaIVA'],
														"ventaTotal" => $item4['ventaTotal'],
														"SalidaSubTotalC" => $item4['SalidaSubTotalC'],
														"SalidaIVAC" => $item4['SalidaIVAC'],
														"SalidaTotalC" => $item4['SalidaTotalC'],
														"tipoMovimientoID" => $item4['tipoMovimientoID'],
														"uuid" => $item4['uuid'],
														"serie" => $item4['serie'],
														"folio" => $item4['folio'],
														"facturado" => $item4['facturado'],
														"facturado2" => $item4['facturado2'],
														"facturado3" => $item4['facturado3'],
														"tipoFacturado" => $item4['tipoFacturado'],
														"almacenTraspasoID" => $item4['almacenTraspasoID'],
														"aperturaBombaID" => $item4['aperturaBombaID'],
														"bombaNumero" => $item4['bombaNumero'],
														"empleadoID" => $item4['empleadoID'],
														"asistenciaID" => $item4['asistenciaID'],
														"jornadaID" => $item4['jornadaID'],
														"venta_Status" => $item4['venta_Status'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"estadoReplica" => $item4['estadoReplica'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}if($objeto=="VentasDetallesImpuestos" or $objeto=="VentasDetallesImpuestosBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"ventaDetalleImpuestoID" => $item4['ventaDetalleImpuestoID'],
														"ventaFormaDePagoID" => $item4['ventaFormaDePagoID'],
														"tipoImpuestoID" => $item4['tipoImpuestoID'],
														"clasificacion" => $item4['clasificacion'],
														"factor" => $item4['factor'],
														"valorImpuesto" => $item4['valorImpuesto'],
														"importeImpuesto" => $item4['importeImpuesto'],
														"estadoReplica" => $item4['estadoReplica'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}if($objeto=="TemplatesReportes" or $objeto=="TemplatesReportesBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"templateReporteID" => $item4['templateReporteID'],
														"nombreTemplateReporte" => $item4['nombreTemplateReporte'],
														"logoTemplateReporte" => $item4['logoTemplateReporte'],
														"encabezadoTemplateReporte" => $item4['encabezadoTemplateReporte'],
														"pieTemplateReporteL1" => $item4['pieTemplateReporteL1'],
														"pieTemplateReporteL2" => $item4['pieTemplateReporteL2'],
														"pieTemplateReporteL3" => $item4['pieTemplateReporteL3'],
														"colorLinea" => $item4['colorLinea'],
														"colorTituloTabla" => $item4['colorTituloTabla'],
														"colorTituloTexto" => $item4['colorTituloTexto'],
														"colorTextoFooter" => $item4['colorTextoFooter'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}if($objeto=="ReportesConfiguraciones" or $objeto=="ReportesConfiguracionesBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"reporteConfiguracionID" => $item4['reporteConfiguracionID'],
														"templateReporteID" => $item4['templateReporteID'],
														"nombreReporte" => $item4['nombreReporte'],
														"queryReporte" => $item4['queryReporte'],
														"columnasReporte" => $item4['columnasReporte'],
														"imprimirLogoPdf" => $item4['imprimirLogoPdf'],
														"imprimirEncabezado" => $item4['imprimirEncabezado'],
														"imprimirFechaHora" => $item4['imprimirFechaHora'],
														"imprimirNombreUsuario" => $item4['imprimirNombreUsuario'],
														"imprimirLogoExcel" => $item4['imprimirLogoExcel'],
														"imprimirPie" => $item4['imprimirPie'],
														"imprimirEncabezadoExcel" => $item4['imprimirEncabezadoExcel'],
														"imprimirFechaHoraExcel" => $item4['imprimirFechaHoraExcel'],
														"imprimirNombreUsuarioExcel" => $item4['imprimirNombreUsuarioExcel'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
                                                                                                                "orientacionPagina" => $item4['orientacionPagina'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}if($objeto=="ReportesCampos" or $objeto=="ReportesCamposBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"reporteCampoID" => $item4['reporteCampoID'],
														"reporteConfiguracionID" => $item4['reporteConfiguracionID'],
														"nombreCampo" => $item4['nombreCampo'],
														"aliasTabla" => $item4['aliasTabla'],
														"visible" => $item4['visible'],
														"searchVisible" => $item4['searchVisible'],
														"orden" => $item4['orden'],
														"textoID" => $item4['textoID'],
														"tipoControl" => $item4['tipoControl'],
														"controlQuery" => $item4['controlQuery'],
														"queryValor" => $item4['queryValor'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
                                                                                                                "sumarCampo" => $item4['sumarCampo'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}if($objeto=="Apis" or $objeto=="ApisBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"apiID" => $item4['apiID'],
														"nombreApi" => $item4['nombreApi'],
														"estadoApi" => $item4['estadoApi'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"rutaApi" => $item4['rutaApi'],
														"aplicacionID" => $item4['aplicacionID'],
														"ordenMigracion" => $item4['ordenMigracion'],
														"tipoLista" => $item4['tipoLista'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}if($objeto=="Scripts" or $objeto=="ScriptsBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"scriptID" => $item4['scriptID'],
														"aplicacionID" => $item4['aplicacionID'],
														"version" => $item4['version'],
														"descripcion" => $item4['descripcion'],
														"fechaInicio" => $item4['fechaInicio'],
														"fechaFin" => $item4['fechaFin'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}if($objeto=="DetalleScripts"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"detalleScriptID" => $item4['detalleScriptID'],
														"scriptID" => $item4['scriptID'],
														"orden" => $item4['orden'],
														"texto" => $item4['texto'],
														"numeroIntentos" => $item4['numeroIntentos'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}if($objeto=="MigracionReportes" or $objeto=="MigracionReportesBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"reporteConfiguracionID" => $item4['reporteConfiguracionID'],
														"aplicacionID" => $item4['aplicacionID'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}if($objeto=="MigracionCatalogos" or $objeto=="MigracionCatalogosBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"catalogoID" => $item4['catalogoID'],
														"aplicacionID" => $item4['aplicacionID'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}if($objeto=="MigracionFormularios" or $objeto=="MigracionFormulariosBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"formularioID" => $item4['formularioID'],
														"aplicacionID" => $item4['aplicacionID'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}if($objeto=="MigracionMenus" or $objeto=="MigracionMenusBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"menuID" => $item4['menuID'],
														"aplicacionID" => $item4['aplicacionID'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}if($objeto=="Versiones" or $objeto=="VersionesBonobo"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"versionID" => $item4['versionID'],
														"version" => $item4['version'],
														"fechaLiberacionVersion" => $item4['fechaLiberacionVersion'],
														"aliasVersion" => $item4['aliasVersion'],
														"urlVersion" => $item4['urlVersion'],
														"urlDocumentacionVersion" => $item4['urlDocumentacionVersion'],
														"versionActual" => $item4['versionActual'],
														"aplicacionID" => $item4['aplicacionID'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}
												


												if($objeto=="ConfiguracionLiquidacion"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"configuracionLiquidacionID" => $item4['configuracionLiquidacionID'],
														"activaTiempoLiquidacion" => $item4['activaTiempoLiquidacion'],
														"cantidadJornadasReporte" => $item4['cantidadJornadasReporte'],
														"configuracionReporteLiquidacionID" => $item4['configuracionReporteLiquidacionID'],
														"imprimeLogEventosLiquidacion" => $item4['imprimeLogEventosLiquidacion'],
														"tiempoLiquidacion" => $item4['tiempoLiquidacion'],
														"tolerancialiquidacion" => $item4['tolerancialiquidacion'],
														"verDescripcionJornada" => $item4['verDescripcionJornada'],
														"establecimientoID" => $item4['establecimientoID'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}

																								
																																				
											
												if($objeto=="ConfiguracionBombasGenerales"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"bombaNumero" => $item4['bombaNumero'],
														"digitosPrecio" => $item4['digitosPrecio'],
														"digitosImporte" => $item4['digitosImporte'],
														"digitosVolumen" => $item4['digitosVolumen'],
														"interfazID" => $item4['interfazID'],
														"tipoMedidaBomba" => $item4['tipoMedidaBomba'],
														"establecimientoID" => $item4['establecimientoID'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}
												
												
											
												
												
												if($objeto=="MensajesBitacora"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"mensajeBitacoraID" => $item4['mensajeBitacoraID'],
														"nombreMensaje" => $item4['nombreMensaje'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}
												
												if($objeto=="MensajesBitacoraIdiomas"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"mensajeBitacoraIdiomasID" => $item4['mensajeBitacoraIdiomasID'],
														"mensaje" => $item4['mensaje'],
														"mensajeBitacoraID" => $item4['mensajeBitacoraID'],
														"idiomaID" => $item4['idiomaID'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}
												
												if($objeto=="VelocidadBaudios"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"paridadID" => $item4['paridadID'],
														"nombre" => $item4['nombre'],
														"valor" => $item4['valor'],
														"descripcion" => $item4['descripcion'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}
												
												if($objeto=="ProtocolosSeguridadTLS"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"protocoloSeguridadTSLID" => $item4['protocoloSeguridadTSLID'],
														"protocolo" => $item4['protocolo'],
														"valprotocoloDescripcionor" => $item4['valprotocoloDescripcionor'],
														"estadoReplica" => $item4['estadoReplica'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}
												
												if($objeto=="TiposConsola"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"tipoConsolaID" => $item4['tipoConsolaID'],
														"nombreTipoConsola" => $item4['nombreTipoConsola'],
														"estadoReplica" => $item4['estadoReplica'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}
												
												if($objeto=="ConfiguracionReporteLiquidacion"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"configuracionReporteLiquidacionID" => $item4['configuracionReporteLiquidacionID'],
														"nombreConfiguracionReporte" => $item4['nombreConfiguracionReporte'],
														"estadoReplica" => $item4['estadoReplica'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}
												
																								
												
												if($objeto=="ConfiguracionTickets"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"configuracionTicketID" => $item4['configuracionTicketID'],
														"mensajeEncabezado" => $item4['mensajeEncabezado'],
														"imprimirTransaccionFlotillas" => $item4['imprimirTransaccionFlotillas'],
														"imprimirSaldo" => $item4['imprimirSaldo'],
														"imprimirClave" => $item4['imprimirClave'],
														"imprimirVehiculo" => $item4['imprimirVehiculo'],
														"imprimirRazonSocial" => $item4['imprimirRazonSocial'],
														"imprimirPortador" => $item4['imprimirPortador'],
														"imprimirOdometro" => $item4['imprimirOdometro'],
														"imprimirCuentaDescripcion" => $item4['imprimirCuentaDescripcion'],
														"imprimirCuentaNumero" => $item4['imprimirCuentaNumero'],
														"imprimirVehiculoMarca" => $item4['imprimirVehiculoMarca'],
														"imprimirVehiculoModelo" => $item4['imprimirVehiculoModelo'],
														"imprimirCalendarioTarjeta" => $item4['imprimirCalendarioTarjeta'],
														"imprimirVehiculoID" => $item4['imprimirVehiculoID'],
														"imprimirVehiculoFlota" => $item4['imprimirVehiculoFlota'],
														"imprimirVehiculoCalendario" => $item4['imprimirVehiculoCalendario'],
														"imprimirVehiculoClase" => $item4['imprimirVehiculoClase'],
														"imprimirVehiculoColor" => $item4['imprimirVehiculoColor'],
														"imprimirVehiculoPlaca" => $item4['imprimirVehiculoPlaca'],
														"imprimirVehiculoMotor" => $item4['imprimirVehiculoMotor'],
														"imprimirVehiculoSerie" => $item4['imprimirVehiculoSerie'],
														"imprimirTarjeta" => $item4['imprimirTarjeta'],
														"imprimirRendimiento" => $item4['imprimirRendimiento'],
														"imprimirRuta" => $item4['imprimirRuta'],
														"imprimeCodigoBarras" => $item4['imprimeCodigoBarras'],
														"mensajePOS1" => $item4['mensajePOS1'],
														"mensajePOS2" => $item4['mensajePOS2'],
														"transaccion" => $item4['transaccion'],
														"imprimePagare" => $item4['imprimePagare'],
														"imprimeFirma" => $item4['imprimeFirma'],
														"imprimeRazonEstacion" => $item4['imprimeRazonEstacion'],
														"imprimeSiic" => $item4['imprimeSiic'],
														"imprimerClientePemex" => $item4['imprimerClientePemex'],
														"imprimeRFC" => $item4['imprimeRFC'],
														"imprimeEstacion" => $item4['imprimeEstacion'],
														"imprimeDireccionfiscal" => $item4['imprimeDireccionfiscal'],
														"imprimeLugar" => $item4['imprimeLugar'],
														"imprimeRegimen" => $item4['imprimeRegimen'],
														"imprimeAdicionalesAutoconsumo" => $item4['imprimeAdicionalesAutoconsumo'],
														"imprimeViaje" => $item4['imprimeViaje'],
														"imprimeCartaPorte" => $item4['imprimeCartaPorte'],
														"imprimeOdometroAnterior" => $item4['imprimeOdometroAnterior'],
														"imprimeFleje1" => $item4['imprimeFleje1'],
														"imprimeFleje2" => $item4['imprimeFleje2'],
														"imprimeFleje3" => $item4['imprimeFleje3'],
														"imprimeFuel" => $item4['imprimeFuel'],
														"imprimeFuelEconomy" => $item4['imprimeFuelEconomy'],
														"imprimeIdleFuel" => $item4['imprimeIdleFuel'],
														"imprimeIdleTime" => $item4['imprimeIdleTime'],
														"imprimeIdleTimePorcentaje" => $item4['imprimeIdleTimePorcentaje'],
														"imprimeDriving" => $item4['imprimeDriving'],
														"imprimeTotalEngineHours" => $item4['imprimeTotalEngineHours'],
														"imprimeProductoTransportado" => $item4['imprimeProductoTransportado'],
														"soloCodigoFacturacion" => $item4['soloCodigoFacturacion'],
														"tipoFlotillas" => $item4['tipoFlotillas'],
														"serieFactura" => $item4['serieFactura'],
														"mesNotasFacturacion" => $item4['mesNotasFacturacion'],
														"formatoFactura" => $item4['formatoFactura'],
														"muestraTransaccion" => $item4['muestraTransaccion'],
														"muestraTransaccionVales" => $item4['muestraTransaccionVales'],
														"muestraClaveSatComb" => $item4['muestraClaveSatComb'],
														"establecimientoID" => $item4['establecimientoID'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}
												if($objeto=="ConfiguracionTanques"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"configuracionLecturasTanquesID" => $item4['configuracionLecturasTanquesID'],
														"puertoConexion" => $item4['puertoConexion'],
														"bitParada" => $item4['bitParada'],
														"velocidadBaudiosID" => $item4['velocidadBaudiosID'],
														"paridadID" => $item4['paridadID'],
														"longitudBits" => $item4['longitudBits'],
														"intervaloConsultas" => $item4['intervaloConsultas'],
														"tipoComunicacion" => $item4['tipoComunicacion'],
														"guardaDescargas" => $item4['guardaDescargas'],
														"cantidadDescargasPantalla" => $item4['cantidadDescargasPantalla'],
														"guardaLogs" => $item4['guardaLogs'],
														"rutaDBTEAM" => $item4['rutaDBTEAM'],
														"usuarioDBTEAM" => $item4['usuarioDBTEAM'],
														"PassDBTEAM" => $item4['PassDBTEAM'],
														"nombreBDTEAM" => $item4['nombreBDTEAM'],
														"passINCON" => $item4['passINCON'],
														"rutaINCON" => $item4['rutaINCON'],
														"intervaloEnvioFlotillas" => $item4['intervaloEnvioFlotillas'],
														"horasPermitidas" => $item4['horasPermitidas'],
														"usaCV" => $item4['usaCV'],
														"valorCV" => $item4['valorCV'],
														"establecimientoID" => $item4['establecimientoID'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}
												if($objeto=="ImpresorasSpooler"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"spoolerID" => $item4['spoolerID'],
														"impresoraID" => $item4['impresoraID'],
														"ignoraFacturas" => $item4['ignoraFacturas'],
														"tipoImpresion" => $item4['tipoImpresion'],
														"nombrePuertoLinea" => $item4['nombrePuertoLinea'],
														"numeroImpresora" => $item4['numeroImpresora'],
														"establecimientoID" => $item4['establecimientoID'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}
												if($objeto=="ConfiguracionesConsola"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"configuracionConsolaID" => $item4['configuracionConsolaID'],
														"rutaRecursosBombas" => $item4['rutaRecursosBombas'],
														"rutaRecursosTanques" => $item4['rutaRecursosTanques'],
														"preAutorizacion" => $item4['preAutorizacion'],
														"actualizaPreset" => $item4['actualizaPreset'],
														"cancelaPresetDespacho" => $item4['cancelaPresetDespacho'],
														"maximaDiferencia" => $item4['maximaDiferencia'],
														"retrasoInicio" => $item4['retrasoInicio'],
														"intentosAutoriza" => $item4['intentosAutoriza'],
														"cantidadDespachosPantalla" => $item4['cantidadDespachosPantalla'],
														"forzarPrecio" => $item4['forzarPrecio'],
														"establecimientoID" => $item4['establecimientoID'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}


												if($objeto=="ConfiguracionesCompras"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"configuracionCompraID" => $item4['configuracionCompraID'],
														"validaFolioAlfaNumerico" => $item4['validaFolioAlfaNumerico'],
														"calculaPrecioCompra" => $item4['calculaPrecioCompra'],
														"cargasRemotas" => $item4['cargasRemotas'],
														"establecimientoID" => $item4['establecimientoID'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}

												if($objeto=="ConfiguracionesReclasificacion"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"configuracionReclasificacionID" => $item4['configuracionReclasificacionID'],
														"validarPermisosReclasificacion" => $item4['validarPermisosReclasificacion'],
														"configuracionTLSUR" => $item4['configuracionTLSUR'],
														"validaEstacionAutorizada" => $item4['validaEstacionAutorizada'],
														"configuracionTLSUR" => $item4['configuracionTLSUR'],
														"establecimientoID" => $item4['establecimientoID'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}

												if($objeto=="configuracionesfacturacion"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"configuracionFacturacionID" => $item4['configuracionFacturacionID'],
														"digitosCantidad" => $item4['digitosCantidad'],
														"exentoIva" => $item4['exentoIva'],
														"complementoINE" => $item4['complementoINE'],
														"muestraIEPS" => $item4['muestraIEPS'],
														"porcentajeIVA" => $item4['porcentajeIVA'],
														"urlWebservicesFacturacion" => $item4['urlWebservicesFacturacion'],
														"establecimientoID" => $item4['establecimientoID'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}

												

												if($objeto=="ConfiguracionesCortes"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"bombasSinBentas" => $item4['bombasSinBentas'],
														"tipoCorte" => $item4['tipoCorte'],
														"impresionCorteTermicaDirecto" => $item4['impresionCorteTermicaDirecto'],
														"establecimientoID" => $item4['establecimientoID'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}
												
												if($objeto=="MetodosApis"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"metodoApiID" => $item4['metodoApiID'],
														"apiID" => $item4['apiID'],
														"estadoMetodo" => $item4['estadoMetodo'],
														"versionRegistro" => $item4['versionRegistro'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"tipoMetodoApi" => $item4['tipoMetodoApi'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}
												
												if($objeto=="ConfiguracionAplicacion"){
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"configuracionAplicacionID" => $item4['configuracionAplicacionID'],
														"modoDescarga" => $item4['modoDescarga'],
														"muestraBotonExportaVentasEinventarios" => $item4['muestraBotonExportaVentasEinventarios'],
														"muestraEncabezado" => $item4['muestraEncabezado'],
														"passwordCerti" => $item4['passwordCerti'],
														"permisoReenvio" => $item4['permisoReenvio'],
														"porFormaPago" => $item4['porFormaPago'],
														"timerOutwsFllet" => $item4['timerOutwsFllet'],
														"timerReenvioFlotillas" => $item4['timerReenvioFlotillas'],
														"urlWerbservicesMobileGas" => $item4['urlWerbservicesMobileGas'],
														"protocoloSeguridadTSLID" => $item4['protocoloSeguridadTSLID'],
														"urlWebservicesMobileFleet" => $item4['urlWebservicesMobileFleet'],
														"imprimeIvaTicket" => $item4['imprimeIvaTicket'],
														"tipoConsolaID" => $item4['tipoConsolaID'],
														"tipoConexion" => $item4['tipoConexion'],
														"rutaWebSocket" => $item4['rutaWebSocket'],
														"vtb" => $item4['vtb'],
														"grupoPolizaID" => $item4['grupoPolizaID'],
														"timerEstacionServicio" => $item4['timerEstacionServicio'],
														"precioEntradas" => $item4['precioEntradas'],
														"activaTipoReportetotalesInvVentas" => $item4['activaTipoReportetotalesInvVentas'],
														"reportePorJornadaYTurno" => $item4['reportePorJornadaYTurno'],
														"ControlInventarios" => $item4['ControlInventarios'],
														"numeroMesesDepuracion" => $item4['numeroMesesDepuracion'],
														"valesCreditoLocal" => $item4['valesCreditoLocal'],
														"vtt" => $item4['vtt'],
														"clco" => $item4['clco'],
														"tipoValidacionIdentificadores" => $item4['tipoValidacionIdentificadores'],
														"validaTarjetaBomba" => $item4['validaTarjetaBomba'],
														"imprimirPOS" => $item4['imprimirPOS'],
														"jornadaActiva" => $item4['jornadaActiva'],
														"preAuth" => $item4['preAuth'],
														"enviaTicketCentral" => $item4['enviaTicketCentral'],
														"urlNeoFact" => $item4['urlNeoFact'],
														"siic" => $item4['siic'],
														"codigoIgnora" => $item4['codigoIgnora'],
														"establecimientoID" => $item4['establecimientoID'],
														"regEstado" => $item4['regEstado'],
														"regFechaUltimaModificacion" => $item4['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item4['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item4['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item4['regVersionUltimaModificacion'],
														"versionRegistro" => $item4['versionRegistro'],
														"MacAddress" => ApiHttpClient::$MacAddress
													));
												}

												if($objeto != "TextosIdiomas" and $objeto != "TextosIdiomasBonobo"){

													
													$datosReturn = ApiHttpClient::ConsumeApi($RutaApi,'POST',$parametrosR);
														
													$arraydatosReturn = json_decode($datosReturn,true);
																									
																									//echo "<br /><b>Resultado Retorno</b> ".$arraydatosReturn['mensaje'];
																									
													//echo $arraydatosReturn['mensaje']."";//$item4['nombreTexto']."<br />";
													if($arraydatosReturn['mensaje']<>""){
														//ECHO "<BR />Fila: ". $contadorReturn ." ".$arraydatosReturn['mensaje'];
														$colorEvento = "naranja";
														//$mensajeCorto = "<BR />".$arraydatosReturn['mensaje'];
														//$mensajeUser .= "<BR />".$arraydatosReturn['mensaje']."<br>";
													}
													if($arraydatosReturn['resultado'] and strtoupper($arraydatosReturn['mensaje'])=='DATOS CREADOS'){
														$contadorExitos++;
													}else{
														$contadorErrores++;
														//aqui hay un echo $ErrorDetenerCiclo .=
														$mensajeUser .= "<BR />Fila Error: ". $contadorReturn ." ". $arraydatosReturn['mensaje']."<br />".$parametrosR."<br />";
													}
													//echo $parametrosR;
													$contadorReturn++;
													if(trim($arraydatosReturn['mensaje'])=="Acceso a metodo no permitido"){
														
														$mensajeUser .="<br />Se detuvo el ciclo por que no se cuenta con permisos para el meétodo solicitado";
														break;
													}
													if(trim($arraydatosReturn['mensaje'])=="método POST no implementado"){	
														//aqui hay un echo $ErrorDetenerCiclo .=
														$mensajeUser .="<br />Se detuvo el ciclo por que el método POST no esta Implementado para el API seleccionado";			
														
														break;
													}
													
													if(isset($arraydatosReturn['desarrollo'])){
														$mensajeDesarrollo = "<br>".$arraydatosReturn['desarrollo']."<br>";
													}
														
												}

												
												
												//if ($contadorReturn==10) break;
											}
											$parametrosIdiomas['datosCliente'] = $temporalTextosIdiomas;
											// print_r(json_encode($parametrosIdiomas));
											// echo $RutaApi;
											if($objeto=="TextosIdiomas" or $objeto=="TextosIdiomasBonobo"){
												$datosReturn = ApiHttpClient::ConsumeApi($RutaApi,'POST', json_encode($parametrosIdiomas));
														
												$arraydatosReturn = json_decode($datosReturn,true);
																								
																								//echo "<br /><b>Resultado Retorno</b> ".$arraydatosReturn['mensaje'];
																								
												//echo $arraydatosReturn['mensaje']."";//$item4['nombreTexto']."<br />";
												if($arraydatosReturn['mensaje']<>""){
													//ECHO "<BR />Fila: ". $contadorReturn ." ".$arraydatosReturn['mensaje'];
													$colorEvento = "naranja";
													//$mensajeCorto = "<BR />".$arraydatosReturn['mensaje'];
													//$mensajeUser .= "<BR />".$arraydatosReturn['mensaje']."<br>";
												}
												if($arraydatosReturn['resultado'] and strtoupper($arraydatosReturn['mensaje'])=='DATOS CREADOS'){
													$contadorExitos++;
												}else{
													$contadorErrores++;
													//aqui hay un echo $ErrorDetenerCiclo .=
													$mensajeUser .= "<BR />Fila Error: ". $contadorReturn ." ". $arraydatosReturn['mensaje']."<br />".$parametrosR."<br />";
												}
												//echo $parametrosR;
												$contadorReturn++;
												if(trim($arraydatosReturn['mensaje'])=="Acceso a metodo no permitido"){
													
													$mensajeUser .="<br />Se detuvo el ciclo por que no se cuenta con permisos para el meétodo solicitado";
													
												}
												if(trim($arraydatosReturn['mensaje'])=="método POST no implementado"){	
													//aqui hay un echo $ErrorDetenerCiclo .=
													$mensajeUser .="<br />Se detuvo el ciclo por que el método POST no esta Implementado para el API seleccionado";			
													
												}
												
												if(isset($arraydatosReturn['desarrollo'])){
													$mensajeDesarrollo = "<br>".$arraydatosReturn['desarrollo']."<br>";
												}

											}
											echo "</div>";
										}
										else{
											echo "La consulta local no trajo resultados";
											$colorEvento = "naranja";
											$mensajeCorto = "La consulta local no trajo resultados<br>";
											$mensajeUser .= "La consulta local no trajo resultados <br>";
										}
										if($validaEjecucion){
											$colorEvento = "verde";
											echo $mensajeUser .= "</br ><b>Se guardaron " . $contadorExitos . " registros de " . count($datosInsertados2)."</b><br />Registros erroneos: " . $contadorErrores;
											$mensajeCorto =  "</br ><b>Se guardaron " . $contadorExitos . " registros de " . count($datosInsertados2)."<br>";
											
										}else{
											$colorEvento = "rojo";
											echo "La ejecución del api no se realizó correctamente";
											$mensajeCorto = "La ejecución del api no se realizó correctamente<br>";
											$mensajeUser .= "La ejecución del api no se realizó correctamente<br>";
										}
										
									}catch (Exception $e){
										$colorEvento = "rojo";
										//echo "Error al consultar datos locales, SIN registros para enviar a método POST: ".$e->getMessage();
										$mensajeCorto = "Ocurrio un error en el api destino";
										$mensajeUser = "Error al enviar a método POST<br>";
										$mensajeDesarrollo .= "Error al enviar a método POST: <br />".$e->getMessage()."<br>";
										print_r($e->getMessage());
										//Yii::$app->session->set('RESULTADO_BOTON', "<br />Error al consultar datos locales para enviar a método POST: <br />".$e->getMessage());
									}
									$exito=true;
								}elseif(trim($objeto) == "EjecucionScripts"){
									$aregloID="";
									$validaEjecucion = $migracion->EjecutaScripts($coleccion, Yii::$app->session['IDENTIFICADOR'], $aregloID);
									//echo "AKI".$aregloID."AKI";
									if($validaEjecucion){
										//$datos = ApiHttpClient::ConsumeApi($RutaApi,'PUT',$parametros);
										try{
											$fechaInicioMigracion="";
											$AplicacionMigracion=0;
											$usuarioMigracion=0;
												$inIDscript="";
												$inIDDetalleScript="";
												$contador=0;
												$contadorExitos2=0;
												$contadorErrores2=0;
											foreach($coleccion as $item2){
												$usuarioMigracion=$item2['usuarioApiID'];
												$AplicacionMigracion= 2;
												$fechaInicioMigracion= $item2['fechaInicio'];
												//break;
											//}
												$dato1=0;
												
												try{
													$datosInsertados= $migracion->ObtenerDatosLog($dato1, $item2['detalleScriptID'], $fechaInicioMigracion);
												}catch(Exception $e){
													$datosInsertados = array();
													$mensajeDesarrollo = "<br>Error: ".$e->getMessage();
												}
												
												$EjecucionCorrecta= false;
												/*try{
													$EjecucionCorrecta= true;
													count($datosInsertados);
												}
												catch (Exception $e){
													$EjecucionCorrecta= false;
												}*/
												foreach($datosInsertados as $item3){
												//foreach($coleccion as $item3){
													if($contador>0){
														$inIDscript .= ",".$item3['scriptID'];
														$inIDDetalleScript .= ",".$item3['detalleScriptID'];
													}else{
														$inIDscript .=  $item3['scriptID'];
														$inIDDetalleScript .= $item3['detalleScriptID'];
													}
													
													$parametros = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
														"usuarioApiID" => $item2['usuarioApiID'],
														"scriptID" => $item2['scriptID'],
														"detalleScriptID" => $item3['detalleScriptID'],
														"fechaEjecucion" => $fechaInicioMigracion,
														"estadoEjecucion" => $item3['estadoEjecucion'],
														"resultado" => str_replace("'","\'",$item3['resultado']),
														"versionRegistro" => $item2['versionRegistro'],
														"regEstado" => $item2['regEstado'],
														"regFechaUltimaModificacion" => $item2['regFechaUltimaModificacion'],
														"regUsuarioUltimaModificacion" => $item2['regUsuarioUltimaModificacion'],
														"regFormularioUltimaModificacion" => $item2['regFormularioUltimaModificacion'],
														"regVersionUltimaModificacion" => $item2['regVersionUltimaModificacion'],
														"MacAddress" => ApiHttpClient::$MacAddress
													)); 
													$datos3 = ApiHttpClient::ConsumeApi($RutaApi,'POST',$parametros);
													$arraydatos2 = json_decode($datos3,true);
													$contador++;
													//ECHO $parametros;
													if($item3['estadoEjecucion']){
														//ECHO "<BR />".$arraydatos2['mensaje'];
														$contadorExitos2++;
													}
													else {
														//echo "<br />Script con errores:";
														if($contadorErrores2 == 0){
															$mensajeUser .= "<br /><b>Script con errores:</b>";
														}
														$mensajeUser .= "<br />";
														$mensajeUser .= "Script ID: <b>".$item3['scriptID']."</b> Detalle script ID: <b>".$item3['detalleScriptID']."</b><br>
														Script: ".$item3['resultado']."<br><br>";
														$contadorErrores2++;
													}
													
												}
											}
											$inIDscript .="";
											//ECHO "<BR />";
											$inIDDetalleScript .="";
											
											if($contador>0){
												$parametros = json_encode(Array(
													"Token" => ApiHttpClient::$Token,
													"scriptID" => $inIDscript,
													"detalleScriptID" => $inIDDetalleScript,
													"MacAddress" => ApiHttpClient::$MacAddress
												)); 
												$datos = ApiHttpClient::ConsumeApi($RutaApi,'PUT',$parametros);
											}
											
													
											if($validaEjecucion){
												$colorEvento = "verde";
												 $mensajeUser .= "</br ><b>Se ejecutaron " . $contadorExitos2 . " registros de " . $contador."</b><br />Registros erroneos: " . $contadorErrores2;
												//$mensajeUser .= "Ejcutados: ".$mensaje2."<br><br>Errores:".$mensajeGeterror."<br>";
												$mensajeCorto = "Ejcutados: ".$mensaje2."<br><br>Errores:".$contadorErrores2."<br>";
											}else{
												//echo "La ejecución del api no se realizó correctamente";
												$mensajeUser .= "Error: La ejecución no se realizó correctamente<br>";
												$mensajeCorto = "Ocurrio un error durante la ejecucion del api<br>";
											}
										}
										catch (Exception $e){
											//echo $e->getMessage();
											$colorEvento = "rojo";
											$mensajeCorto = "Ocurrio un error en la ejecución";
											$mensajeDesarrollo .= "Error :".$e->getMessage()."<br>";
										}
									}else{
										$colorEvento = "rojo";
										echo "No se pudo ejecutar el script del api";
										$mensajeUser .= "No se pudo ejecutar el script del api<br>";
										$mensajeCorto = "No se pudo ejecutar el script del api";
									}
								}else{
									////////////////////////////////////////////////////////////////////////////////////
									///////////////////////////// PARA METODO NORMAL GET ///////////////////////////////
									////////////////////////////////////////////////////////////////////////////////////
									try{										                           
										$validaEjecucion = $migracion->Inserta($coleccion);										
									}catch (Exception $e){
										$colorEvento = "rojo";
										$validaEjecucion=false;
										$mensajeDesarrollo .= "<br>Error al insertar los datos localmente, ".$e->getMessage()."<br>";//.'<br /> <br />Consulta: <br />';
										$mensajeCorto = "Error : no se pudieron migrar los datos";
									}
								}
								
								
								try{
									if(trim($objeto) <> "EjecucionScripts" and Yii::$app->session['TIPOMETODO']<>"POST"){//$aplicacionID <> "1" AND 
										if($validaEjecucion){
											$colorEvento = "verde";
											 $mensajeUser .= "<b>Se guardaron " . count($coleccion) . " registros de ".count($coleccion)."</b>";
											$exito=true;
											$mensajeCorto = "<b>Se guardaron " . count($coleccion) . " registros de ".count($coleccion)."</b>";
											//Yii::$app->session->set('RESULTADO_BOTON', $mensaje2);
										}
										else{
											$colorEvento = "naranja";
											//echo "La ejecución no se realizó correctamente";
											$mensajeCorto = "<br>Error:  No se pudo validar la ejecucion del api<br>";
											$mensajeUser .= "<br>Error: <br> no se pudo validar la ejecucion del api<br>";
										}
									}
								}
								catch (Exception $e){
									//echo $e->getMessage();//.'<br /> <br />Consulta: <br />';
									$colorEvento = "rojo";
									$mensajeCorto = "Ocurrio un error en la ejecucion del script";
									$mensajeDesarrollo .= "Ocurrio un error en la ejecucion del script ".$e->getMessage()."<br><br>";
								}
								
							}else{
								$colorEvento = "naranja";
								$mensajeCorto = "Alerta : No hay registros para actualizar <br>";
								$mensajeUser = "Alerta : No hay registros para actualizar <br><br>";
							}
						}else{
							$exito=true;
							 $colorEvento = "naranja";
							$mensajeCorto = "La tabla de origen: ".trim($txtBanderaApi)." no contiene datos para actualizar o hay un error en la conexion";
							 $mensajeUser .= "<br /><b>La tabla de origen: ".trim($txtBanderaApi)." no contiene datos para actualizar o hay un error en la conexion</b><br>";
							//$RegistrosErrores .= $mensaje2."<br> ".$mensajeGeterror."<br>";
						}
						//finaliza la coleccion de datos
					}catch (Exception $e){
						//echo $e->getMessage();
						$colorEvento = "rojo";
						$mensajeCorto = "Error al cargar los dato";
						$mensajeDesarrollo .= "Error de datos:".$e->getMessage()."<br>";
                       // echo "<br /><b>Cai en excepcion coleccion</b> ".$e->getMessage();
					}
				}else{
					if(isset($arraydatos['Mensaje'])){
						$colorEvento = "rojo";
						$mensajeCorto = "Error de conexion con el api";
						$mensajeUser .= "Error de conexion con el api: ".$arraydatos['mensaje']."<br>";
					}else{
						$colorEvento = "rojo";
						$mensajeCorto = "Error de conexion con el api";
						$mensajeUser .= "Error de conexion con el api: El api no produjo resultados.<br><br>";
					}
					
					if(isset($arraydatos['desarrollo'])){
						$mensajeDesarrollo .= "<br>".$arraydatos['desarrollo']."<br>";
					}
				}
			
			
		}else{
			$colorEvento = "naranja";
			$mensajeCorto = "No hay datos en " . $txtBanderaApi;
			$mensajeUser .= "No hay datos en " . $txtBanderaApi."<br>";		
		}
		//finaliza listados api
		
	}else{
		$colorEvento = "rojo";
		$mensajeCorto = "El fichero Modelo" . trim($txtBanderaApi) . ".php no existe<br>";
		$mensajeUser .= "El fichero Modelo" . trim($txtBanderaApi) . ".php no existe<br>";
		//echo $RegistrosErrores;
	}
	//$colorEvento = "verde";
}catch (Exception $e){
   //echo $this->mensaje = $e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
	$colorEvento = "rojo";
	$mensajeDesarrollo .= "Exception:".$e->getMessage()."<br>";
	$mensajeCorto = "Error al intentar ejecutar el Modelo :  ".$file."<br>";
}

//echo trim($txtBanderaApi)."--- txtbande<br>";
$banderaName = trim($txtBanderaApi);
//$miArray = Yii::$app->session['logMigracion'];
$session = Yii::$app->session;
// Yii::$app->session->set('RESULTADO_BOTON', $RegistrosErrores.$ErrorDetenerCiclo);
Yii::$app->session->set('RESULTADO_BOTON', $mensajeCorto);

 if(empty($session['logMigracion'])) {
      $session['logMigracion'] = array($banderaName => array('color'=>$colorEvento, "error"=>$mensajeUser, 'mensaje2'=>$cabeceraError.$mensajeUser, 'mensajeCorto'=>$mensajeCorto, 'desarrollo'=>$mensajeDesarrollo));
 }else{
      $session['logMigracion'] = array_merge(Yii::$app->session['logMigracion'], array($banderaName => array('color'=>$colorEvento, "error"=> $mensajeUser, 'mensaje2'=>$cabeceraError."<br>".$mensajeUser, 'mensajeCorto'=>$mensajeCorto, 'desarrollo'=>$mensajeDesarrollo)));
 }

//$miArray = array($banderaName = array('color'=>'verde', "error"=>$RegistrosErrores));



?>