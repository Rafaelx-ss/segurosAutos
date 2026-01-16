
<?php
use Yii;
	Yii::$app->session->set('RESULTADO_BOTON', "");
	$RegistrosErrores= "";
	$ErrorDetenerCiclo= "";
	try{
		$mensaje2="";
		//require 'lib/database.php';
		//echo $txtBanderaApi;
		require_once(dirname(__DIR__).'/clientemigracion/modelos/ModeloApiListado.php');
		//require 'modelos/ModeloApiMigracion.php';
		$incluir= dirname(__DIR__).'/clientemigracion/modelos/Modelo' . trim($txtBanderaApi) . '.php';
		
		if (file_exists($incluir)) {
			require $incluir;
            $database = new Database();
            $conn = $database->getConnection();
            
			$listadoApi = new ListadoApi($database);
			
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
				elseif(trim($txtBanderaApi) == "ApiAplicacionesConexionApi"){
					$objeto="AplicacionesConexionApi";
				}
				elseif(trim($txtBanderaApi) == "ApiConexionesApis"){
					$objeto="ConexionesApis";
				}
				elseif(trim($txtBanderaApi) == "ApiConexionesApisEstablecimientos"){
					$objeto="ConexionesApisEstablecimientos";
				}
				else{
					$objeto= str_replace('Api','',trim($txtBanderaApi));
				}
				$migracion = new $objeto($database);
			}

			$exito = false;

			//echo $txtBanderaApi.".";
            if($listadoApi->ObtenerDatos($txtBanderaApi))
            {
				
                $servidor = $listadoApi->Dataset['direccionServidor'];
				//echo $servidor = "http://localhost:8080/ApiCongo";
				
                $RutaApi = $listadoApi->Dataset['rutaApi'];
				echo "<br /><b>Direccion Descarga:</b> ".$servidor .$RutaApi;
            

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
				elseif(trim($objeto) == "GruposEstablecimientos" ){
					
				}
				//echo "user: ".$Username;
				if(trim($objeto) == "Establecimientos" || trim($objeto) == "DireccionesEstablecimientos" || trim($objeto) == "AplicacionessGruposEstablecimientos" || trim($objeto) == "UsuariosAPI" || trim($objeto) == "MetodosUsuariosApis"){
					$IdGrupoParametro= Yii::$app->session['IDENTIFICADOR'];
				}
				
				echo "<br /><b>Identificador:</b> ";
				echo Yii::$app->session['IDENTIFICADOR'];
				 
				$aplicacionID= Yii::$app->session['APLICACION_ID'];
				
				
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
				//ECHO Yii::$app->session['USUARIOSAPILISTA'];
                $arraydatos = json_decode($datos,true);
               //echo "as".count($coleccion)."pp";
			   //echo $_SESSION["Token"]." MAC " . $_SESSION["MacAddress"];
				//echo $arraydatos['mensaje'];
				//echo $parametros;//."----".$RutaApi;
                if($arraydatos['resultado'] or Yii::$app->session['TIPOMETODO']=="POST")
                {
					try{
					
						$coleccion = $arraydatos["datos"];
						if($coleccion != "" or Yii::$app->session['TIPOMETODO']=="POST"){
							//echo "antes de borrado";
							//if($migracion->BorrarTodo($error)){
							if(Yii::$app->session['TIPOMETODO']=="POST"){
								$coleccion= array("");
							}
							if(count($coleccion) > 0){
								
								//echo "despues de borrado";
								//echo count($coleccion);
								//echo $migracion->Inserta($txtBanderaApi,$coleccion);
								//echo "antes de inserta";
								$mensajeGeterror="";
								$validaEjecucion= true;
								if(Yii::$app->session['TIPOMETODO']=="POST"){ 
									////////////////////////////////////////////////////////////////////////////////////
									//////////////////////////////// PARA METODOS POST /////////////////////////////////
									////////////////////////////////////////////////////////////////////////////////////
									
									try{
										$dato1=0;
										$datosInsertados2= $migracion->ObtenerDatos($dato1, "REGRESA_DATOS");
										$contadorReturn=0;
										$contadorExitos=0;
										$contadorErrores=0;
										
										
										IF(count($datosInsertados2)>0){
											echo "<br /><label class='lblVerMensaje' style='cursor:pointer;' onclick='$(\".lblVerMensaje\").hide();$(\".lblOcultarMensaje\").show();$(\".divMensajeReturn\").show();' >Ver Resultado</label>
											<label class='lblOcultarMensaje' style='cursor:pointer; display:none;'onclick='$(\".lblVerMensaje\").show();$(\".lblOcultarMensaje\").hide();$(\".divMensajeReturn\").hide();' >Ocultar</label>
											<div id='divMensajeReturn' class='divMensajeReturn' style='display:none;'>";
											ECHO "Total de filas consultadas: ".count($datosInsertados2)."<br />";
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
													$parametrosR = json_encode(Array(
														"Token" => ApiHttpClient::$Token,
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
													));
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
												
												$datosReturn = ApiHttpClient::ConsumeApi($RutaApi,'POST',$parametrosR);
												
												$arraydatosReturn = json_decode($datosReturn,true);
												//echo $arraydatosReturn['mensaje']."";//$item4['nombreTexto']."<br />";
												IF($arraydatosReturn['mensaje']<>""){
													//ECHO "<BR />Fila: ". $contadorReturn ." ".$arraydatosReturn['mensaje'];
													$RegistrosErrores = "<BR />".$arraydatosReturn['mensaje'];
												}
												if($arraydatosReturn['resultado']){
													$contadorExitos++;
												}else{
													$contadorErrores++;
													ECHO "<BR />Fila Error: ". $contadorReturn ." ". $arraydatosReturn['mensaje']."<br />".$parametrosR."<br />";
												}
												//echo $parametrosR;
												$contadorReturn++;
												if(trim($arraydatosReturn['mensaje'])=="Acceso a metodo no permitido"){
													echo $ErrorDetenerCiclo ="<br />Se detuvo el ciclo por que no se cuenta con permisos para el meétodo solicitado";
													break;
												}
												if(trim($arraydatosReturn['mensaje'])=="método POST no implementado"){
													
													echo $ErrorDetenerCiclo ="<br />Se detuvo el ciclo por que el método POST no esta Implementado para el API seleccionado";
													
													
													break;
												}
												//if ($contadorReturn==10) break;
											}
											echo "</div>";
										}
										else{
											echo "La consulta local no trajo resultados";
											$RegistrosErrores = "La consulta local no trajo resultados";
										}
										if($validaEjecucion){
											echo $mensaje2 = "</br ><b>Se guardaron " . $contadorExitos . " registros de " . count($datosInsertados2)."</b><br />Registros erroneos: " . $contadorErrores;
											$RegistrosErrores = $mensaje2;
										}
										else{
											echo "La ejecución no se realizó correctamente";
											$RegistrosErrores = "La ejecución no se realizó correctamente";
										}
										
									}
									catch (Exception $e){
										echo "<br />Error al consultar datos locales, SIN registros para enviar a método POST: ".$e->getMessage();
										$RegistrosErrores = "<br />Error al consultar datos locales, SIN registros  para enviar a método POST: <br />".$e->getMessage();
										//Yii::$app->session->set('RESULTADO_BOTON', "<br />Error al consultar datos locales para enviar a método POST: <br />".$e->getMessage());
									}
									$exito=true;
								}elseif(trim($objeto) == "EjecucionScripts"){
									$exito=true;
									////////////////////////////////////////////////////////////////////////////////////
									/////////////////////////// PARA EJECUCION DE SCRIPTS //////////////////////////////
									////////////////////////////////////////////////////////////////////////////////////
									//ECHO "OBJETO=EjecucionScripts";
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
												$datosInsertados= $migracion->ObtenerDatosLog($dato1, $item2['detalleScriptID'], $fechaInicioMigracion);
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
													IF($item3['estadoEjecucion']){
														//ECHO "<BR />".$arraydatos2['mensaje'];
														$contadorExitos2++;
													}
													else {
														//echo "<br />Script con errores:";
														if($contadorErrores2 == 0){
															echo "<br /><b>Script con errores:</b>";
														}
														echo "<br />";
														echo "Script ID: <b>".$item3['scriptID']."</b> Detalle script ID: <b>".$item3['detalleScriptID']."</b>";
														$contadorErrores2++;
													}
													if($arraydatos2['resultado']){
														
													}else{
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
												echo $mensaje2 = "</br ><b>Se ejecutaron " . $contadorExitos2 . " registros de " . $contador."</b><br />Registros erroneos: " . $contadorErrores2;
												$RegistrosErrores = $mensaje2;
											}
											else{
												echo "La ejecución no se realizó correctamente";
												$RegistrosErrores = "La ejecución no se realizó correctamente";
											}
										}
										catch (Exception $e){
											echo $e->getMessage();
											$RegistrosErrores = $e->getMessage();
										}
									}else{
										echo "La ejecución no se realizó correctamente";
										$RegistrosErrores = "La ejecución no se realizó correctamente";
									}
								}
								else{
									////////////////////////////////////////////////////////////////////////////////////
									///////////////////////////// PARA METODO NORMAL GET ///////////////////////////////
									////////////////////////////////////////////////////////////////////////////////////
									try{
										//echo "INSERTA NORMAL";
										$validaEjecucion = $migracion->Inserta($coleccion);
									}
									catch (Exception $e){
										$validaEjecucion=false;
										echo $mensajeGeterror= $e->getMessage();//.'<br /> <br />Consulta: <br />';
										$RegistrosErrores = $e->getMessage();
									}
								}
								
								try{
									if(trim($objeto) <> "EjecucionScripts" and Yii::$app->session['TIPOMETODO']<>"POST"){//$aplicacionID <> "1" AND 
										if($validaEjecucion){
											echo $mensaje2 = "</br ><b>Se guardaron " . count($coleccion) . " registros de ".count($coleccion)."</b>";
											$exito=true;
											$RegistrosErrores = $mensajeGeterror.$mensaje2;
											//Yii::$app->session->set('RESULTADO_BOTON', $mensaje2);
										}
										else{
											echo "La ejecución no se realizó correctamente";
											$RegistrosErrores = $mensajeGeterror."La ejecución no se realizó correctamente";
										}
									}
								}
								catch (Exception $e){
									echo $e->getMessage();//.'<br /> <br />Consulta: <br />';
									$RegistrosErrores = $e->getMessage();
								}
							}else{
								//echo $mensaje2 = $arraydatos['mensaje'];
								$exito=false;
							}
						}
						else {
							$exito=true;
							echo $mensaje2= "<br /><b>La tabla de origen: ".trim($txtBanderaApi)." no contiene datos para actualizar</b>";
							$RegistrosErrores = $mensaje2;
						}
						
					}   
					catch (Exception $e){
						echo $e->getMessage();
						$RegistrosErrores = $e->getMessage();
					}
                }
				else{
					IF(TRIM($arraydatos['mensaje']))
						echo $mensaje2= "</br>El api no produjo resultados" . $arraydatos['mensaje'];
					else
						echo $mensaje2= "</br>El api no produjo resultados: " . $arraydatos['mensaje'];
					
					$RegistrosErrores = $mensaje2;
				}
			    
            }
            else
            {
                $mensaje2= "Error al obtener los datos";
				$RegistrosErrores = $mensaje2;
            }
			
			if(!$exito){
				$mensaje2= "Error al guardar los datos. ".$mensaje2;
				$RegistrosErrores = $mensajeGeterror."<br />".$mensaje2;
			}
		} else {
			$RegistrosErrores = "El fichero Modelo" . trim($txtBanderaApi) . ".php no existe";
			echo $RegistrosErrores;
			
		}
		
			
	}  
    catch (Exception $e){
        echo $this->mensaje = $e->getMessage();//.'<br /> <br />Consulta: <br />'.$consulta;
		$RegistrosErrores = $e->getMessage();
    }
	
	Yii::$app->session->set('RESULTADO_BOTON', $RegistrosErrores.$ErrorDetenerCiclo);
?>