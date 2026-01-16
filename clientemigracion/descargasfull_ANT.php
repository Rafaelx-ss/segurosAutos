
        <?php
		use Yii;
		$mensaje2="";
            //require 'lib/database.php';
           //echo $txtBanderaApi;
            require_once(dirname(__DIR__).'/clientemigracion/modelos/ModeloApiListado.php');
            //require 'modelos/ModeloApiMigracion.php';
			$incluir= dirname(__DIR__).'/clientemigracion/modelos/Modelo' . trim($txtBanderaApi) . '.php';
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
                if($arraydatos['resultado'])
                {
					try{
					
						$coleccion = $arraydatos["datos"];
						if($coleccion != ""){
							//echo "antes de borrado";
							//if($migracion->BorrarTodo($error)){
							if(count($coleccion) > 0){
								
								//echo "despues de borrado";
								//echo count($coleccion);
								//echo $migracion->Inserta($txtBanderaApi,$coleccion);
								//echo "antes de inserta";
								$validaEjecucion= true;
								if($aplicacionID == "1"){ 
									//ECHO "APLICACION==1";
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
											if($objeto=="Textos"){
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
											elseif($objeto=="Menus"){
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
											elseif($objeto=="Catalogos"){
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
											$datosReturn = ApiHttpClient::ConsumeApi($RutaApi,'POST',$parametrosR);
											$arraydatosReturn = json_decode($datosReturn,true);
											//echo $arraydatosReturn['mensaje']."";//$item4['nombreTexto']."<br />";
											IF($arraydatosReturn['mensaje']<>""){
												ECHO "<BR />".$arraydatosReturn['mensaje'];
											}
											if($arraydatosReturn['resultado']){
												$contadorExitos++;
											}else{
												$contadorErrores++;
											}
											//echo $parametrosR;
											$contadorReturn++;
											if ($contadorReturn==10) break;
										}
										echo "</div>";
									}
									else{
										echo "La consulta local no trajo resultados";
									}
									if($validaEjecucion){
										echo $mensaje2 = "</br ><b>Se guardaron " . $contadorExitos . " registros de " . count($datosInsertados2)."</b><br />Registros erroneos: " . $contadorErrores;
											//$exito=true;
									}
									else{
										echo "La ejecución no se realizó correctamente";
										//$exito=false;
									}
									
								}elseif(trim($objeto) == "EjecucionScripts"){
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
											foreach($coleccion as $item2){
												$usuarioMigracion=$item2['usuarioApiID'];
												$AplicacionMigracion= 2;
												$fechaInicioMigracion= $item2['fechaInicio'];
												break;
											}
											$dato1=0;
											$datosInsertados= $migracion->ObtenerDatosLog($dato1, $usuarioMigracion, $fechaInicioMigracion);
											$inIDscript="";
											$inIDDetalleScript="";
											$contador=0;
											$contadorExitos2=0;
											$contadorErrores2=0;
											foreach($datosInsertados as $item3){
												if($contador>0){
													$inIDscript .= ",".$item3['scriptID'];
													$inIDDetalleScript .= ",".$item3['detalleScriptID'];
												}else{
													$inIDscript .=  $item3['scriptID'];
													$inIDDetalleScript .= $item3['detalleScriptID'];
												}
												
												$parametros = json_encode(Array(
													"Token" => ApiHttpClient::$Token,
													"usuarioApiID" => $item3['usuarioApiID'],
													"scriptID" => $item3['scriptID'],
													"detalleScriptID" => $item3['detalleScriptID'],
													"fechaEjecucion" => $item3['fechaEjecucion'],
													"estadoEjecucion" => $item3['estadoEjecucion'],
													"resultado" => str_replace("'","\'",$item3['resultado']),
													"versionRegistro" => $item3['versionRegistro'],
													"regEstado" => $item3['regEstado'],
													"regFechaUltimaModificacion" => $item3['regFechaUltimaModificacion'],
													"regUsuarioUltimaModificacion" => $item3['regUsuarioUltimaModificacion'],
													"regFormularioUltimaModificacion" => $item3['regFormularioUltimaModificacion'],
													"regVersionUltimaModificacion" => $item3['regVersionUltimaModificacion'],
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
													$contadorErrores2++;
												}
												if($arraydatos2['resultado']){
													
												}else{
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
												echo $mensaje2 = "</br ><b>Se ejecutaron " . $contadorExitos2 . " registros de " . count($datosInsertados)."</b><br />Registros erroneos: " . $contadorErrores2;
													//$exito=true;
											}
											else{
												echo "La ejecución no se realizó correctamente";
												//$exito=false;
											}
										}
										catch (Exception $e){
											echo $this->mensaje = $e->getMessage();
										}
									}
								}
								else{
									try{
										//echo "INSERTA NORMAL";
										$validaEjecucion = $migracion->Inserta($coleccion);
									}
									catch (Exception $e){
										echo $e->getMessage();//.'<br /> <br />Consulta: <br />';
									}
								}
								
								try{
									if($aplicacionID <> "1" AND trim($objeto) <> "EjecucionScripts"){
										if($validaEjecucion){
											echo $mensaje2 = "</br ><b>Se guardaron " . count($coleccion) . " registros de ".count($coleccion)."</b>";
												$exito=true;
										}
										else{
											echo "La ejecución no se realizó correctamente";
											$exito=false;
										}
									}
								}
								catch (Exception $e){
									echo $e->getMessage();//.'<br /> <br />Consulta: <br />';
								}
							}else{
								//echo $mensaje2 = $arraydatos['mensaje'];
								$exito=false;
							}
						}
						else {
							$exito=true;
							echo $mensaje2= "<br /><b>La tabla de origen: ".trim($txtBanderaApi)." no contiene datos para actualizar</b>";
						}
						
					}   
					catch (Exception $e){
						echo $this->mensaje = $e->getMessage();
					}
                }
				else{
					IF(TRIM($arraydatos['mensaje']))
						echo $mensaje2= "</br>El api no produjo resultados" . $arraydatos['mensaje'];
					else
						echo $mensaje2= "</br>El api no produjo resultados: " . $arraydatos['mensaje'];
				}
			    
            }
            else
            {
                $mensaje2= "Error al obtener los datos";
            }
       if(!$exito)
			$mensaje2= "Error al guardar los datos. ".$mensaje2;
        ?>