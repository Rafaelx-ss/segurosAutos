<?php
namespace app\components;


use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Url;

class Globals extends Component
{
	/*
	public function getTraslete($textoID, $idiomaID, $defaul){
		//$texto = Yii::$app->db->createCommand('SELECT texto FROM Textos inner join TextosIdiomas on Textos.textoID=TextosIdiomas.textoID  where Textos.textoID="'.$textoID.'" and TextosIdiomas.idiomaID="'.$idiomaID.'"')->queryOne();
		$fileTraduccion = Yii::$app->basePath.'/traducciones/traduccion_'.$idiomaID.'.php';
		if(file_exists($fileTraduccion)) {
			require '../traducciones/traduccion_'.$idiomaID.'.php';
			if(isset($mensajes[$textoID])){
				return $mensajes[$textoID];
			}else{
				return $defaul;
			}
		}else{
			return $defaul;
		}		
	} 
	*/
	public $data = 1;

	public function getTimeSession()
	{
		$configData = Yii::$app->db->createCommand("SELECT * FROM ConfiguracionesSistema where configuracionesSistemaID='1'")->queryOne();
		if (isset($configData['tiempoSesion'])) {
			return $configData['tiempoSesion'];
		} else {
			return 3600 * 24 * 30;
		}

	}




	public function setBitacora($encabezado, $detalle, $correo, $docto, $user, $idMsg = 0)
	{
		// return false;
		$folio = 0;

		$ip = $_SERVER['REMOTE_ADDR'];
		$mac = "";

		$macCad = shell_exec('cat /sys/class/net/*/address');
		$lines = explode("\n", $macCad);
		$macAddr = 'ND';

		foreach ($lines as $rmac) {
			if ($rmac != "") {
				$macAddr = $rmac;
				break;
			}
		}


		if ($user == 0 or $user == "") {
			$user = 1;
		}

		if ($correo == 1) {
			$correo = 1;
		} else {
			$correo = 0;
		}

		$id = 0;
		$idEstablecimiento = 0;
		if (Yii::$app->globals->getEstablecimiento() != 0) {
			$idEstablecimiento = Yii::$app->globals->getEstablecimiento();
		}

		Yii::$app->db->createCommand('INSERT INTO Bitacora(folio, fecha, detalle, envioCorreoElectronico, documento, usuarioID, establecimientoID, usuarioIp, usuarioMac, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion, mensajeBitacoraID) VALUES (0, NOW(), "' . $detalle . '", 0, "' . $docto . '", ' . $user . ', "' . $idEstablecimiento . '", "' . $ip . '", "' . $macAddr . '", "' . Yii::$app->globals->getVersion() . '", 1, NOW(), ' . $user . ', "1",  "' . Yii::$app->globals->getVersion() . '", "' . $idMsg . '")')->query();
		$id = Yii::$app->db->getLastInsertID();



		return $id;


	}


	//menu general
	function getMenu($idUser)
	{
		$menusCatalogo = Yii::$app->db->createCommand("SELECT Menus.menuID, Menus.nombreMenu, Menus.textoID FROM Menus inner join PermisosMenus on Menus.menuID=PermisosMenus.menuID  inner join PerfilesCompuestos on PerfilesCompuestos.perfilID=PermisosMenus.perfilID inner join Usuarios on Usuarios.usuarioID = PerfilesCompuestos.usuarioID where Menus.regEstado=1 and activoMenusFormularios=1 and Usuarios.usuarioID='" . $idUser . "' and PermisosMenus.regEstado=1 and  PerfilesCompuestos.regEstado=1 GROUP BY Menus.menuID order by Menus.orden ASC")->queryAll();

		$menuDatos = "";

		$perfilesCompuestos = Yii::$app->db->createCommand("SELECT * FROM PerfilesCompuestos where usuarioID='" . $idUser . "' and activoPermiso=1 and regEstado=1")->queryAll();

		$wherePerfil = "";
		$dmas = 1;
		foreach ($perfilesCompuestos as $dato) {
			if ($dmas == 1) {
				$wherePerfil .= "and (";
				$wherePerfil .= "PermisosFormulariosPerfiles.perfilID='" . $dato['perfilID'] . "'";
			} else {
				$wherePerfil .= " or PermisosFormulariosPerfiles.perfilID='" . $dato['perfilID'] . "'";
			}
			$dmas++;

		}
		if ($wherePerfil != "") {
			$wherePerfil .= ")";
		}
		//".$wherePerfil."

		foreach ($menusCatalogo as $rMenuCat) {
			$submenusCat = Yii::$app->db->createCommand("SELECT Formularios.formularioID, Formularios.nombreFormulario, Formularios.icono, Formularios.urlArchivo, Formularios.tipoFormularioID, Formularios.textoID, Formularios.tipoMenu, Formularios.formID FROM Formularios inner join MenusFormularios on Formularios.formularioID=MenusFormularios.formularioID  inner join PermisosFormulariosPerfiles on Formularios.formularioID=PermisosFormulariosPerfiles.formularioID   inner join PerfilesCompuestos on PerfilesCompuestos.perfilID=PermisosFormulariosPerfiles.perfilID inner join Usuarios on Usuarios.usuarioID = PerfilesCompuestos.usuarioID  where MenusFormularios.regEstado=1 and MenusFormularios.menuID='" . $rMenuCat['menuID'] . "' " . $wherePerfil . " and Formularios.regEstado=1 and Formularios.estadoFormulario=1 and Usuarios.usuarioID='" . $idUser . "'  and PermisosFormulariosPerfiles.activoPermiso=1 and PerfilesCompuestos.regEstado=1 GROUP BY Formularios.formularioID order by MenusFormularios.ordenMenuFormulario ASC;")->queryAll();

			$activeMenu = '';
			$arraySmenu = array();
			foreach ($submenusCat as $rsmform) {
				if (isset($_GET['r']) and $rsmform['tipoMenu'] != 'Submenu' and $rsmform['tipoMenu'] != 'Submenuv2') {
					$mActual = explode("/", $_GET['r']);
					$mConfig = explode("/", $rsmform['urlArchivo']);
					if (isset($mActual[0]) and isset($mConfig[0])) {
						if ($rsmform['tipoMenu'] == 'Menu') {
							$formMenu = Yii::$app->db->createCommand("SELECT * FROM Formularios where formID='" . $rsmform['formularioID'] . "' and regEstado=1 and estadoFormulario=1")->queryAll();

							foreach ($formMenu as $ndatafile) {
								$mConfign = explode("/", $ndatafile['urlArchivo']);
								if ($mActual[0] == $mConfign[0]) {
									$activeMenu = 'mm-active';
								}
							}

						} else {
							if ($mActual[0] == $mConfig[0]) {
								$activeMenu = 'mm-active';
							} else {
								if ($rMenuCat['menuID'] == 1 and ($mActual[0] == 'campos' or $mActual[0] == 'camposgrid' or $mActual[0] == 'traducciones' or $mActual[0] == 'formularios' or $mActual[0] == 'mformularios' or $mActual[0] == 'acciones' or $mActual[0] == 'aformularios' or $mActual[0] == 'usuarios' or $mActual[0] == 'pcompuestos' or $mActual[0] == 'paccion' or $mActual[0] == 'pmenus' or $mActual[0] == 'formulariosperfiles')) {
									$activeMenu = 'mm-active';
								}
							}
						}


					}


				}

				if ($rsmform['tipoMenu'] != 'Submenu' and $rsmform['tipoMenu'] != 'Submenuv2') {
					$arraySmenu[] = array('formularioID' => $rsmform['formularioID'], 'nombreFormulario' => $rsmform['nombreFormulario'], 'icono' => $rsmform['icono'], 'urlArchivo' => $rsmform['urlArchivo'], 'tipoFormularioID' => $rsmform['tipoFormularioID'], 'textoID' => $rsmform['textoID'], 'tipoMenu' => $rsmform['tipoMenu'], 'formID' => $rsmform['formID']);
				}
			}

			$menuDatos .= ' <li class="app-sidebar__heading ' . $activeMenu . '" style="margin: .1rem 0;">';
			$menuDatos .= '<a href="#" style="padding:0 .1rem 0 2px; text-transform: uppercase; font-weight:600;">';
			$menuDatos .= Yii::$app->globals->getTraductor($rMenuCat['textoID'], Yii::$app->session['idiomaId'], $rMenuCat['nombreMenu']);
			$menuDatos .= '<i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>';
			$menuDatos .= '</a>';

			//echo '<li class="app-sidebar__heading">'.Yii::$app->globals->getTraductor($rMenuCat['textoID'], Yii::$app->session['idiomaId'], $rMenuCat['nombreMenu']).'</li>';


			$menuDatos .= '<ul class="mm-collapse">';
			foreach ($arraySmenu as $arsmform) {
				$menuDatos .= '<li>';
				if ($arsmform['tipoFormularioID'] == '2') {
					$menuDatos .= Html::a('<i class="metismenu-icon ' . $arsmform['icono'] . '"></i>' . Yii::$app->globals->getTraductor($arsmform['textoID'], Yii::$app->session['idiomaId'], $arsmform['nombreFormulario']) . '', $url = ['site/iframe&pagina=' . md5($arsmform['formularioID'])], $options = ['class' => 'mrg-top-30']);
				} elseif ($arsmform['tipoFormularioID'] == '3') {
					$menuDatos .= '<a class="mrg-top-30" href="' . $arsmform['urlArchivo'] . '" target="_blank"><i class="metismenu-icon ' . $arsmform['icono'] . '"></i>' . Yii::$app->globals->getTraductor($arsmform['textoID'], Yii::$app->session['idiomaId'], $arsmform['nombreFormulario']) . '</a>';
				} elseif ($arsmform['tipoFormularioID'] == '4' and $arsmform['tipoMenu'] == 'Menu') {
					$menuDatos .= Html::a('<i class="metismenu-icon ' . $arsmform['icono'] . '"></i>' . Yii::$app->globals->getTraductor($arsmform['textoID'], Yii::$app->session['idiomaId'], $arsmform['nombreFormulario']) . '', $url = [trim($arsmform['urlArchivo']) . '&f=' . md5($arsmform['formID'])], $options = ['class' => 'mrg-top-30']);
				} else {
					$menuDatos .= Html::a('<i class="metismenu-icon ' . $arsmform['icono'] . '"></i>' . Yii::$app->globals->getTraductor($arsmform['textoID'], Yii::$app->session['idiomaId'], $arsmform['nombreFormulario']) . '', $url = [trim($arsmform['urlArchivo']) . '&f=' . md5($arsmform['formularioID'])], $options = ['class' => 'mrg-top-30']);
				}
				$menuDatos .= '</li>';


			}
			$menuDatos .= '</ul>';
			$menuDatos .= '</li>';

		}


		return $menuDatos;
	}



	function createCrud($modeloNm)
	{
		$rutaArchivos = "";
		$template = "Custom";
		$modelo = $modeloNm;


		$data = array(
			"Generator[baseControllerClass]" => "yii\web\Controller",
			"Generator[controllerClass]" => "app\\" . $rutaArchivos . "controllers\\" . $modelo . "Controller",
			"Generator[enableI18N]" => 0,
			"Generator[enablePjax]" => 0,
			"Generator[indexWidgetType]" => "grid",
			"Generator[messageCategory]" => "app",
			"Generator[modelClass]" => "app\\" . $rutaArchivos . "models\\" . $modelo,
			"Generator[searchModelClass]" => "app\\" . $rutaArchivos . "models\\" . $modelo . "Search",
			"Generator[template]" => $template,
			"Generator[viewPath]" => addslashes(Yii::$app->basePath) . "\\" . $rutaArchivos . "views\\" . strtolower($modelo),
			"modeloName" => $modelo,
			"preview" => ""
		);


		$url = Url::home(true) . "?r=gii/default/view&id=crud";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
		$response = curl_exec($ch);
		//print_r($response);
		if ($response) {
			$regex = '|(<div class="invalid-feedback">(.+?)</div>)|';
			//print_r($response);
			if (preg_match_all($regex, $response, $list)) {
				echo '<span style="color:#8d1717">EL modelo "' . $modeloNm . '" no existe o hay un error de sintaxis </span><br>';
			} else {
				//no hay error
				//print_r($response);
				$regex = '|(?<=answers\[).+?(?=\])|';
				if (preg_match_all($regex, $response, $list)) {
					$answers = '';
					if (isset($list[0])) {
						$send = array(
							"Generator[baseControllerClass]" => "yii\web\Controller",
							"Generator[controllerClass]" => "app\\" . $rutaArchivos . "controllers\\" . $modelo . "Controller",
							"Generator[enableI18N]" => 0,
							"Generator[enablePjax]" => 0,
							"Generator[indexWidgetType]" => "grid",
							"Generator[messageCategory]" => "app",
							"Generator[modelClass]" => "app\\" . $rutaArchivos . "models\\" . $modelo,
							"Generator[searchModelClass]" => "app\\" . $rutaArchivos . "models\\" . $modelo . "Search",
							"Generator[template]" => $template,
							"Generator[viewPath]" => addslashes(Yii::$app->basePath) . "\\" . $rutaArchivos . "views\\" . strtolower($modelo)
						);

						foreach ($list[0] as $dataAns) {
							$send['answers[' . $dataAns . ']'] = 1;
						}
						$send['generate'] = "";
						$send['modeloName'] = "";


						$ch2 = curl_init($url);
						curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch2, CURLINFO_HEADER_OUT, true);
						curl_setopt($ch2, CURLOPT_POST, true);
						curl_setopt($ch2, CURLOPT_POSTFIELDS, $send);
						curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, false);
						curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($ch2, CURLOPT_CONNECTTIMEOUT, 15);
						$create = curl_exec($ch2);
						//print_r($create);
						if ($create) {
							$regex2 = '|(<div class="alert alert-success">(.+?)</div>)|';
							if (preg_match_all($regex2, $create, $respuesta)) {
								if (isset($respuesta[0][0])) {
									echo '<span style="color:#3a7914">El CRUD del modelo "' . $modeloNm . '" se generó correctamente </span><br>';
								} else {
									echo '<span style="color:#8d1717">Ocurrio un error al generar el CRUD del modelo "' . $modeloNm . '", intenta de nuevo</span><br>';
								}

							} else {
								echo '<span style="color:#8d1717">Ocurrio un error no se genero el CRUD del modelo "' . $modeloNm . '"</span><br>';
							}
						} else {
							$error_msg = curl_error($ch2);
							echo '<span style="color:#8d1717">Error de comunicacion del modelo , "' . $modeloNm . '"' . $error_msg . "</span><br>";
						}

					} else {
						echo 'No hay cambios para aplicar al CRUD del modelo "' . $modeloNm . '" o verifica las configuraciones del modelo<br>';
					}
				} else {
					echo 'No hay cambios para aplicar al CRUD del modelo "' . $modeloNm . ' o hay un error en el modelo"<br>';
				}
			}
		} else {
			$error_msg = curl_error($ch);
			echo '<span style="color:#8d1717">Error de comunicacion del modelo "' . $modeloNm . '", ' . $error_msg . '</span><br>';
		}

		curl_close($ch);
	}



	function createModels($modeloNm, $tableNm)
	{
		//$this->controller->enableCsrfValidation = false;

		$dbDinamica = "db";
		$rutaArchivos = "app\models";
		$template = "Custom";
		$modelo = $modeloNm;
		$tablaName = $tableNm;

		$data = array(
			"Generator[baseClass]" => "yii\db\ActiveRecord",
			"Generator[db]" => $dbDinamica,
			"Generator[enableI18N]" => 0,
			"Generator[generateLabelsFromComments]" => 0,
			"Generator[generateQuery]" => 0,
			"Generator[generateRelationsFromCurrentSchema]" => 0,
			"Generator[generateRelationsFromCurrentSchema]" => 1,
			"Generator[generateRelations]" => "all",
			"Generator[messageCategory]" => "app",
			"Generator[modelClass]" => $modelo,
			"Generator[ns]" => $rutaArchivos,
			"Generator[queryBaseClass]" => "yii\db\ActiveQuery",
			"Generator[queryNs]" => $rutaArchivos,
			"Generator[singularize]" => 0,
			"Generator[standardizeCapitals]" => 0,
			"Generator[tableName]" => $tablaName,
			"Generator[template]" => $template,
			"Generator[useSchemaName]" => 0,
			"Generator[useSchemaName]" => 1,
			"Generator[useTablePrefix]" => 0,
			"_csrf" => Yii::$app->request->csrfToken,
			//"answers[30bf2df9d8a43eda9ae09c8efe48e517]" => 1,
			"preview" => "",
		);

		//print_r($data);
		$url = Url::home(true) . "?r=gii/default/view&id=model";
		//echo $url."<br>";

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
		$response = curl_exec($ch);
		//
		//print_r($response);
		if ($response) {
			//print_r($response);
			$regexInvalid = '|(<div class="invalid-feedback">(.+?)</div>)|';
			if (preg_match_all($regexInvalid, $response, $listInvalid)) {
				echo '<span style="color:#8d1717">Hay un error de sintaxis en el modelo "' . $modeloNm . '"</span><br>';
				//print_r($response);
				if (isset($listInvalid[0][0])) {
					echo $listInvalid[0][0];
					//echo "<br>";
				}
			} else {
				$regex = '|(?<=answers\[).+?(?=\])|';
				if (preg_match_all($regex, $response, $list)) {
					if (isset($list[0][0])) {

						//print_r($list[0][0]);
						$send = array(
							"Generator[baseClass]" => "yii\db\ActiveRecord",
							"Generator[db]" => $dbDinamica,
							"Generator[enableI18N]" => 0,
							"Generator[generateLabelsFromComments]" => 0,
							"Generator[generateQuery]" => 0,
							"Generator[generateRelationsFromCurrentSchema]" => 0,
							"Generator[generateRelationsFromCurrentSchema]" => 1,
							"Generator[generateRelations]" => "all",
							"Generator[messageCategory]" => "app",
							"Generator[modelClass]" => $modelo,
							"Generator[ns]" => $rutaArchivos,
							"Generator[queryBaseClass]" => "yii\db\ActiveQuery",
							"Generator[queryNs]" => $rutaArchivos,
							"Generator[singularize]" => 0,
							"Generator[standardizeCapitals]" => 0,
							"Generator[tableName]" => $tablaName,
							"Generator[template]" => $template,
							"Generator[useSchemaName]" => 0,
							"Generator[useSchemaName]" => 1,
							"Generator[useTablePrefix]" => 0,
							"_csrf" => Yii::$app->request->csrfParam,
							"answers[" . trim($list[0][0]) . "]" => 1,
							"generate" => "",
						);
						$ch2 = curl_init($url);
						curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch2, CURLINFO_HEADER_OUT, true);
						curl_setopt($ch2, CURLOPT_POST, true);
						curl_setopt($ch2, CURLOPT_POSTFIELDS, $send);
						curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, false);
						curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($ch2, CURLOPT_CONNECTTIMEOUT, 15);
						$create = curl_exec($ch2);

						if ($create) {
							$regex2 = '|(<div class="alert alert-success">(.+?)</div>)|';
							if (preg_match_all($regex2, $create, $respuesta)) {
								if (isset($respuesta[0][0])) {
									if (trim($respuesta[0][0]) == '<div class="alert alert-success">The code has been generated successfully.</div>') {
										echo '<span style="color:#3a7914">El modelo "' . $modeloNm . '" se generó correctamente </span><br>';
									} else {
										echo $respuesta[0][0];
									}
								} else {
									echo '<span style="color:#8d1717">Ocurrio un error al generar el modelo "' . $modeloNm . '", intenta de nuevo</span><br>';
								}

							} else {
								echo '<span style="color:#8d1717">Ocurrio un error no se genero el modelo "' . $modeloNm . '"</span><br>' . $create;
								var_dump($respuesta);
							}
						} else {
							$error_msg = curl_error($ch2);
							echo '<span style="color:#8d1717">Error de comunicacion del modelo "' . $modeloNm . '", ' . $error_msg . '</span><br>';
						}

					} else {
						echo '<span style="color:#8d1717">Ocurrio un error con el ID del modelo "' . $modeloNm . '", intenta de nuevo</span><br>';
					}
				} else {
					echo 'No hay cambios para aplicar al modelo "' . $modeloNm . '"<br>' . $response;
				}
			}
		} else {
			$error_msg = curl_error($ch);
			echo '<span style="color:#8d1717">Error de comunicacion del modelo "' . $modeloNm . '", ' . $error_msg . "</span><br>";
		}
		curl_close($ch);

	}

	//envento
	public function setEvento($usuario, $tipoID, $componenteID, $formulario, $btID = 0, $observ)
	{

		$folio = Yii::$app->db->createCommand('Select * from Eventos order by eventoID')->queryOne();

		$folio = 0;
		if (isset($folio['folio'])) {
			$folio = $folio['folio'] + 1;
		}



		$eventoID = Yii::$app->db->createCommand('SELECT * FROM TiposEventos where tipoEventoID="' . $tipoID . '"')->queryOne();
		$generAlarma = NULL;
		if (isset($eventoID['generaAlarma'])) {
			if ($eventoID['generaAlarma'] == 1) {
				$generAlarma = 3;
			}
		}

		$establecimiento = 0;
		if (Yii::$app->globals->getEstablecimiento() != 0) {
			$establecimiento = Yii::$app->globals->getEstablecimiento();
		}


		Yii::$app->db->createCommand('INSERT INTO Eventos (folio, fecha, usuarioID, tipoEventoID, componenteAlarmaID, establecimientoID, observaciones, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion, bitacoraID) VALUES ("' . $folio . '", NOW(), "' . $usuario . '", "' . $tipoID . '", ' . $generAlarma . ', "' . $establecimiento . '", "' . $observ . '", "' . Yii::$app->globals->getVersion() . '", 1, NOW(), "' . $usuario . '", "' . $formulario . '", "' . Yii::$app->globals->getVersion() . '", "' . $btID . '")')->query();



		return 'ok';
	}


	public function getEstablecimiento()
	{
		/*
		$establecimiento = Yii::$app->db->createCommand('SELECT * FROM Establecimientos limit 1')->queryOne();

		$st = 1;
		if(isset($establecimiento['establecimientoID'])){
			$st = $establecimiento['establecimientoID'];
		}

		return $st;
		*/
		$establecimiento = "SELECT * FROM Establecimientos where activoEstablecimiento=1 and regEstado=1";
		$idEsta = Yii::$app->db->createCommand($establecimiento)->queryAll();

		$idEstab = 0;
		$numEst = 1;
		foreach ($idEsta as $dataEst) {
			//if($numEst == 1){
			$idEstab = $dataEst['establecimientoID'];
			//}

			$numEst++;
		}

		//si tiene mas que uno es un grupo y se agrega cero
		if ($numEst > 2) {
			$idEstab = 0;
		}

		return $idEstab;
	}
	//bitacora
	public function setRegistro($accion, $cambio, $tabla, $formulario)
	{

		$btID = Yii::$app->globals->setBitacora("Cambio en registros", "Se realiza cambios en los registros " . $tabla, 0, "", Yii::$app->user->identity->usuarioID, 6);

		Yii::$app->globals->setEvento(Yii::$app->user->identity->usuarioID, 9, 0, $formulario, $btID, "Se realiza cambios en los registros " . $tabla);
		//setEvento($usuario, $tipoID, $componenteID, $formulario, $btID=0, $observ)


		Yii::$app->db->createCommand('INSERT INTO RegistroAuditoriaAplicaciones(accionID, tablaModificada, fechaBitacora, formularioID, usuarioID, establecimientoID, versionAplicacionID, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion, bitacoraID) VALUES ("' . $accion . '", "' . $tabla . '", NOW(), "' . $formulario . '", "' . Yii::$app->user->identity->usuarioID . '", "1", "' . Yii::$app->globals->getVersion() . '", "1", 1, NOW(), "' . Yii::$app->user->identity->usuarioID . '", "1",  "' . Yii::$app->globals->getVersion() . '", "' . $btID . '")')->query();
		$id = Yii::$app->db->getLastInsertID();

		//consulta de datos		
		//bombas, mangueras, dispensarios, calibracion, configuraciones interfaz, configuraciones red, config pass, empleados,



		return $id;
	}


	public function setRmodifica($rauditoria, $campo, $valor)
	{
		Yii::$app->db->createCommand('INSERT INTO RegistroAuditoriaAplicacionesDetalle(registroAuditoriaAplicacionID, campo, valor) VALUES ("' . $rauditoria . '", "' . $campo . '", "' . $valor . '")')->query();

		return 'ok';
	}

	public function getPaginasControlador()
	{
		$paginas = Yii::$app->db->createCommand("SELECT paginaControlador FROM PaginasControlador where regEstado=1 and activoPaginaControlador=1 order by paginaControladorID")->queryAll();

		$paginasArray = array();

		foreach ($paginas as $row) {
			$paginasArray[] = $row['paginaControlador'];
		}

		if (count($paginasArray) == 0) {
			//return array('createform', 'getselect', 'getcombo', 'getdatacombo', 'index', 'create', 'delete', 'deletedata', 'update', 'xportexcel', 'xportpdf');
			return $paginasArray;
		} else {
			return $paginasArray;
		}

	}

	public function getPermisoControlador($formularioID, $usuarioIdToken, $r)
	{


		$permisosBtn = Yii::$app->db->createCommand("SELECT Acciones.paginaAccion, Acciones.accionID, Acciones.nombreAccion, Formularios.nombreFormulario, Formularios.tipoFormularioID, Formularios.urlArchivo FROM Acciones
			inner join AccionesFormularios on Acciones.accionID=AccionesFormularios.accionID 
			inner join Formularios on AccionesFormularios.formularioID=Formularios.formularioID
			inner join PerfilAccionFormulario on AccionesFormularios.accionFormularioID =PerfilAccionFormulario.accionFormularioID
			inner join PerfilesCompuestos on PerfilAccionFormulario.perfilID=PerfilesCompuestos.perfilID
			inner join Usuarios on Usuarios.usuarioID = PerfilesCompuestos.usuarioID
			where md5(Formularios.formularioID)='" . $formularioID . "' and AccionesFormularios.estadoAccion=1 and AccionesFormularios.regEstado=1 and PerfilAccionFormulario.regEstado=1 and PerfilAccionFormulario.activoPerfilAccionFormulario=1 and Usuarios.usuarioID='" . $usuarioIdToken . "' group by AccionesFormularios.accionFormularioID order by accionID")->queryAll();

		$modeloActual = "";
		$urlPagina = explode("/", $r);
		if (isset($urlPagina[0])) {
			$modeloActual = $urlPagina[0];
		}

		$actions = array();
		foreach ($permisosBtn as $pbrow) {
			if (isset($pbrow['accionID'])) {
				$urlSeguridad = explode("/", $pbrow['urlArchivo']);
				$modeloConsulta = "";
				if (isset($urlSeguridad[0])) {
					$modeloConsulta = $urlSeguridad[0];
				}

				if ($modeloActual == $modeloConsulta) {
					$actions[] = $pbrow['paginaAccion'];
					if ($pbrow['accionID'] == '4') {
						$actions[] = 'deletedata';
					}
				}
			}
		}

		return $actions;
	}

	public function getPaginaInicialControlador()
	{
		return array('createform', 'getselect', 'getcombo', 'getdatacombo');
	}

	public function getFormulario($f)
	{
		$formulario = Yii::$app->db->createCommand('SELECT formularioID FROM Formularios WHERE md5(formularioID)="' . $f . '"')->queryOne();

		if (isset($formulario['formularioID'])) {
			return $formulario['formularioID'];
		} else {
			return 1;
		}

	}

	public function getFormularioToken($menu)
	{

		$usuarioIdToken = 0;
		$formularioID = 1;
		if (isset(Yii::$app->user->identity->usuarioID)) {
			$usuarioIdToken = Yii::$app->user->identity->usuarioID;
		}

		$permisosBtn = Yii::$app->db->createCommand("SELECT Acciones.accionID, Acciones.nombreAccion, Formularios.nombreFormulario, Formularios.tipoFormularioID, Formularios.urlArchivo, Formularios.formularioID FROM Acciones
			inner join AccionesFormularios on Acciones.accionID=AccionesFormularios.accionID 
			inner join Formularios on AccionesFormularios.formularioID=Formularios.formularioID
			inner join PerfilAccionFormulario on AccionesFormularios.accionFormularioID =PerfilAccionFormulario.accionFormularioID
			inner join PerfilesCompuestos on PerfilAccionFormulario.perfilID=PerfilesCompuestos.perfilID
			inner join Usuarios on Usuarios.usuarioID = PerfilesCompuestos.usuarioID
			where Formularios.urlArchivo='" . $menu . "' and AccionesFormularios.estadoAccion=1 and AccionesFormularios.regEstado=1 and PerfilAccionFormulario.regEstado=1 and PerfilAccionFormulario.activoPerfilAccionFormulario=1 and Usuarios.usuarioID='" . $usuarioIdToken . "' group by AccionesFormularios.accionFormularioID")->queryAll();

		foreach ($permisosBtn as $dataPbtn) {
			$urlSeguridad = explode("/", $dataPbtn['urlArchivo']);
			if (isset($urlSeguridad[0])) {
				$formularioID = $dataPbtn['formularioID'];
			}
		}

		return $formularioID;
	}

	//finaliza bitacora

	//funcion para los search del index
	public function getIndexSearch($catalogo)
	{
		$catalogoIndex = Yii::$app->db->createCommand("SELECT * FROM Catalogos where regEstado='1' and nombreCatalogo='" . $catalogo . "'")->queryOne();
		$arrayGrid = array();
		$showSearch = 0;

		if (isset($catalogoIndex['catalogoID'])) {
			$camposIndex = Yii::$app->db->createCommand("SELECT * FROM CamposGrid where regEstado='1' and catalogoID='" . $catalogoIndex['catalogoID'] . "' order by orden ASC")->queryAll();

			foreach ($camposIndex as $indexCampos) {
				if ($indexCampos['searchVisible'] == 1) {
					$showSearch++;
				}
			}

		}

		return $showSearch;
	}


	//funcion para obtener los campos dependientes form
	public function getFormDependientes($catalogoID, $campoID)
	{
		return Yii::$app->db->createCommand("SELECT CombosAnidados.controlQuery, CombosAnidados.campoIDdependiente, CombosAnidados.queryValue, CombosAnidados.queryText, Campos.nombreCampo, CombosAnidados.parametrosQuery FROM CombosAnidados inner join Campos on Campos.campoID = CombosAnidados.campoIDPadre where CombosAnidados.catalogoID='" . $catalogoID . "' and campoIDdependiente='" . $campoID . "'")->queryOne();
	}

	//funcion para obtener los campos grid
	public function getCamposGrid($catalogoID)
	{
		return Yii::$app->db->createCommand("SELECT * FROM CamposGrid where regEstado='1' and catalogoID='" . $catalogoID . "' order by orden ASC")->queryAll();
	}

	public function getCamposForm($catalogoID)
	{
		return Yii::$app->db->createCommand("SELECT * FROM Campos where regEstado='1' and catalogoID='" . $catalogoID . "' order by orden ASC")->queryAll();
	}

	//funcion para obtener los datos de un catalogo
	public function getCatalogo($nombreCatalogo)
	{
		return Yii::$app->db->createCommand("SELECT * FROM Catalogos where regEstado='1' and nombreCatalogo='" . $nombreCatalogo . "'")->queryOne();
	}

	//inicia los botones para seguridad
	public function getActionButton($idForm, $modName, $nameForm, $frmSeguridad, $page, $delMultiple, $urlData = "")
	{
		$usuarioIdToken = 0;
		$echo = "";

		if (isset(Yii::$app->user->identity->usuarioID)) {
			$usuarioIdToken = Yii::$app->user->identity->usuarioID;
		}

		$permisosBtn = Yii::$app->db->createCommand("SELECT Acciones.textoID as textoAccion, Acciones.accionID, Acciones.nombreAccion, Formularios.nombreFormulario, Formularios.icono,  Formularios.textoID, Formularios.tipoFormularioID, Formularios.urlArchivo, Acciones.imagen FROM Acciones
		inner join AccionesFormularios on Acciones.accionID=AccionesFormularios.accionID 
		inner join Formularios on AccionesFormularios.formularioID=Formularios.formularioID
		inner join PerfilAccionFormulario on AccionesFormularios.accionFormularioID =PerfilAccionFormulario.accionFormularioID
		inner join PerfilesCompuestos on PerfilAccionFormulario.perfilID=PerfilesCompuestos.perfilID
		inner join Usuarios on Usuarios.usuarioID = PerfilesCompuestos.usuarioID
		where md5(Formularios.formularioID)='" . $idForm . "' and AccionesFormularios.estadoAccion=1 and AccionesFormularios.regEstado=1 and PerfilAccionFormulario.regEstado=1 and PerfilAccionFormulario.activoPerfilAccionFormulario=1 and Usuarios.usuarioID='" . $usuarioIdToken . "' group by AccionesFormularios.accionFormularioID")->queryAll();


		$perCons = 0;
		$perAlta = 0;
		$perElim = 0;
		$perEdit = false;
		$perElimckh = false;
		$perExcel = 0;
		$perPdf = 0;
		$txtID = 1;
		$iconForm = 'pe-7s-lock';
		$perPass = 0;
		$perEliminados = 0;

		$iAdd = "fa fa-plus";
		$iSeacrh = "fa fa-search";
		$iDelete = "fa fa-trash";
		$iExcel = "fa fa-file-excel";
		$iPdf = "fa fa-file-pdf";
		$iElimina = "fa fa-trash";

		$iUpdate = "fa fa-pencil-alt";
		$iPass = "fa fa-unlock-alt";

		$btnAdiciones = array();
		foreach ($permisosBtn as $dataPbtn) {
			$urlSeguridad = explode("/", $dataPbtn['urlArchivo']);
			if (isset($urlSeguridad[0]) and isset($frmSeguridad[0])) {
				if ($frmSeguridad[0] == $urlSeguridad[0]) {
					if (isset($dataPbtn['accionID'])) {
						if ($dataPbtn['accionID'] == '1') {
							$perPass = 1;
						}
						if ($dataPbtn['accionID'] == '2') {
							$perCons = 1;
							$iSeacrh = $dataPbtn['imagen'];
						}
						if ($dataPbtn['accionID'] == '3') {
							$perAlta = 1;
							$iAdd = $dataPbtn['imagen'];
						}
						if ($dataPbtn['accionID'] == '4') {
							$perElim = 1;
							$perElimckh = true;
							$iDelete = $dataPbtn['imagen'];
						}
						if ($dataPbtn['accionID'] == '5') {
							$perEdit = true;
						}
						if ($dataPbtn['accionID'] == '6') {
							$perExcel = 1;
							$iExcel = $dataPbtn['imagen'];
						}
						if ($dataPbtn['accionID'] == '7') {
							$perPdf = 1;
							$iPdf = $dataPbtn['imagen'];
						}
						if ($dataPbtn['accionID'] == '9') {
							$perEliminados = 1;
							$iElimina = $dataPbtn['imagen'];
						}


						$txtID = $dataPbtn['textoID'];
						$iconForm = $dataPbtn['icono'];
					}
				}
			}
		}

		$pageTxt = "";

		if ($page == 'index') {
			$pageTxt = $this->getTraductor(18, Yii::$app->session['idiomaId'], 'Consulta');
		} elseif ($page == 'create') {
			$pageTxt = Yii::$app->globals->getTraductor(2, Yii::$app->session['idiomaId'], 'Alta');
		} elseif ($page == 'update' or $page == 'pass') {
			$pageTxt = Yii::$app->globals->getTraductor(4, Yii::$app->session['idiomaId'], 'Editar Registro');
		}

		$echo .= '<div class="app-page-title">
					<div class="page-title-wrapper">';

		$echo .= '<div class="page-title-heading">
					<div class="page-title-icon">
						<i class="' . $iconForm . ' ' . $this->btnAcciones() . '"></i>
					</div>
					<div>
						' . $this->getTraductor($txtID, Yii::$app->session['idiomaId'], $nameForm) . '				
						<div class="page-title-subheading">' . $pageTxt . '</div>
					</div>
				</div>';
		$echo .= '<div class="page-title-actions">';
		$formMenu = Yii::$app->db->createCommand("SELECT * FROM Formularios where md5(formularioID)='" . $idForm . "' and regEstado=1 and estadoFormulario=1")->queryOne();

		$tipoMenu = "";
		if (isset($formMenu['tipoMenu'])) {
			$tipoMenu = $formMenu['tipoMenu'];
		}
		if ($tipoMenu == 'Submenu') {

			$echo .= '<div style="float: right;">';
			$formSubmenus = Yii::$app->db->createCommand("SELECT * FROM Formularios where formID='" . $formMenu['formID'] . "' and regEstado=1 and estadoFormulario=1")->queryAll();

			foreach ($formSubmenus as $rsubMenu) {
				$echo .= Html::a('<i class="' . $rsubMenu['icono'] . '"></i> ' . $this->getTraductor($rsubMenu['textoID'], Yii::$app->session['idiomaId'], $rsubMenu['nombreFormulario']), $url = [$rsubMenu['urlArchivo'] . '&f=' . md5($rsubMenu['formularioID'])], $options = ['style' => 'border-top-left-radius: 0; border-top-right-radius: 0;', 'class' => 'btn-shadow btn ' . $this->btnMenu() . ' mr-3 active']);
			}

			$echo .= '</div>';
			$echo .= '<div style="clear: both;"></div>';
			$echo .= '<div style="margin-top: 20px; float: right;">';
			//Consulta
			if ($perCons == 1 and ($page == 'index' or $page == 'create' or $page == 'update' or $page == 'otro' or $page == 'pass')) {
				$echo .= Html::a('<i class="' . $iSeacrh . '"></i> ' . $this->getTraductor(18, Yii::$app->session['idiomaId'], 'Consulta'), $url = [$modName . '/index&f=' . $idForm], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
			}
			//Alta
			if ($perAlta == 1 and ($page == 'index' or $page == 'create' or $page == 'update' or $page == 'otro' or $page == 'pass')) {
				$echo .= Html::a('<i class="' . $iAdd . '" aria-hidden="true"></i> ' . $this->getTraductor(2, Yii::$app->session['idiomaId'], 'Alta'), $url = [$modName . '/create&f=' . $idForm], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
			}
			//Eliminar
			if ($delMultiple == 1) {
				if ($perElim == 1 and $page == 'index') {
					$echo .= Html::button('<i class="' . $iDelete . '" aria-hidden="true"></i> ' . $this->getTraductor(3, Yii::$app->session['idiomaId'], 'Eliminar'), ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones(), 'onclick' => 'getRows()']);
				}
			}

			if ($page == 'update') {
				if ($perElim == 1) {
					$echo .= Html::a('<i class="' . $iDelete . '" aria-hidden="true"></i> ' . $this->getTraductor(3, Yii::$app->session['idiomaId'], 'Eliminar'), $url = [$modName . '/deletedata&f=' . $_GET['f'] . $urlData], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
				}


				if ($perPass == 1) {
					$echo .= Html::a('<i class="' . $iPass . '" aria-hidden="true"></i> ' . $this->getTraductor(1, Yii::$app->session['idiomaId'], 'Contraseña'), $url = [$modName . '/pass&f=' . $_GET['f'] . $urlData], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
				}
			}

			if ($page == 'pass') {
				if ($perElim == 1) {
					$echo .= Html::a('<i class="' . $iDelete . '" aria-hidden="true"></i> ' . $this->getTraductor(3, Yii::$app->session['idiomaId'], 'Eliminar'), $url = [$modName . '/deletedata&f=' . $_GET['f'] . $urlData], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
				}

				$echo .= Html::a('<i class="' . $iUpdate . '" aria-hidden="true"></i> ' . $this->getTraductor(21, Yii::$app->session['idiomaId'], 'Editar'), $url = [$modName . '/update&f=' . $_GET['f'] . $urlData], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
			}


			if ($perExcel == 1 and $page == 'index') {
				$echo .= Html::a('<i class="' . $iExcel . '"></i> ' . $this->getTraductor(22, Yii::$app->session['idiomaId'], 'Excel'), $url = [$modName . '/xportexcel&f=' . $idForm], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
			}

			if ($perPdf == 1 and $page == 'index') {
				$echo .= Html::a('<i class="' . $iPdf . '"></i> ' . $this->getTraductor(23, Yii::$app->session['idiomaId'], 'PDF'), $url = [$modName . '/xportpdf&f=' . $idForm], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones(), 'target' => '_blank']);
			}

			if ($perEliminados == 1 and $page == 'index') {
				$echo .= Html::a('<i class="' . $iElimina . '"></i> ' . $this->getTraductor(0, Yii::$app->session['idiomaId'], 'Eliminados'), $url = [$modName . '/eliminados&f=' . $idForm], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
			}


			$echo .= '</div>';

		} else {
			//Consulta

			if ($perCons == 1 and ($page == 'index' or $page == 'create' or $page == 'update' or $page == 'otro' or $page == 'pass')) {
				$echo .= Html::a('<i class="' . $iSeacrh . '"></i> ' . $this->getTraductor(18, Yii::$app->session['idiomaId'], 'Consulta'), $url = [$modName . '/index&f=' . $idForm], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
			}
			//Alta
			if ($perAlta == 1 and ($page == 'index' or $page == 'create' or $page == 'update' or $page == 'otro' or $page == 'pass')) {
				$echo .= Html::a('<i class="' . $iAdd . '" aria-hidden="true"></i> ' . $this->getTraductor(2, Yii::$app->session['idiomaId'], 'Alta'), $url = [$modName . '/create&f=' . $idForm], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
			}
			//Eliminar	
			if ($delMultiple == 1) {
				if ($perElim == 1 and $page == 'index') {
					$echo .= Html::button('<i class="' . $iDelete . '" aria-hidden="true"></i> ' . $this->getTraductor(3, Yii::$app->session['idiomaId'], 'Eliminar'), ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones(), 'onclick' => 'getRows()']);
				}
			}

			if ($page == 'update') {
				if ($perElim == 1) {
					$echo .= Html::a('<i class="' . $iDelete . '" aria-hidden="true"></i> ' . $this->getTraductor(3, Yii::$app->session['idiomaId'], 'Eliminar'), $url = [$modName . '/deletedata&f=' . $_GET['f'] . $urlData], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
				}


				if ($perPass == 1) {
					$echo .= Html::a('<i class="' . $iPass . '" aria-hidden="true"></i> ' . $this->getTraductor(1, Yii::$app->session['idiomaId'], 'Contraseña'), $url = [$modName . '/pass&f=' . $_GET['f'] . $urlData], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
				}
			}

			if ($page == 'pass') {
				if ($perElim == 1) {
					$echo .= Html::a('<i class="' . $iDelete . '" aria-hidden="true"></i> ' . $this->getTraductor(3, Yii::$app->session['idiomaId'], 'Eliminar'), $url = [$modName . '/deletedata&f=' . $_GET['f'] . $urlData], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
				}

				$echo .= Html::a('<i class="' . $iUpdate . '" aria-hidden="true"></i> ' . $this->getTraductor(21, Yii::$app->session['idiomaId'], 'Editar'), $url = [$modName . '/update&f=' . $_GET['f'] . $urlData], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
			}


			if ($perExcel == 1 and $page == 'index') {
				$echo .= Html::a('<i class="' . $iExcel . '"></i> ' . $this->getTraductor(22, Yii::$app->session['idiomaId'], 'Excel'), $url = [$modName . '/xportexcel&f=' . $idForm], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
			}

			if ($perPdf == 1 and $page == 'index') {
				$echo .= Html::a('<i class="' . $iPdf . '"></i> ' . $this->getTraductor(23, Yii::$app->session['idiomaId'], 'PDF'), $url = [$modName . '/xportpdf&f=' . $idForm], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones(), 'target' => '_blank']);
			}

			if ($perEliminados == 1 and $page == 'index') {
				$echo .= Html::a('<i class="' . $iElimina . '"></i> ' . $this->getTraductor(0, Yii::$app->session['idiomaId'], 'Eliminados'), $url = [$modName . '/eliminados&f=' . $idForm], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
			}
		}
		$echo .= '</div></div></div>';


		return array('botones' => $echo, 'visible' => $perElimckh, 'perEdit' => $perEdit);
	}
	public function getActionButtonV2($idForm, $modName, $nameForm, $frmSeguridad, $page, $delMultiple, $urlData = "", $replaceidform, $replacemodname)
	{
		$usuarioIdToken = 0;
		$echo = "";

		if (isset(Yii::$app->user->identity->usuarioID)) {
			$usuarioIdToken = Yii::$app->user->identity->usuarioID;
		}


		$permisosBtn = Yii::$app->db->createCommand("SELECT Acciones.textoID as textoAccion, Acciones.accionID, Acciones.nombreAccion, Formularios.nombreFormulario, Formularios.icono,  Formularios.textoID, Formularios.tipoFormularioID, Formularios.urlArchivo, Acciones.imagen FROM Acciones
		inner join AccionesFormularios on Acciones.accionID=AccionesFormularios.accionID 
		inner join Formularios on AccionesFormularios.formularioID=Formularios.formularioID
		inner join PerfilAccionFormulario on AccionesFormularios.accionFormularioID =PerfilAccionFormulario.accionFormularioID
		inner join PerfilesCompuestos on PerfilAccionFormulario.perfilID=PerfilesCompuestos.perfilID
		inner join Usuarios on Usuarios.usuarioID = PerfilesCompuestos.usuarioID
		where md5(Formularios.formularioID)='" . $idForm . "' and AccionesFormularios.estadoAccion=1 and AccionesFormularios.regEstado=1 and PerfilAccionFormulario.regEstado=1 and PerfilAccionFormulario.activoPerfilAccionFormulario=1 and Usuarios.usuarioID='" . $usuarioIdToken . "' group by AccionesFormularios.accionFormularioID")->queryAll();


		$perCons = 0;
		$perAlta = 0;
		$perElim = 0;
		$perEdit = false;
		$perElimckh = false;
		$perExcel = 0;
		$perPdf = 0;
		$txtID = 1;
		$iconForm = 'pe-7s-lock';
		$perPass = 0;
		$perEliminados = 0;

		$iAdd = "fa fa-plus";
		$iSeacrh = "fa fa-search";
		$iDelete = "fa fa-trash";
		$iExcel = "fa fa-file-excel";
		$iPdf = "fa fa-file-pdf";
		$iElimina = "fa fa-trash";

		$iUpdate = "fa fa-pencil-alt";
		$iPass = "fa fa-unlock-alt";

		$btnAdiciones = array();
		foreach ($permisosBtn as $dataPbtn) {
			$urlSeguridad = explode("/", $dataPbtn['urlArchivo']);
			if (isset($urlSeguridad[0]) and isset($frmSeguridad[0])) {
				if ($frmSeguridad[0] == $urlSeguridad[0]) {
					if (isset($dataPbtn['accionID'])) {
						if ($dataPbtn['accionID'] == '1') {
							$perPass = 1;
						}
						if ($dataPbtn['accionID'] == '2') {
							$perCons = 1;
							$iSeacrh = $dataPbtn['imagen'];
						}
						if ($dataPbtn['accionID'] == '3') {
							$perAlta = 1;
							$iAdd = $dataPbtn['imagen'];
						}
						if ($dataPbtn['accionID'] == '4') {
							$perElim = 1;
							$perElimckh = true;
							$iDelete = $dataPbtn['imagen'];
						}
						if ($dataPbtn['accionID'] == '5') {
							$perEdit = true;
						}
						if ($dataPbtn['accionID'] == '6') {
							$perExcel = 1;
							$iExcel = $dataPbtn['imagen'];
						}
						if ($dataPbtn['accionID'] == '7') {
							$perPdf = 1;
							$iPdf = $dataPbtn['imagen'];
						}
						if ($dataPbtn['accionID'] == '9') {
							$perEliminados = 1;
							$iElimina = $dataPbtn['imagen'];
						}


						$txtID = $dataPbtn['textoID'];
						$iconForm = $dataPbtn['icono'];
					}
				}
			}
		}

		$pageTxt = "";

		if ($page == 'index') {
			$pageTxt = $this->getTraductor(18, Yii::$app->session['idiomaId'], 'Consulta');
		} elseif ($page == 'create') {
			$pageTxt = Yii::$app->globals->getTraductor(2, Yii::$app->session['idiomaId'], 'Alta');
		} elseif ($page == 'update' or $page == 'pass') {
			$pageTxt = Yii::$app->globals->getTraductor(4, Yii::$app->session['idiomaId'], 'Editar Registro');
		}

		$echo .= '<div class="app-page-title">
					<div class="page-title-wrapper">';

		$echo .= '<div class="page-title-heading">
					<div class="page-title-icon">
						<i class="' . $iconForm . ' icon-gradient bg-malibu-beach"></i>
					</div>
					<div>
						' . $this->getTraductor($txtID, Yii::$app->session['idiomaId'], $nameForm) . '				
						<div class="page-title-subheading">' . $pageTxt . '</div>
					</div>
				</div>';
		$echo .= '<div class="page-title-actions">';
		$formMenu = Yii::$app->db->createCommand("SELECT * FROM Formularios where md5(formularioID)='" . $replaceidform . "' and regEstado=1 and estadoFormulario=1")->queryOne();
		$tipoMenu = "";
		if (isset($formMenu['tipoMenu'])) {
			$tipoMenu = $formMenu['tipoMenu'];
		}
		if ($tipoMenu == 'Submenu') {

			$echo .= '<div style="margin-top: -30px; float: right;">';
			$formSubmenus = Yii::$app->db->createCommand("SELECT * FROM Formularios where formID='" . $formMenu['formID'] . "' and regEstado=1 and estadoFormulario=1")->queryAll();

			foreach ($formSubmenus as $rsubMenu) {
				$echo .= Html::a('<i class="' . $rsubMenu['icono'] . '"></i> ' . $this->getTraductor($rsubMenu['textoID'], Yii::$app->session['idiomaId'], $rsubMenu['nombreFormulario']), $url = [$rsubMenu['urlArchivo'] . '&f=' . md5($rsubMenu['formularioID'])], $options = ['style' => 'border-top-left-radius: 0; border-top-right-radius: 0;', 'class' => 'btn-shadow btn ' . $this->btnMenu() . ' mr-3 active']);
			}

			$echo .= '</div>';
			$echo .= '<div style="clear: both;"></div>';
			$echo .= '<div style="margin-top: 20px; float: right;">';
			//Consulta
			if ($perCons == 1 and ($page == 'index' or $page == 'create' or $page == 'update' or $page == 'otro' or $page == 'pass')) {
				$echo .= Html::a('<i class="' . $iSeacrh . '"></i> ' . $this->getTraductor(18, Yii::$app->session['idiomaId'], 'Consulta'), $url = [$modName . '/index&f=' . $idForm], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
			}
			//Alta
			if ($perAlta == 1 and ($page == 'index' or $page == 'create' or $page == 'update' or $page == 'otro' or $page == 'pass')) {
				$echo .= Html::a('<i class="' . $iAdd . '" aria-hidden="true"></i> ' . $this->getTraductor(2, Yii::$app->session['idiomaId'], 'Alta'), $url = [$modName . '/create&f=' . $replaceidform], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
			}
			//Eliminar
			if ($delMultiple == 1) {
				if ($perElim == 1 and $page == 'index') {
					$echo .= Html::button('<i class="' . $iDelete . '" aria-hidden="true"></i> ' . $this->getTraductor(3, Yii::$app->session['idiomaId'], 'Eliminar'), ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones(), 'onclick' => 'getRows()']);
				}
			}

			if ($page == 'update') {
				if ($perElim == 1) {
					$echo .= Html::a('<i class="' . $iDelete . '" aria-hidden="true"></i> ' . $this->getTraductor(3, Yii::$app->session['idiomaId'], 'Eliminar'), $url = [$modName . '/deletedata&f=' . $replaceidform . $urlData], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
				}


				if ($perPass == 1) {
					$echo .= Html::a('<i class="' . $iPass . '" aria-hidden="true"></i> ' . $this->getTraductor(1, Yii::$app->session['idiomaId'], 'Contraseña'), $url = [$modName . '/pass&f=' . $replaceidform . $urlData], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
				}
			}

			if ($page == 'pass') {
				if ($perElim == 1) {
					$echo .= Html::a('<i class="' . $iDelete . '" aria-hidden="true"></i> ' . $this->getTraductor(3, Yii::$app->session['idiomaId'], 'Eliminar'), $url = [$modName . '/deletedata&f=' . $replaceidform . $urlData], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
				}

				$echo .= Html::a('<i class="' . $iUpdate . '" aria-hidden="true"></i> ' . $this->getTraductor(21, Yii::$app->session['idiomaId'], 'Editar'), $url = [$modName . '/update&f=' . $replaceidform . $urlData], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
			}


			if ($perExcel == 1 and $page == 'index') {
				$echo .= Html::a('<i class="' . $iExcel . '"></i> ' . $this->getTraductor(22, Yii::$app->session['idiomaId'], 'Excel'), $url = [$modName . '/xportexcel&f=' . $replaceidform], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
			}

			if ($perPdf == 1 and $page == 'index') {
				$echo .= Html::a('<i class="' . $iPdf . '"></i> ' . $this->getTraductor(23, Yii::$app->session['idiomaId'], 'PDF'), $url = [$modName . '/xportpdf&f=' . $replaceidform], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones(), 'target' => '_blank']);
			}

			if ($perEliminados == 1 and $page == 'index') {
				$echo .= Html::a('<i class="' . $iElimina . '"></i> ' . $this->getTraductor(0, Yii::$app->session['idiomaId'], 'Eliminados'), $url = [$modName . '/eliminados&f=' . $replaceidform], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
			}


			$echo .= '</div>';

		} else {
			//Consulta

			if ($perCons == 1 and ($page == 'index' or $page == 'create' or $page == 'update' or $page == 'otro' or $page == 'pass')) {
				$echo .= Html::a('<i class="' . $iSeacrh . '"></i> ' . $this->getTraductor(18, Yii::$app->session['idiomaId'], 'Consulta'), $url = [$replacemodname . '/index&f=' . $idForm], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
			}
			//Alta
			if ($perAlta == 1 and ($page == 'index' or $page == 'create' or $page == 'update' or $page == 'otro' or $page == 'pass')) {
				$echo .= Html::a('<i class="' . $iAdd . '" aria-hidden="true"></i> ' . $this->getTraductor(2, Yii::$app->session['idiomaId'], 'Alta'), $url = [$modName . '/create&f=' . $replaceidform], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
			}
			//Eliminar	
			if ($delMultiple == 1) {
				if ($perElim == 1 and $page == 'index') {
					$echo .= Html::button('<i class="' . $iDelete . '" aria-hidden="true"></i> ' . $this->getTraductor(3, Yii::$app->session['idiomaId'], 'Eliminar'), ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones(), 'onclick' => 'getRows()']);
				}
			}

			if ($page == 'update') {
				if ($perElim == 1) {
					$echo .= Html::a('<i class="' . $iDelete . '" aria-hidden="true"></i> ' . $this->getTraductor(3, Yii::$app->session['idiomaId'], 'Eliminar'), $url = [$modName . '/deletedata&f=' . $replaceidform . $urlData], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
				}


				if ($perPass == 1) {
					$echo .= Html::a('<i class="' . $iPass . '" aria-hidden="true"></i> ' . $this->getTraductor(1, Yii::$app->session['idiomaId'], 'Contraseña'), $url = [$modName . '/pass&f=' . $replaceidform . $urlData], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
				}
			}

			if ($page == 'pass') {
				if ($perElim == 1) {
					$echo .= Html::a('<i class="' . $iDelete . '" aria-hidden="true"></i> ' . $this->getTraductor(3, Yii::$app->session['idiomaId'], 'Eliminar'), $url = [$modName . '/deletedata&f=' . $replaceidform . $urlData], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
				}

				$echo .= Html::a('<i class="' . $iUpdate . '" aria-hidden="true"></i> ' . $this->getTraductor(21, Yii::$app->session['idiomaId'], 'Editar'), $url = [$modName . '/update&f=' . $replaceidform . $urlData], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
			}


			if ($perExcel == 1 and $page == 'index') {
				$echo .= Html::a('<i class="' . $iExcel . '"></i> ' . $this->getTraductor(22, Yii::$app->session['idiomaId'], 'Excel'), $url = [$modName . '/xportexcel&f=' . $replaceidform], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
			}

			if ($perPdf == 1 and $page == 'index') {
				$echo .= Html::a('<i class="' . $iPdf . '"></i> ' . $this->getTraductor(23, Yii::$app->session['idiomaId'], 'PDF'), $url = [$modName . '/xportpdf&f=' . $replaceidform], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones(), 'target' => '_blank']);
			}

			if ($perEliminados == 1 and $page == 'index') {
				$echo .= Html::a('<i class="' . $iElimina . '"></i> ' . $this->getTraductor(0, Yii::$app->session['idiomaId'], 'Eliminados'), $url = [$modName . '/eliminados&f=' . $replaceidform], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn ' . $this->btnAcciones()]);
			}
		}
		$echo .= '</div></div></div>';


		return array('botones' => $echo, 'visible' => $perElimckh, 'perEdit' => $perEdit);
	}

	public function getSubmenuV2($idForm)
	{
		$echo = '';
		$sql1 = "SELECT * FROM Formularios where md5(formularioID)='" . $idForm . "' and regEstado=1 and estadoFormulario=1";
		//$echo .= $sql1;
		$formMenu = Yii::$app->db->createCommand($sql1)->queryOne();
		if ($formMenu) {
			if ($formMenu['tipoMenu'] == 'Menu' || $formMenu['tipoMenu'] == 'Submenuv2') {
				$formSubmenus = Yii::$app->db->createCommand("SELECT * FROM Formularios where formID='" . $formMenu['formID'] . "' and regEstado=1 and estadoFormulario=1 and (tipoMenu='Submenuv2' or tipoMenu='Menu')")->queryAll();
				$echo .= '<div class="clsTab d-flex fz-4">';
				foreach ($formSubmenus as $rsubMenu) {
					$active = "";
					if (md5($rsubMenu['formularioID']) == $idForm) {
						$active = "active";
					}
					$echo .= '<div class="px-4 py-1 ' . $active . '">' . Html::a('<i class="' . $rsubMenu['icono'] . '"></i> ' . Yii::$app->globals->getTraductor($rsubMenu['textoID'], Yii::$app->session['idiomaId'], $rsubMenu['nombreFormulario']), $url = [$rsubMenu['urlArchivo'] . '&f=' . md5($rsubMenu['formularioID'])]);
					$echo .= '</div>';
				}
				$echo .= '</div>';
			}
		}
		return $echo;
	}


	public function btnAcciones()
	{
		return Yii::$app->session['temaBtnAccion'];
	}

	public function btnSave()
	{
		return Yii::$app->session['temaBtnSave'];
	}

	public function btnMenu()
	{
		return Yii::$app->session['temaBtnMenu'];
	}

	public function cargarTraductor($id)
	{
		$fileTraduccion = Yii::$app->basePath . '/traducciones/traduccion_' . $id . '.php';
		if (file_exists($fileTraduccion)) {
			require '../traducciones/traduccion_' . $id . '.php';
			Yii::$app->session->set('traduccionesSelct', $mensajes);
		}
	}


	public function cargarError($id)
	{
		$fileTraduccion = Yii::$app->basePath . '/traducciones/error_' . $id . '.php';
		if (file_exists($fileTraduccion)) {
			require '../traducciones/error_' . $id . '.php';
			Yii::$app->session->set('traduccionesError', $codigo);
		} else {
			require '../traducciones/error_1.php';
			Yii::$app->session->set('traduccionesError', $codigo);
		}
	}

	public function getTraductor($textoID, $idiomaID, $defaul)
	{
		if ($textoID == 1) {
			return $defaul;
		} else {
			if (isset(Yii::$app->session['traduccionesSelct'])) {
				if (isset(Yii::$app->session['traduccionesSelct'][$textoID])) {
					return Yii::$app->session['traduccionesSelct'][$textoID];
				} else {
					return $defaul;
				}
			} else {
				return $defaul;
			}
		}
	}


	public function getVersion()
	{
		$version = Yii::$app->db->createCommand('SELECT versionID FROM Versiones where versionActual=1 limit 1')->queryOne();
		return $version['versionID'];
	}

	public function genKey()
	{
		//Se define una cadena de caractares. Te recomiendo que uses esta.
		$cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
		//Obtenemos la longitud de la cadena de caracteres
		$longitudCadena = strlen($cadena);

		//Se define la variable que va a contener la contraseña
		$pass = "";
		//Se define la longitud de la contraseña, en mi caso 10, pero puedes poner la longitud que quieras
		$longitudPass = 4;

		//Creamos la contraseña
		for ($i = 1; $i <= $longitudPass; $i++) {
			//Definimos numero aleatorio entre 0 y la longitud de la cadena de caracteres-1
			$pos = rand(0, $longitudCadena - 1);

			//Vamos formando la contraseña en cada iteraccion del bucle, añadiendo a la cadena $pass la letra correspondiente a la posicion $pos en la cadena de caracteres definida.
			$pass .= substr($cadena, $pos, 1);
		}

		$fecha = date('ymd');
		return 'Key-' . $fecha . $pass;
	}


	public function genConsecutivo($empresa)
	{
		$cad = (new \yii\db\Query())->select('No_sconsecutivo')->from('tbl_siniestros')->where('Id_empresa="' . $empresa . '"')->orderBy(['Id_siniestro' => SORT_DESC])->one();

		if (isset($cad['No_sconsecutivo'])) {
			$fecha = substr($cad['No_sconsecutivo'], 0, 4);
			if ($fecha == date('ym')) {
				$num_old = explode(date('ym'), $cad['No_sconsecutivo']);
				$num_nuevo = $num_old[1] + 1;
				return date('ym') . str_pad($num_nuevo, 2, "0", STR_PAD_LEFT);
			} else {
				return date('ym') . '01';
			}
		} else {
			return date('ym') . '01';
		}
	}



	public function getMes($fecha)
	{
		$fechaComoEntero = strtotime($fecha);
		$date = date("m", $fechaComoEntero);
		return $date;
	}

	public function getDia($fecha)
	{
		$fechaComoEntero = strtotime($fecha);
		$date = date("d", $fechaComoEntero);
		return $date;
	}

	public function numFormat($num)
	{
		if ($num == 0 or $num == '') {
			return '0.00';
		} else {
			return number_format($num, 2);
		}
	}

	public function getIcons()
	{
		$icons = '<ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
						<li onclick="getIcon(this)"  class="nav-item">
                            <a role="tab" class="nav-link show active" id="tab-0" data-toggle="tab" href="#tab-content-0" aria-selected="true">
                                <span>Pe7 Icons</span>
                            </a>
                        </li>
                       
                        <li onclick="getIcon(this)"  class="nav-item">
                            <a role="tab" class="nav-link show" id="tab-2" data-toggle="tab" href="#tab-content-2" aria-selected="false">
                                <span>Linear Icons</span>
                            </a>
                        </li>
                        <li onclick="getIcon(this)"  class="nav-item">
                            <a role="tab" class="nav-link show" id="tab-3" data-toggle="tab" href="#tab-content-3" aria-selected="false">
                                <span>Ion Icons</span>
                            </a>
                        </li>
                    </ul>
					<div class="tab-content">
                        <div class="tab-pane tabs-animation fade active show" id="tab-content-0" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="main-card mb-3 card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-album"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-album</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-arc"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-arc</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-back-2"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-back-2</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-bandaid"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-bandaid</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-car"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-car</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-diamond"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-diamond</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-door-lock"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-door-lock</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-eyedropper"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-eyedropper</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-female"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-female</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-gym"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-gym</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-hammer"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-hammer</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-headphones"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-headphones</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-helm"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-helm</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-hourglass"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-hourglass</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-leaf"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-leaf</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-magic-wand"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-magic-wand</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-male"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-male</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-map-2"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-map-2</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-next-2"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-next-2</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-paint-bucket"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-paint-bucket</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-pendrive"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-pendrive</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-photo"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-photo</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-piggy"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-piggy</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-plugin"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-plugin</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-refresh-2"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-refresh-2</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-rocket"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-rocket</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-settings"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-settings</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-shield"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-shield</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-smile"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-smile</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-usb"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-usb</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-vector"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-vector</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-wine"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-wine</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-cloud-upload"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-cloud-upload</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-cash"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-cash</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-close"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-close</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-bluetooth"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-bluetooth</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-cloud-download"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-cloud-download</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-way"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-way</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-close-circle"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-close-circle</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-id"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-id</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-angle-up"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-angle-up</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-wristwatch"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-wristwatch</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-angle-up-circle"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-angle-up-circle</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-world"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-world</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-angle-right"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-angle-right</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-volume"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-volume</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-angle-right-circle"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-angle-right-circle</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-users"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-users</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-angle-left"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-angle-left</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-user-female"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-user-female</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-angle-left-circle"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-angle-left-circle</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-up-arrow"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-up-arrow</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-angle-down"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-angle-down</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-switch"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-switch</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-angle-down-circle"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-angle-down-circle</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-scissors"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-scissors</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-wallet"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-wallet</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-safe"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-safe</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-volume2"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-volume2</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-volume1"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-volume1</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-voicemail"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-voicemail</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-video"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-video</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-user"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-user</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-upload"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-upload</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-unlock"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-unlock</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-umbrella"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-umbrella</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-trash"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-trash</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-tools"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-tools</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-timer"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-timer</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-ticket"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-ticket</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-target"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-target</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-sun"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-sun</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-study"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-study</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-stopwatch"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-stopwatch</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-star"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-star</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-speaker"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-speaker</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-signal"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-signal</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-shuffle"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-shuffle</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-shopbag"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-shopbag</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-share"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-share</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-server"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-server</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-search"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-search</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-film"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-film</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-science"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-science</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-disk"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-disk</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-ribbon"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-ribbon</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-repeat"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-repeat</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-refresh"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-refresh</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-add-user"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-add-user</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-refresh-cloud"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-refresh-cloud</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-paperclip"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-paperclip</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-radio"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-radio</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-note2"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-note2</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-print"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-print</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-network"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-network</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-prev"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-prev</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-mute"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-mute</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-power"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-power</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-medal"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-medal</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-portfolio"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-portfolio</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-like2"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-like2</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-plus"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-plus</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-left-arrow"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-left-arrow</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-play"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-play</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-key"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-key</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-plane"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-plane</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-joy"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-joy</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-photo-gallery"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-photo-gallery</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-pin"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-pin</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-phone"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-phone</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-plug"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-plug</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-pen"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-pen</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-right-arrow"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-right-arrow</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-paper-plane"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-paper-plane</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-delete-user"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-delete-user</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-paint"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-paint</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-bottom-arrow"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-bottom-arrow</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-notebook"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-notebook</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-note"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-note</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-next"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-next</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-news-paper"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-news-paper</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-musiclist"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-musiclist</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-music"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-music</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-mouse"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-mouse</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-more"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-more</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-moon"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-moon</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-monitor"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-monitor</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-micro"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-micro</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-menu"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-menu</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-map"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-map</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-map-marker"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-map-marker</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-mail"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-mail</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-mail-open"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-mail-open</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-mail-open-file"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-mail-open-file</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-magnet"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-magnet</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-loop"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-loop</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-look"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-look</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-lock"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-lock</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-lintern"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-lintern</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-link"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-link</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-like"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-like</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-light"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-light</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-less"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-less</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-keypad"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-keypad</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-junk"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-junk</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-info"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-info</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-home"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-home</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-help2"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-help2</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-help1"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-help1</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-graph3"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-graph3</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-graph2"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-graph2</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-graph1"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-graph1</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-graph"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-graph</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-global"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-global</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-gleam"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-gleam</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-glasses"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-glasses</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-gift"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-gift</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-folder"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-folder</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-flag"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-flag</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-filter"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-filter</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-file"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-file</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-expand1"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-expand1</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-exapnd2"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-exapnd2</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-edit"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-edit</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-drop"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-drop</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-drawer"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-drawer</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-download"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-download</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-display2"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-display2</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-display1"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-display1</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-diskette"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-diskette</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-date"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-date</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-cup"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-cup</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-culture"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-culture</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-crop"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-crop</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-credit"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-credit</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-copy-file"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-copy-file</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-config"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-config</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-compass"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-compass</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-comment"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-comment</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-coffee"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-coffee</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-cloud"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-cloud</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-clock"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-clock</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-check"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-check</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-chat"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-chat</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-cart"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-cart</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-camera"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-camera</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-call"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-call</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-calculator"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-calculator</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-browser"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-browser</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-box2"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-box2</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-box1"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-box1</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-bookmarks"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-bookmarks</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-bicycle"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-bicycle</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-bell"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-bell</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-battery"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-battery</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-ball"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-ball</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-back"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-back</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-attention"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-attention</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-anchor"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-anchor</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-albums"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-albums</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-alarm"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-alarm</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="pe-7s-airplay"> </i>
                                                        <p onclick="imageAdd(this)" >pe-7s-airplay</p></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
				   
				   
                        <div class="tab-pane tabs-animation fade" id="tab-content-2" role="tabpanel">
                            <div class="row">
                               
                                <div class="col-md-12">
                                    <div class="main-card mb-3 card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-apartment"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-apartment</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-pencil"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-pencil</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-magic-wand"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-magic-wand</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-drop"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-drop</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-lighter"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-lighter</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-poop"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-poop</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-sun"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-sun</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-moon"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-moon</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-cloud"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-cloud</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-cloud-upload"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-cloud-upload</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-cloud-download"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-cloud-download</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-cloud-sync"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-cloud-sync</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-cloud-check"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-cloud-check</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-database"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-database</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-lock"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-lock</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-cog"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-cog</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-trash"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-trash</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-dice"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-dice</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-heart"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-heart</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-star"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-star</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-star-half"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-star-half</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-star-empty"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-star-empty</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-flag"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-flag</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-envelope"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-envelope</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-paperclip"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-paperclip</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-inbox"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-inbox</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-eye"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-eye</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-printer"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-printer</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-file-empty"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-file-empty</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-file-add"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-file-add</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-enter"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-enter</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-exit"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-exit</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-graduation-hat"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-graduation-hat</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-license"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-license</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-music-note"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-music-note</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-film-play"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-film-play</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-camera-video"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-camera-video</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-camera"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-camera</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-picture"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-picture</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-book"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-book</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-bookmark"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-bookmark</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-user"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-user</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-users"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-users</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-shirt"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-shirt</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-store"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-store</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-cart"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-cart</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-tag"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-tag</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-phone-handset"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-phone-handset</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-phone"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-phone</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-pushpin"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-pushpin</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-map-marker"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-map-marker</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-map"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-map</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-location"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-location</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-calendar-full"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-calendar-full</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-keyboard"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-keyboard</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-spell-check"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-spell-check</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-screen"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-screen</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-smartphone"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-smartphone</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-tablet"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-tablet</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-laptop"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-laptop</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-laptop-phone"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-laptop-phone</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-power-switch"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-power-switch</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-bubble"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-bubble</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-heart-pulse"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-heart-pulse</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-construction"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-construction</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-pie-chart"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-pie-chart</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-chart-bars"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-chart-bars</p></div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-gift"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-gift</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-diamond"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-diamond</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-linearicons"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-linearicons</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-dinner"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-dinner</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-coffee-cup"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-coffee-cup</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-leaf"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-leaf</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-paw"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-paw</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-rocket"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-rocket</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-briefcase"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-briefcase</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-bus"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-bus</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-car"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-car</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-train"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-train</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-bicycle"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-bicycle</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-wheelchair"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-wheelchair</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-select"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-select</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-earth"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-earth</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-smile"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-smile</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-sad"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-sad</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-neutral"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-neutral</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-mustache"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-mustache</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-alarm"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-alarm</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-bullhorn"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-bullhorn</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-volume-high"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-volume-high</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-volume-medium"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-volume-medium</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-volume-low"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-volume-low</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-volume"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-volume</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-mic"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-mic</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-hourglass"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-hourglass</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-undo"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-undo</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-redo"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-redo</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-sync"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-sync</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-history"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-history</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-clock"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-clock</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-download"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-download</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-upload"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-upload</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-enter-down"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-enter-down</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-exit-up"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-exit-up</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-bug"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-bug</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-code"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-code</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-link"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-link</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-unlink"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-unlink</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-thumbs-up"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-thumbs-up</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-thumbs-down"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-thumbs-down</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-magnifier"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-magnifier</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-cross"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-cross</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-menu"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-menu</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-list"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-list</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-chevron-up"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-chevron-up</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-chevron-down"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-chevron-down</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-chevron-left"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-chevron-left</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-chevron-right"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-chevron-right</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-arrow-up"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-arrow-up</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-arrow-down"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-arrow-down</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-arrow-left"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-arrow-left</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-arrow-right"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-arrow-right</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-move"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-move</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-warning"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-warning</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-question-circle"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-question-circle</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-menu-circle"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-menu-circle</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-checkmark-circle"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-checkmark-circle</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-cross-circle"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-cross-circle</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-plus-circle"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-plus-circle</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-circle-minus"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-circle-minus</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-arrow-up-circle"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-arrow-up-circle</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-arrow-down-circle"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-arrow-down-circle</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-arrow-left-circle"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-arrow-left-circle</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-arrow-right-circle"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-arrow-right-circle</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-chevron-up-circle"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-chevron-up-circle</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-chevron-down-circle"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-chevron-down-circle</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-chevron-left-circle"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-chevron-left-circle</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-chevron-right-circle"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-chevron-right-circle</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-crop"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-crop</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-frame-expand"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-frame-expand</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-frame-contract"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-frame-contract</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-layers"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-layers</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-funnel"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-funnel</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-text-format"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-text-format</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-text-format-remove"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-text-format-remove</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-text-size"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-text-size</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-bold"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-bold</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-italic"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-italic</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-underline"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-underline</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-strikethrough"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-strikethrough</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-highlight"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-highlight</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-text-align-left"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-text-align-left</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-text-align-center"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-text-align-center</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-text-align-right"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-text-align-right</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-text-align-justify"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-text-align-justify</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-line-spacing"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-line-spacing</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-indent-increase"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-indent-increase</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-indent-decrease"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-indent-decrease</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-pilcrow"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-pilcrow</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-direction-ltr"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-direction-ltr</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-direction-rtl"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-direction-rtl</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-page-break"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-page-break</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-sort-alpha-asc"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-sort-alpha-asc</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-sort-amount-asc"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-sort-amount-asc</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-hand"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-hand</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-pointer-up"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-pointer-up</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-pointer-right"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-pointer-right</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-pointer-down"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-pointer-down</p></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper"><i onclick="getIcon(this)"  class="lnr-pointer-left"> </i>
                                                        <p onclick="imageAdd(this)" >lnr-pointer-left</p></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane tabs-animation fade" id="tab-content-3" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="main-card mb-3 card">
                                        <div class="card-body">
        
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-add-circle"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-add-circle</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-add"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-add</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-alert"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-alert</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-apps"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-apps</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-archive"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-archive</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-arrow-back"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-arrow-back</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-arrow-down"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-arrow-down</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-arrow-dropdown-circle"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-arrow-dropdown-circle</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-arrow-dropdown"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-arrow-dropdown</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-arrow-dropleft-circle"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-arrow-dropleft-circle</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-arrow-dropleft"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-arrow-dropleft</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-arrow-dropright-circle"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-arrow-dropright-circle</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-arrow-dropright"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-arrow-dropright</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-arrow-dropup-circle"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-arrow-dropup-circle</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-arrow-dropup"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-arrow-dropup</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-arrow-forward"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-arrow-forward</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-arrow-up"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-arrow-up</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-attach"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-attach</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-boat"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-boat</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-bookmark"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-bookmark</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-bulb"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-bulb</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-bus"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-bus</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-calendar"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-calendar</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-call"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-call</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-camera"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-camera</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-car"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-car</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-cart"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-cart</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-checkbox-outline"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-checkbox-outline</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-checkbox"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-checkbox</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-checkmark-circle"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-checkmark-circle</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-clipboard"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-clipboard</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-close"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-close</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-cloud-circle"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-cloud-circle</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-cloud-done"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-cloud-done</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-cloud-outline"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-cloud-outline</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-cloud"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-cloud</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-color-palette"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-color-palette</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-compass"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-compass</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-contact"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-contact</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-contacts"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-contacts</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-contract"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-contract</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-create"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-create</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-desktop"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-desktop</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-document"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-document</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-done-all"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-done-all</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-download"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-download</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-exit"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-exit</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-expand"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-expand</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-film"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-film</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-folder-open"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-folder-open</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-folder"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-folder</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-funnel"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-funnel</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-globe"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-globe</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-hand"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-hand</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-happy"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-happy</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-home"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-home</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-list"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-list</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-locate"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-locate</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-lock"></i>
                                                        <p onclick="imageAdd(this)" >ion-android-lock</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="font-icon-wrapper">
                                                        <i onclick="getIcon(this)"  class="icon ion-android-map"></i>

                                                        <p onclick="imageAdd(this)" >ion-android-map</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
				   
                    </div>';

		return $icons;
	}


	public function getIconsLinear()
	{
		$icons = '<div class="clearfix mhl ptl">
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-home"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e600" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe600;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="home, building" class="liga unitRight"   />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-home2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e601" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe601;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="home2, building2" class="liga unitRight"   />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-home3"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e602" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe602;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="home3, building3" class="liga unitRight"   />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-home4"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e603" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe603;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="home4, building4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-home5"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e604" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe604;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="home5, building5" class="liga unitRight"   />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-home6"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e605" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe605;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="home6, building6" class="liga unitRight"    />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bathtub"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e606" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe606;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bathtub, shower" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-toothbrush"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e607" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe607;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="toothbrush, hygiene" class="liga unitRight"    />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bed"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e608" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe608;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bed, hotel" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-couch"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e609" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe609;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="couch, furniture" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-chair"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e60a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe60a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="chair, furniture2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-city"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e60b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe60b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="city, building7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-apartment"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e60c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe60c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="apartment, building8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pencil"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e60d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe60d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pencil, write" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pencil2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e60e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe60e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pencil2, write2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pen"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e60f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe60f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pen, write3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pencil3"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e610" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe610;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pencil3, write4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-eraser"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e611" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe611;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="eraser, rubber" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pencil4"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e612" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe612;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pencil4, write5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pencil5"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e613" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe613;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pencil5, write6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-feather"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e614" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe614;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="feather, write7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-feather2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e615" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe615;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="feather2, write8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-feather3"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e616" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe616;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="feather3, write9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pen2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e617" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe617;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pen2, write10" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pen-add"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e618" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe618;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pen-add" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pen-remove"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e619" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe619;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pen-remove" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-vector"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e61a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe61a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="vector, bezier" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pen3"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e61b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe61b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pen3, write11" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-blog"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e61c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe61c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="blog, write12" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-brush"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e61d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe61d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="brush, paint" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-brush2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e61e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe61e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="brush2, paint2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-spray"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e61f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe61f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="spray, paint3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-paint-roller"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e620" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe620;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="paint-roller, paint4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-stamp"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e621" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe621;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="stamp, rubber-stamp" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-tape"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e622" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe622;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="tape, adhesive" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-desk-tape"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e623" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe623;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="desk-tape, adhesive2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-texture"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e624" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe624;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="texture, design" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-eye-dropper"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e625" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe625;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="eye-dropper, color-picker" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-palette"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e626" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe626;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="palette, design2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-color-sampler"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e627" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe627;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="color-sampler, design3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bucket"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e628" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe628;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bucket, paint5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-gradient"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e629" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe629;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="gradient" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-gradient2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e62a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe62a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="gradient2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-magic-wand"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e62b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe62b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="magic-wand, tool" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-magnet"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e62c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe62c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="magnet, attract" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pencil-ruler"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e62d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe62d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pencil-ruler, design4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pencil-ruler2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e62e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe62e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pencil-ruler2, design5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-compass"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e62f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe62f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="compass, tool2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-aim"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e630" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe630;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="aim, target" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-gun"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e631" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe631;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="gun, weapon" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bottle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e632" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe632;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bottle, water" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-drop"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e633" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe633;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="drop, droplet" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-drop-crossed"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e634" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe634;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="drop-crossed, droplet2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-drop2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e635" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe635;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="drop2, droplet3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-snow"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e636" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe636;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="snow, winter" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-snow2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e637" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe637;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="snow2, winter2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-fire"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e638" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe638;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="fire, flame" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-lighter"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e639" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe639;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="lighter, fire2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-knife"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e63a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe63a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="knife, tool3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-dagger"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e63b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe63b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="dagger, weapon2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-tissue"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e63c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe63c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="tissue, napkin" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-toilet-paper"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e63d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe63d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="toilet-paper, toilet" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-poop"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e63e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe63e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="poop, toilet2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-umbrella"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e63f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe63f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="umbrella, rain" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-umbrella2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e640" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe640;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="umbrella2, rain2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-rain"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e641" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe641;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="rain3, weather" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-tornado"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e642" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe642;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="tornado, weather2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-wind"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e643" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe643;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="wind, weather3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-fan"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e644" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe644;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="fan, cooling" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-contrast"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e645" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe645;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="contrast" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-sun-small"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e646" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe646;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="sun-small, brightness" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-sun"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e647" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe647;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="sun, brightness2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-sun2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e648" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe648;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="sun2, brightness-auto" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-moon"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e649" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe649;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="moon, night" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cloud"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e64a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe64a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cloud, weather4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cloud-upload"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e64b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe64b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cloud-upload, cloud2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cloud-download"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e64c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe64c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cloud-download, cloud3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cloud-rain"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e64d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe64d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cloud-rain, weather5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cloud-hailstones"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e64e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe64e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cloud-hailstones, weather6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cloud-snow"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e64f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe64f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cloud-snow, weather7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cloud-windy"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e650" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe650;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cloud-windy, weather8" class="liga unitRight"  />

			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-sun-wind"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e651" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe651;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="sun-wind, weather9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cloud-fog"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e652" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe652;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cloud-fog, weather10" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cloud-sun"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e653" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe653;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cloud-sun, weather11" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cloud-lightning"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e654" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe654;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cloud-lightning, weather12" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cloud-sync"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e655" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe655;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cloud-sync, cloud4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cloud-lock"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e656" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe656;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cloud-lock, cloud5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cloud-gear"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e657" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe657;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cloud-gear, cloud6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cloud-alert"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e658" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe658;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cloud-alert, cloud7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cloud-check"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e659" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe659;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cloud-check, cloud8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cloud-cross"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e65a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe65a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cloud-cross, cloud9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cloud-crossed"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e65b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe65b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cloud-crossed, cloud10" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cloud-database"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e65c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe65c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cloud-database, cloud11" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-database"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e65d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe65d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="database, storage" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-database-add"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e65e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe65e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="database-add, database2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-database-remove"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e65f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe65f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="database-remove, database3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-database-lock"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e660" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe660;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="database-lock, database4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-database-refresh"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e661" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe661;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="database-refresh, database5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-database-check"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e662" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe662;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="database-check, database6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-database-history"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e663" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe663;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="database-history, database7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-database-upload"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e664" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe664;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="database-upload, database8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-database-download"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e665" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe665;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="database-download, database9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-server"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e666" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe666;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="server, hosting" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-shield"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e667" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe667;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="shield, security" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-shield-check"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e668" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe668;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="shield-check, shield2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-shield-alert"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e669" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe669;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="shield-alert, shield3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-shield-cross"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e66a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe66a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="shield-cross, shield4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-lock"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e66b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe66b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="lock, privacy" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-rotation-lock"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e66c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe66c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="rotation-lock, screen-lock" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-unlock"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e66d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe66d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="unlock, lock2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-key"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e66e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe66e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="key, unlock2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-key-hole"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e66f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe66f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="key-hole, lock3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-toggle-off"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e670" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe670;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="toggle-off, toggle" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-toggle-on"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e671" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe671;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="toggle-on, toggle2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cog"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e672" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe672;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cog, gear" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cog2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e673" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe673;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cog2, gear2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-wrench"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e674" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe674;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="wrench, tool4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-screwdriver"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e675" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe675;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="screwdriver, tool5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-hammer-wrench"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e676" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe676;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="hammer-wrench, tool6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-hammer"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e677" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe677;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="hammer, tool7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-saw"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e678" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe678;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="saw, tool8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-axe"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e679" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe679;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="axe, tool9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-axe2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e67a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe67a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="axe2, tool10" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-shovel"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e67b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe67b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="shovel, tool11" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pickaxe"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e67c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe67c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pickaxe, tool12" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-factory"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e67d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe67d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="factory, build" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-factory2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e67e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe67e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="factory2, build2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-recycle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e67f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe67f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="recycle" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-trash"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e680" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe680;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="trash, bin" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-trash2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e681" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe681;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="trash2, bin2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-trash3"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e682" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe682;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="trash3, bin3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-broom"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e683" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe683;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="broom, sweep" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-game"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e684" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe684;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="game, retro" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-gamepad"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e685" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe685;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="gamepad, game2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-joystick"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e686" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe686;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="joystick, game3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-dice"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e687" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe687;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="dice, game4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-spades"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e688" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe688;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="spades, cards" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-diamonds"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e689" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe689;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="diamonds, cards2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-clubs"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e68a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe68a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="clubs, cards3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-hearts"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e68b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe68b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="hearts, cards4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-heart"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e68c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe68c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="heart, love" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-star"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e68d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe68d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="star, rating" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-star-half"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e68e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe68e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="star-half, rating2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-star-empty"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e68f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe68f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="star-empty, rating3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-flag"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e690" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe690;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="flag, report" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-flag2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e691" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe691;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="flag2, report2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-flag3"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e692" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe692;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="flag3, report3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-mailbox-full"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e693" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe693;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="mailbox-full, mailbox" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-mailbox-empty"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e694" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe694;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="mailbox-empty, mailbox2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-at-sign"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e695" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe695;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="at-sign, mail" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-envelope"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e696" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe696;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="envelope, mail2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-envelope-open"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e697" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe697;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="envelope-open, mail3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-paperclip"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e698" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe698;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="paperclip, attachment" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-paper-plane"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e699" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe699;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="paper-plane, mail4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-reply"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e69a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe69a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="reply, left" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-reply-all"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e69b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe69b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="reply-all, left2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-inbox"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e69c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe69c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="inbox, drawer" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-inbox2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e69d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe69d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="inbox2, drawer2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-outbox"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e69e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe69e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="outbox, drawer3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-box"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e69f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe69f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="box, storage2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-archive"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6a0" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6a0;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="archive, drawer4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-archive2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6a1" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6a1;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="archive2, drawer5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-drawers"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6a2" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6a2;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="drawers, drawer6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-drawers2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6a3" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6a3;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="drawers2, drawer7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-drawers3"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6a4" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6a4;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="drawers3, drawer8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-eye"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6a5" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6a5;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="eye, vision" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-eye-crossed"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6a6" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6a6;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="eye-crossed, eye2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-eye-plus"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6a7" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6a7;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="eye-plus, eye3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-eye-minus"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6a8" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6a8;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="eye-minus, eye4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-binoculars"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6a9" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6a9;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="binoculars, lookup" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-binoculars2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6aa" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6aa;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="binoculars2, lookup2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-hdd"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6ab" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6ab;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="hdd, storage3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-hdd-down"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6ac" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6ac;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="hdd-down, hdd2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-hdd-up"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6ad" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6ad;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="hdd-up, hdd3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-floppy-disk"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6ae" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6ae;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="floppy-disk, storage4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-disc"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6af" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6af;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="disc, storage5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-tape2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6b0" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6b0;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="tape2, storage6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-printer"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6b1" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6b1;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="printer, print" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-shredder"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6b2" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6b2;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="shredder, remove" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-file-empty"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6b3" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6b3;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="file-empty, file" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-file-add"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6b4" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6b4;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="file-add, file2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-file-check"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6b5" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6b5;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="file-check, file3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-file-lock"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6b6" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6b6;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="file-lock, file4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-files"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6b7" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6b7;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="files, stack" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-copy"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6b8" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6b8;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="copy, files2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-compare"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6b9" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6b9;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="compare, diff" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-folder"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6ba" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6ba;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="folder, directory" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-folder-search"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6bb" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6bb;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="folder-search, folder2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-folder-plus"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6bc" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6bc;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="folder-plus, folder3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-folder-minus"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6bd" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6bd;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="folder-minus, folder4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-folder-download"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6be" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6be;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="folder-download, folder5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-folder-upload"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6bf" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6bf;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="folder-upload, folder6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-folder-star"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6c0" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6c0;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="folder-star, folder7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-folder-heart"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6c1" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6c1;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="folder-heart, folder8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-folder-user"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6c2" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6c2;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="folder-user, folder9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-folder-shared"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6c3" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6c3;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="folder-shared, folder10" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-folder-music"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6c4" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6c4;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="folder-music, folder11" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-folder-picture"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6c5" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6c5;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="folder-picture, folder12" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-folder-film"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6c6" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6c6;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="folder-film, folder13" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-scissors"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6c7" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6c7;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="scissors, tool13" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-paste"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6c8" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6c8;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="paste, clipboard" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-clipboard-empty"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6c9" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6c9;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="clipboard-empty, clipboard2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-clipboard-pencil"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6ca" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6ca;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="clipboard-pencil, clipboard3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-clipboard-text"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6cb" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6cb;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="clipboard-text, clipboard4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-clipboard-check"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6cc" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6cc;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="clipboard-check, clipboard5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-clipboard-down"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6cd" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6cd;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="clipboard-down, clipboard6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-clipboard-left"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6ce" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6ce;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="clipboard-left, clipboard7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-clipboard-alert"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6cf" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6cf;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="clipboard-alert, clipboard8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-clipboard-user"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6d0" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6d0;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="clipboard-user, clipboard9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-register"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6d1" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6d1;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="register, signature" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-enter"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6d2" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6d2;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="enter, door" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-exit"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6d3" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6d3;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="exit, door2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-papers"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6d4" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6d4;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="papers, stack2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-news"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6d5" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6d5;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="news, newspaper" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-reading"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6d6" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6d6;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="reading, library" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-typewriter"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6d7" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6d7;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="typewriter, typing" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-document"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6d8" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6d8;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="document, file5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-document2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6d9" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6d9;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="document2, file6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-graduation-hat"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6da" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6da;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="graduation-hat, education" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-license"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6db" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6db;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="license, certificate" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-license2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6dc" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6dc;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="license2, certificate2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-medal-empty"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6dd" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6dd;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="medal-empty, medal" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-medal-first"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6de" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6de;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="medal-first, medal2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-medal-second"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6df" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6df;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="medal-second, medal3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-medal-third"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6e0" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6e0;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="medal-third, medal4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-podium"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6e1" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6e1;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="podium, standings" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-trophy"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6e2" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6e2;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="trophy, cup" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-trophy2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6e3" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6e3;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="trophy2, cup2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-music-note"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6e4" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6e4;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="music-note, music" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-music-note2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6e5" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6e5;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="music-note2, music2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-music-note3"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6e6" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6e6;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="music-note3, music3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-playlist"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6e7" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6e7;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="playlist" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-playlist-add"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6e8" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6e8;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="playlist-add" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-guitar"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6e9" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6e9;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="guitar, music4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-trumpet"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6ea" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6ea;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="trumpet, music5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-album"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6eb" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6eb;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="album, music6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-shuffle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6ec" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6ec;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="shuffle, randomize" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-repeat-one"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6ed" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6ed;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="repeat-one, loop" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-repeat"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6ee" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6ee;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="repeat, loop2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-headphones"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6ef" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6ef;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="headphones, music7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-headset"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6f0" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6f0;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="headset, headphones2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-loudspeaker"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6f1" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6f1;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="loudspeaker, music8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-equalizer"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6f2" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6f2;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="equalizer, settings" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-theater"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6f3" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6f3;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="theater, drama" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-3d-glasses"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6f4" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6f4;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="3d-glasses, glasses" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-ticket"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6f5" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6f5;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="ticket, theater2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-presentation"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6f6" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6f6;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="presentation, board" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-play"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6f7" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6f7;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="play, video" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-film-play"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6f8" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6f8;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="film-play, video2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-clapboard-play"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6f9" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6f9;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="clapboard-play, video3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-media"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6fa" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6fa;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="media, film-picture-music" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-film"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6fb" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6fb;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="film, video4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-film2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6fc" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6fc;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="film2, photo" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-surveillance"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6fd" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6fd;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="surveillance, security-camera" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-surveillance2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6fe" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6fe;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="surveillance2, security-camera2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-camera"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e6ff" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe6ff;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="camera, video5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-camera-crossed"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e700" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe700;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="camera-crossed, video6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-camera-play"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e701" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe701;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="camera-play, video7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-time-lapse"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e702" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe702;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="time-lapse, video8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-record"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e703" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe703;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="record, video9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-camera2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e704" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe704;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="camera2, photo2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-camera-flip"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e705" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe705;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="camera-flip" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-panorama"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e706" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe706;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="panorama, photo3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-time-lapse2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e707" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe707;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="time-lapse2, photo4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-shutter"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e708" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe708;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="shutter, camera3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-shutter2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e709" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe709;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="shutter2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-face-detection"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e70a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe70a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="face-detection" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-flare"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e70b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe70b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="flare" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-convex"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e70c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe70c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="convex" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-concave"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e70d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe70d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="concave" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-picture"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e70e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe70e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="picture, photo5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-picture2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e70f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe70f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="picture2, photo6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-picture3"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e710" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe710;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="picture3, photo7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pictures"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e711" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe711;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pictures, photo8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-book"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e712" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe712;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="book, read" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-audio-book"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e713" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe713;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="audio-book, book2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-book2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e714" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe714;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="book3, read2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bookmark"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e715" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe715;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bookmark, book4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bookmark2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e716" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe716;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bookmark2, ribbon" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-label"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e717" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe717;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="label" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-library"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e718" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe718;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="library2, book5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-library2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e719" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe719;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="library3, building9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-contacts"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e71a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe71a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="contacts, book6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-profile"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e71b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe71b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="profile, card" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-portrait"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e71c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe71c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="portrait, photo9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-portrait2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e71d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe71d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="portrait2, photo10" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-user"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e71e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe71e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="user, persona" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-user-plus"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e71f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe71f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="user-plus, user2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-user-minus"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e720" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe720;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="user-minus, user3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-user-lock"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e721" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe721;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="user-lock, user4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-users"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e722" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe722;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="users, group" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-users2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e723" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe723;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="users2, group2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-users-plus"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e724" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe724;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="users-plus, group3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-users-minus"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e725" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe725;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="users-minus, group4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-group-work"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e726" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe726;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="group-work, group5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-woman"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e727" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe727;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="woman, female" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-man"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e728" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe728;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="man, male" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-baby"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e729" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe729;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="baby, girl" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-baby2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e72a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe72a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="baby2, boy" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-baby3"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e72b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe72b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="baby3, newborn" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-baby-bottle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e72c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe72c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="baby-bottle, baby4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-walk"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e72d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe72d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="walk, human" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-hand-waving"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e72e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe72e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="hand-waving, human2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-jump"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e72f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe72f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="jump, human3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-run"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e730" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe730;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="run, human4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-woman2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e731" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe731;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="woman2, female2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-man2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e732" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe732;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="man2, male2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-man-woman"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e733" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe733;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="man-woman, gender" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-height"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e734" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe734;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="height" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-weight"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e735" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe735;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="weight, scale" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-scale"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e736" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe736;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="scale2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-button"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e737" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe737;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="button, clothing" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bow-tie"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e738" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe738;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bow-tie, clothing2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-tie"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e739" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe739;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="tie, clothing3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-socks"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e73a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe73a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="socks, clothing4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-shoe"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e73b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe73b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="shoe, clothing5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-shoes"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e73c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe73c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="shoes, clothing6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-hat"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e73d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe73d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="hat, clothing7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pants"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e73e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe73e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pants, clothing8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-shorts"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e73f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe73f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="shorts, clothing9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-flip-flops"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e740" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe740;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="flip-flops, clothing10" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-shirt"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e741" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe741;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="shirt, clothing11" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-hanger"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e742" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe742;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="hanger, clothing12" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-laundry"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e743" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe743;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="laundry, washing-machine" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-store"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e744" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe744;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="store, shop" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-haircut"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e745" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe745;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="haircut, scissors-comb" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-store-24"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e746" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe746;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="store-24, shop2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-barcode"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e747" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe747;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="barcode, price" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-barcode2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e748" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe748;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="barcode2, price2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-barcode3"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e749" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe749;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="barcode3, price3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cashier"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e74a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe74a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cashier, checkout" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bag"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e74b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe74b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bag, shop3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bag2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e74c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe74c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bag2, shop4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cart"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e74d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe74d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cart, shop5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cart-empty"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e74e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe74e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cart-empty, shop6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cart-full"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e74f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe74f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cart-full, shop7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cart-plus"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e750" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe750;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cart-plus, shop8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cart-plus2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e751" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe751;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cart-plus2, shop9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cart-add"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e752" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe752;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cart-add, shop10" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cart-remove"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e753" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe753;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cart-remove, shop11" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cart-exchange"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e754" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe754;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cart-exchange, shop12" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-tag"></span>

			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e755" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe755;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="tag, price4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-tags"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e756" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe756;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="tags, price5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-receipt"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e757" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe757;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="receipt, price6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-wallet"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e758" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe758;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="wallet, money" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-credit-card"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e759" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe759;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="credit-card, money2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cash-dollar"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e75a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe75a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cash-dollar, money3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cash-euro"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e75b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe75b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cash-euro, money4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cash-pound"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e75c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe75c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cash-pound, money5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cash-yen"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e75d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe75d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cash-yen, money6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bag-dollar"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e75e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe75e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bag-dollar, money7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bag-euro"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e75f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe75f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bag-euro, money8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bag-pound"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e760" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe760;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bag-pound, money9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bag-yen"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e761" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe761;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bag-yen, money10" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-coin-dollar"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e762" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe762;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="coin-dollar, money11" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-coin-euro"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e763" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe763;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="coin-euro, money12" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-coin-pound"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e764" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe764;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="coin-pound, money13" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-coin-yen"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e765" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe765;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="coin-yen, money14" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-calculator"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e766" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe766;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="calculator, arithmetic" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-calculator2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e767" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe767;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="calculator2, arithmetic2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-abacus"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e768" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe768;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="abacus, arithmetic3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-vault"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e769" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe769;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="vault, safe" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-telephone"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e76a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe76a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="telephone, phone" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-phone-lock"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e76b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe76b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="phone-lock, phone2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-phone-wave"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e76c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe76c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="phone-wave, phone3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-phone-pause"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e76d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe76d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="phone-pause, phone4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-phone-outgoing"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e76e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe76e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="phone-outgoing, phone5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-phone-incoming"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e76f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe76f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="phone-incoming, phone6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-phone-in-out"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e770" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe770;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="phone-in-out, phone7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-phone-error"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e771" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe771;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="phone-error, phone8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-phone-sip"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e772" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe772;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="phone-sip, phone9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-phone-plus"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e773" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe773;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="phone-plus, phone10" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-phone-minus"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e774" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe774;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="phone-minus, phone11" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-voicemail"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e775" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe775;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="voicemail, message" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-dial"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e776" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe776;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="dial, dial-pad" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-telephone2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e777" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe777;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="telephone2, phone12" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pushpin"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e778" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe778;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pushpin, pin" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pushpin2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e779" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe779;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pushpin2, pin2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-map-marker"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e77a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe77a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="map-marker, pin3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-map-marker-user"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e77b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe77b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="map-marker-user, pin4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-map-marker-down"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e77c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe77c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="map-marker-down, pin5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-map-marker-check"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e77d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe77d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="map-marker-check, pin6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-map-marker-crossed"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e77e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe77e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="map-marker-crossed, pin7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-radar"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e77f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe77f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="radar, scanner" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-compass2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e780" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe780;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="compass2, guide" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-map"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e781" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe781;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="map, guide2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-map2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e782" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe782;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="map2, guide3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-location"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e783" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe783;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="location, compass3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-road-sign"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e784" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe784;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="road-sign, directions" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-calendar-empty"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e785" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe785;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="calendar-empty, calendar" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-calendar-check"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e786" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe786;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="calendar-check, calendar2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-calendar-cross"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e787" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe787;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="calendar-cross, calendar3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-calendar-31"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e788" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe788;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="calendar-31, calendar4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-calendar-full"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e789" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe789;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="calendar-full, calendar5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-calendar-insert"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e78a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe78a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="calendar-insert, calendar6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-calendar-text"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e78b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe78b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="calendar-text, calendar7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-calendar-user"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e78c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe78c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="calendar-user, calendar8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-mouse"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e78d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe78d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="mouse, click" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-mouse-left"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e78e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe78e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="mouse-left, click2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-mouse-right"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e78f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe78f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="mouse-right, click3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-mouse-both"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e790" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe790;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="mouse-both, click4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-keyboard"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e791" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe791;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="keyboard, type" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-keyboard-up"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e792" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe792;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="keyboard-up, keyboard2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-keyboard-down"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e793" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe793;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="keyboard-down, keyboard3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-delete"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e794" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe794;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="delete, backspace" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-spell-check"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e795" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe795;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="spell-check, spelling" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-escape"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e796" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe796;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="escape" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-enter2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">

				<input type="text" readonly value="e797" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe797;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="enter2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-screen"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e798" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe798;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="screen, monitor" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-aspect-ratio"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e799" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe799;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="aspect-ratio, screen2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-signal"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e79a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe79a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="signal, bars" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-signal-lock"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e79b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe79b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="signal-lock, signal2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-signal-80"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e79c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe79c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="signal-80, signal3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-signal-60"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e79d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe79d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="signal-60, signal4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-signal-40"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e79e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe79e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="signal-40, signal5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-signal-20"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e79f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe79f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="signal-20, signal6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-signal-0"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7a0" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7a0;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="signal-0, signal7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-signal-blocked"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7a1" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7a1;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="signal-blocked, signal8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-sim"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7a2" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7a2;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="sim, sim-card" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-flash-memory"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7a3" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7a3;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="flash-memory" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-usb-drive"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7a4" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7a4;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="usb-drive, flash-memory2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-phone"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7a5" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7a5;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="phone13, mobile" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-smartphone"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7a6" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7a6;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="smartphone, mobile2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-smartphone-notification"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7a7" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7a7;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="smartphone-notification, mobile3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-smartphone-vibration"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7a8" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7a8;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="smartphone-vibration, mobile4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-smartphone-embed"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7a9" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7a9;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="smartphone-embed, mobile5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-smartphone-waves"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7aa" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7aa;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="smartphone-waves, mobile6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-tablet"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7ab" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7ab;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="tablet, mobile7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-tablet2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7ac" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7ac;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="tablet2, mobile8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-laptop"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7ad" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7ad;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="laptop, computer" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-laptop-phone"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7ae" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7ae;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="laptop-phone, devices" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-desktop"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7af" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7af;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="desktop, computer2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-launch"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7b0" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7b0;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="launch, share" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-new-tab"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7b1" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7b1;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="new-tab, window-tab" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-window"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7b2" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7b2;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="window, program" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cable"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7b3" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7b3;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cable, plug" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cable2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7b4" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7b4;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cable2, plug2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-tv"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7b5" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7b5;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="tv, television" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-radio"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7b6" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7b6;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="radio, music9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-remote-control"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7b7" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7b7;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="remote-control" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-power-switch"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7b8" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7b8;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="power-switch, toggle3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-power"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7b9" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7b9;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="power, lightning" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-power-crossed"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7ba" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7ba;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="power-crossed, lightning2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-flash-auto"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7bb" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7bb;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="flash-auto, lightning3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-lamp"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7bc" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7bc;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="lamp, light" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-flashlight"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7bd" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7bd;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="flashlight, light2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-lampshade"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7be" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7be;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="lampshade, light3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cord"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7bf" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7bf;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cord, plug3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-outlet"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7c0" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7c0;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="outlet, socket" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-battery-power"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7c1" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7c1;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="battery-power, battery" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-battery-empty"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7c2" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7c2;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="battery-empty, battery2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-battery-alert"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7c3" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7c3;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="battery-alert, battery3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-battery-error"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7c4" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7c4;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="battery-error, battery4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-battery-low1"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7c5" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7c5;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="battery-low1, battery5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-battery-low2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7c6" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7c6;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="battery-low2, battery6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-battery-low3"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7c7" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7c7;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="battery-low3, battery7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-battery-mid1"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7c8" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7c8;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="battery-mid1, battery8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-battery-mid2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7c9" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7c9;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="battery-mid2, battery9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-battery-mid3"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7ca" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7ca;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="battery-mid3, battery10" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-battery-full"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7cb" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7cb;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="battery-full, battery11" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-battery-charging"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7cc" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7cc;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="battery-charging, battery12" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-battery-charging2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7cd" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7cd;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="battery-charging2, battery13" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-battery-charging3"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7ce" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7ce;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="battery-charging3, battery14" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-battery-charging4"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7cf" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7cf;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="battery-charging4, battery15" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-battery-charging5"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7d0" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7d0;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="battery-charging5, battery16" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-battery-charging6"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7d1" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7d1;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="battery-charging6, battery17" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-battery-charging7"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7d2" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7d2;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="battery-charging7, battery18" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-chip"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7d3" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7d3;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="chip, cpu" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-chip-x64"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7d4" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7d4;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="chip-x64, cpu2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-chip-x86"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7d5" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7d5;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="chip-x86, cpu3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bubble"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7d6" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7d6;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bubble, chat" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bubbles"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7d7" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7d7;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bubbles, chat2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bubble-dots"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7d8" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7d8;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bubble-dots, chat3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bubble-alert"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7d9" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7d9;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bubble-alert, chat4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bubble-question"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7da" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7da;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bubble-question, chat5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bubble-text"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7db" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7db;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bubble-text, chat6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bubble-pencil"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7dc" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7dc;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bubble-pencil, chat7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bubble-picture"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7dd" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7dd;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bubble-picture, chat8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bubble-video"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7de" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7de;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bubble-video, chat9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bubble-user"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7df" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7df;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bubble-user, chat10" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bubble-quote"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7e0" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7e0;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bubble-quote, chat11" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bubble-heart"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7e1" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7e1;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bubble-heart, chat12" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bubble-emoticon"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7e2" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7e2;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bubble-emoticon, chat13" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bubble-attachment"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7e3" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7e3;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bubble-attachment, chat14" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-phone-bubble"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7e4" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7e4;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="phone-bubble, phone14" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-quote-open"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7e5" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7e5;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="quote-open" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-quote-close"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7e6" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7e6;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="quote-close" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-dna"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7e7" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7e7;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="dna, gene" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-heart-pulse"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7e8" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7e8;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="heart-pulse, health" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pulse"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7e9" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7e9;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pulse, health2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-syringe"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7ea" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7ea;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="syringe, health3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pills"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7eb" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7eb;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pills, health4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-first-aid"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7ec" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7ec;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="first-aid, health5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-lifebuoy"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7ed" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7ed;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="lifebuoy, help" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bandage"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7ee" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7ee;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bandage, health6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bandages"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7ef" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7ef;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bandages, health7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-thermometer"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7f0" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7f0;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="thermometer, temperature" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-microscope"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7f1" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7f1;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="microscope, lab" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-brain"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7f2" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7f2;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="brain, mind" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-beaker"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7f3" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7f3;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="beaker, lab2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-skull"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7f4" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7f4;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="skull, skeleton" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bone"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7f5" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7f5;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bone, dog" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">

			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-construction"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7f6" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7f6;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="construction, road-sign2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-construction-cone"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7f7" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7f7;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="construction-cone, construction2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pie-chart"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7f8" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7f8;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pie-chart, chart" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pie-chart2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7f9" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7f9;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pie-chart2, chart2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-graph"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7fa" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7fa;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="graph, chart3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-chart-growth"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7fb" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7fb;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="chart-growth, chart4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-chart-bars"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7fc" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7fc;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="chart-bars, chart5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-chart-settings"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7fd" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7fd;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="chart-settings, chart6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cake"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7fe" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7fe;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cake, birthday" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-gift"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e7ff" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe7ff;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="gift, birthday2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-balloon"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e800" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe800;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="balloon, birthday3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-rank"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e801" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe801;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="rank, chevron" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-rank2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e802" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe802;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="rank2, chevron2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-rank3"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e803" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe803;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="rank3, chevron3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-crown"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e804" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe804;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="crown, king" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-lotus"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e805" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe805;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="lotus, flower" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-diamond"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e806" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe806;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="diamond, jewelry" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-diamond2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e807" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe807;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="diamond2, jewelry2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-diamond3"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e808" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe808;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="diamond3, jewelry3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-diamond4"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e809" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe809;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="diamond4, jewelry4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-linearicons"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e80a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe80a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="linearicons, perxis" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-teacup"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e80b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe80b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="teacup, drink" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-teapot"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e80c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe80c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="teapot, drink2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-glass"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e80d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe80d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="glass, drink3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bottle2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e80e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe80e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bottle2, drink4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-glass-cocktail"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e80f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe80f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="glass-cocktail, drink5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-glass2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e810" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe810;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="glass2, drink6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-dinner"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e811" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe811;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="dinner, food" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-dinner2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e812" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe812;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="dinner2, food2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-chef"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e813" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe813;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="chef, food3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-scale2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e814" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe814;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="scale3, weight2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-egg"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e815" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe815;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="egg, food4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-egg2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e816" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe816;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="egg2, food5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-eggs"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e817" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe817;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="eggs, food6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-platter"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e818" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe818;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="platter, food7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-steak"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e819" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe819;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="steak, food8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-hamburger"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e81a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe81a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="hamburger, food9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-hotdog"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e81b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe81b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="hotdog, food10" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pizza"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e81c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe81c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pizza, food11" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-sausage"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e81d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe81d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="sausage, food12" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-chicken"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e81e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe81e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="chicken, food13" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-fish"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e81f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe81f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="fish, food14" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-carrot"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e820" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe820;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="carrot, food15" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cheese"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e821" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe821;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cheese, food16" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bread"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e822" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe822;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bread, food17" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-ice-cream"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e823" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe823;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="ice-cream, dessert" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-ice-cream2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e824" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe824;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="ice-cream2, dessert2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-candy"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e825" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe825;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="candy, dessert3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-lollipop"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e826" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe826;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="lollipop, dessert4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-coffee-bean"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e827" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe827;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="coffee-bean, coffee" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-coffee-cup"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e828" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe828;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="coffee-cup, drink7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cherry"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e829" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe829;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cherry, fruit" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-grapes"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e82a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe82a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="grapes, fruit2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-citrus"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e82b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe82b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="citrus, fruit3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-apple"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e82c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe82c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="apple, fruit4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-leaf"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e82d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe82d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="leaf, nature" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-landscape"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e82e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe82e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="landscape, nature2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pine-tree"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e82f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe82f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pine-tree, nature3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-tree"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e830" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe830;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="tree, nature4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cactus"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e831" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe831;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cactus, nature5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-paw"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e832" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe832;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="paw, pet" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-footprint"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e833" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe833;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="footprint, steps" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-speed-slow"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e834" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe834;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="speed-slow, gauge" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-speed-medium"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e835" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe835;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="speed-medium, gauge2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-speed-fast"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e836" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe836;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="speed-fast, gauge3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-rocket"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e837" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe837;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="rocket, spaceship" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-hammer2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e838" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe838;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="hammer2, justice" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-balance"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e839" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe839;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="balance, justice2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-briefcase"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e83a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe83a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="briefcase, suitcase" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-luggage-weight"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e83b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe83b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="luggage-weight, scale4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-dolly"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e83c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe83c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="dolly, luggage" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-plane"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e83d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe83d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="plane, flight" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-plane-crossed"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e83e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe83e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="plane-crossed, flight2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-helicopter"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e83f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe83f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="helicopter, flight3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-traffic-lights"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e840" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe840;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="traffic-lights, traffic-signals" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-siren"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e841" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe841;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="siren, alarm" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-road"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e842" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe842;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="road, travel" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-engine"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e843" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe843;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="engine, motor" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-oil-pressure"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e844" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe844;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="oil-pressure" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-coolant-temperature"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e845" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe845;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="coolant-temperature, thermometer2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-car-battery"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e846" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe846;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="car-battery, battery19" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-gas"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e847" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe847;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="gas, fuel" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-gallon"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e848" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe848;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="gallon, gas2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-transmission"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e849" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe849;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="transmission, stick-shift" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-car"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e84a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe84a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="car, travel2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-car-wash"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e84b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe84b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="car-wash" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-car-wash2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e84c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe84c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="car-wash2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bus"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e84d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe84d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bus, travel3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bus2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e84e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe84e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bus2, travel4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-car2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e84f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe84f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="car2, travel5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-parking"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e850" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe850;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="parking" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-car-lock"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e851" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe851;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="car-lock" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-taxi"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e852" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe852;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="taxi, travel6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-car-siren"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e853" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe853;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="car-siren, police" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-car-wash3"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e854" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe854;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="car-wash3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-car-wash4"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e855" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe855;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="car-wash4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-ambulance"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e856" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe856;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="ambulance, health8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-truck"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e857" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe857;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="truck, delivery" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-trailer"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e858" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe858;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="trailer, delivery2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-scale-truck"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e859" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe859;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="scale-truck, weighbridge" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-train"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e85a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe85a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="train, travel7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-ship"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e85b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe85b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="ship, travel8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-ship2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e85c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe85c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="ship2, travel9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-anchor"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e85d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe85d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="anchor, sailing" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-boat"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e85e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe85e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="boat, travel10" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bicycle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e85f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe85f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bicycle, travel11" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bicycle2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e860" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe860;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bicycle2, exercise" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-dumbbell"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e861" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe861;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="dumbbell, exercise2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bench-press"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e862" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe862;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bench-press, exercise3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-swim"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e863" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe863;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="swim, exercise4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-football"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e864" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe864;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="football, sports" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-baseball-bat"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e865" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe865;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="baseball-bat, sports2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-baseball"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e866" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe866;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="baseball, sports3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-tennis"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e867" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe867;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="tennis, sports4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-tennis2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e868" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe868;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="tennis2, sports5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-ping-pong"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e869" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe869;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="ping-pong, sports6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-hockey"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e86a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe86a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="hockey, sports7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-8ball"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e86b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe86b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="8ball, sports8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bowling"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e86c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe86c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bowling, sports9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bowling-pins"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e86d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe86d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bowling-pins, sports10" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-golf"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e86e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe86e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="golf, sports11" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-golf2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e86f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe86f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="golf2, sports12" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-archery"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e870" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe870;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="archery, sports13" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-slingshot"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e871" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe871;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="slingshot, weapon3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-soccer"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e872" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe872;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="soccer, sports14" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-basketball"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e873" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe873;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="basketball, sports15" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cube"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e874" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe874;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cube, geometry" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-3d-rotate"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e875" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe875;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="3d-rotate" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-puzzle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e876" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe876;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="puzzle, piece" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-glasses"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e877" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe877;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="glasses2, vision2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-glasses2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e878" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe878;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="glasses3, vision3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-accessibility"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e879" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe879;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="accessibility" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-wheelchair"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e87a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe87a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="wheelchair, disabled" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-wall"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e87b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe87b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="wall, bricks" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-fence"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e87c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe87c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="fence, wall2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-wall2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e87d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe87d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="wall3, bricks2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-icons"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e87e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe87e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="icons, grid" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-resize-handle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e87f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe87f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="resize-handle" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-icons2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e880" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe880;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="icons2, grid2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-select"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e881" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe881;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="select, cursor" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-select2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e882" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe882;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="select2, cursor2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-site-map"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e883" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe883;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="site-map, tree2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-earth"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e884" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe884;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="earth, globe" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-earth-lock"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e885" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe885;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="earth-lock, internet-lock" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-network"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e886" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe886;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="network, globe2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-network-lock"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e887" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe887;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="network-lock, internet-lock2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-planet"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e888" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe888;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="planet, globe3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-happy"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e889" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe889;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="happy, emoticon" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-smile"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e88a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe88a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="smile, emoticon2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-grin"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e88b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe88b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="grin, emoticon3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-tongue"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e88c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe88c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="tongue, emoticon4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-sad"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e88d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe88d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="sad, emoticon5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-wink"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e88e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe88e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="wink, emoticon6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-dream"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e88f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe88f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="dream, emoticon7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-shocked"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e890" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe890;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="shocked, emoticon8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-shocked2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e891" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe891;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="shocked2, emoticon9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-tongue2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e892" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe892;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="tongue2, emoticon10" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-neutral"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e893" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe893;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="neutral, emoticon11" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-happy-grin"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e894" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe894;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="happy-grin, emoticon12" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cool"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e895" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe895;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cool, emoticon13" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-mad"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e896" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe896;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="mad, emoticon14" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-grin-evil"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e897" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe897;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="grin-evil, emoticon15" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-evil"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e898" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe898;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="evil, emoticon16" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-wow"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e899" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe899;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="wow, emoticon17" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-annoyed"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e89a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe89a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="annoyed, emoticon18" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-wondering"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e89b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe89b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="wondering, emoticon19" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-confused"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e89c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe89c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="confused, emoticon20" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-zipped"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e89d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe89d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="zipped, emoticon21" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-grumpy"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e89e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe89e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="grumpy, emoticon22" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-mustache"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e89f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe89f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="mustache, emoticon23" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-tombstone-hipster"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8a0" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8a0;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="tombstone-hipster, rip" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-tombstone"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8a1" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8a1;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="tombstone, rip2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-ghost"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8a2" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8a2;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="ghost, spirit" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-ghost-hipster"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8a3" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8a3;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="ghost-hipster, spirit2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-halloween"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8a4" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8a4;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="halloween, pumpkin" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-christmas"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8a5" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8a5;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="christmas, tree3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-easter-egg"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8a6" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8a6;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="easter-egg, egg3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-mustache2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8a7" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8a7;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="mustache2, hipster" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-mustache-glasses"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8a8" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8a8;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="mustache-glasses, hipster2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pipe"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8a9" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8a9;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pipe, hipster3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-alarm"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8aa" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8aa;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="alarm2, bell" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-alarm-add"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8ab" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8ab;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="alarm-add, bell2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-alarm-snooze"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8ac" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8ac;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="alarm-snooze, bell3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-alarm-ringing"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8ad" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8ad;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="alarm-ringing, bell4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bullhorn"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8ae" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8ae;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bullhorn, megaphone" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-hearing"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8af" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8af;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="hearing, health9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-volume-high"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8b0" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8b0;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="volume-high, speaker" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-volume-medium"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8b1" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8b1;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="volume-medium, speaker2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-volume-low"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8b2" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8b2;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="volume-low, speaker3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-volume"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8b3" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8b3;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="volume, speaker4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-mute"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8b4" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8b4;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="mute, speaker5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-lan"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8b5" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8b5;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="lan, network2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-lan2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8b6" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8b6;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="lan2, network3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-wifi"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8b7" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8b7;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="wifi, connection" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-wifi-lock"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8b8" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8b8;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="wifi-lock, connection2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-wifi-blocked"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8b9" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8b9;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="wifi-blocked, connection3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-wifi-mid"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8ba" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8ba;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="wifi-mid, connection4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-wifi-low"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8bb" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8bb;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="wifi-low, connection5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-wifi-low2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8bc" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8bc;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="wifi-low2, connection6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-wifi-alert"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8bd" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8bd;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="wifi-alert, connection7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-wifi-alert-mid"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8be" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8be;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="wifi-alert-mid, connection8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-wifi-alert-low"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8bf" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8bf;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="wifi-alert-low, connection9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-wifi-alert-low2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8c0" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8c0;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="wifi-alert-low2, connection10" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-stream"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8c1" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8c1;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="stream, broadcast" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-stream-check"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8c2" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8c2;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="stream-check, stream2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-stream-error"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8c3" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8c3;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="stream-error, stream3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-stream-alert"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8c4" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8c4;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="stream-alert, stream4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-communication"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8c5" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8c5;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="communication, waves" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-communication-crossed"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8c6" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8c6;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="communication-crossed, waves2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-broadcast"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8c7" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8c7;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="broadcast2, waves3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-antenna"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8c8" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8c8;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="antenna, waves4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-satellite"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8c9" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8c9;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="satellite, gps" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-satellite2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8ca" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8ca;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="satellite2, antenna2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-mic"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8cb" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8cb;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="mic, voice" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-mic-mute"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8cc" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8cc;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="mic-mute, voice2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-mic2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8cd" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8cd;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="mic2, voice3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-spotlights"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8ce" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8ce;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="spotlights, featured" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-hourglass"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8cf" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8cf;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="hourglass, loading" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-loading"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8d0" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8d0;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="loading2, spinner" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-loading2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8d1" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8d1;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="loading3, spinner2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-loading3"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8d2" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8d2;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="loading4, spinner3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-refresh"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8d3" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8d3;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="refresh, spinner4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-refresh2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8d4" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8d4;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="refresh2, spinner5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-undo"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8d5" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8d5;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="undo, left3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-redo"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8d6" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8d6;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="redo, right" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-jump2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8d7" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8d7;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="jump2, step-over" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-undo2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8d8" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8d8;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="undo2, ccw" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-redo2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8d9" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8d9;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="redo2, cw" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-sync"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8da" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8da;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="sync, spinner6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-repeat-one2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8db" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8db;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="repeat-one2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-sync-crossed"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8dc" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8dc;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="sync-crossed" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-sync2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8dd" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8dd;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">

				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="sync2, spinner7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-repeat-one3"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8de" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8de;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="repeat-one3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-sync-crossed2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8df" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8df;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="sync-crossed2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-return"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8e0" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8e0;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="return, backward" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-return2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8e1" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8e1;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="return2, backward2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-refund"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8e2" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8e2;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="refund, return3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-history"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8e3" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8e3;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="history, archive3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-history2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8e4" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8e4;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="history2, archive4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-self-timer"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8e5" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8e5;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="self-timer" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-clock"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8e6" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8e6;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="clock, time" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-clock2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8e7" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8e7;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="clock2, time2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-clock3"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8e8" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8e8;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="clock3, time3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-watch"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8e9" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8e9;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="watch, time4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-alarm2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8ea" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8ea;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="alarm3, time5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-alarm-add2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8eb" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8eb;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="alarm-add2, time6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-alarm-remove"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8ec" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8ec;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="alarm-remove, time7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-alarm-check"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8ed" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8ed;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="alarm-check, time8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-alarm-error"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8ee" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8ee;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="alarm-error, time9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-timer"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8ef" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8ef;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="timer, time10" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-timer-crossed"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8f0" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8f0;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="timer-crossed, time11" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-timer2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8f1" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8f1;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="timer2, time12" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-timer-crossed2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8f2" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8f2;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="timer-crossed2, time13" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-download"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8f3" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8f3;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="download, down" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-upload"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8f4" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8f4;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="upload, up" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-download2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8f5" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8f5;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="download2, down2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-upload2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8f6" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8f6;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="upload2, up2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-enter-up"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8f7" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8f7;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="enter-up, up3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-enter-down"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8f8" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8f8;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="enter-down, down3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-enter-left"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8f9" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8f9;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="enter-left, left4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-enter-right"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8fa" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8fa;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="enter-right, right2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-exit-up"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8fb" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8fb;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="exit-up, up4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-exit-down"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8fc" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8fc;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="exit-down, down4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-exit-left"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8fd" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8fd;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="exit-left, left5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-exit-right"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8fe" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8fe;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="exit-right, right3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-enter-up2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e8ff" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe8ff;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="enter-up2, up5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-enter-down2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e900" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe900;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="enter-down2, down5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-enter-vertical"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e901" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe901;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="enter-vertical" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-enter-left2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e902" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe902;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="enter-left2, left6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-enter-right2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e903" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe903;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="enter-right2, right4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-enter-horizontal"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e904" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe904;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="enter-horizontal, arrow" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-exit-up2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e905" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe905;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="exit-up2, up6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-exit-down2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e906" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe906;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="exit-down2, down6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-exit-left2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e907" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe907;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="exit-left2, left7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-exit-right2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e908" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe908;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="exit-right2, right5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cli"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e909" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe909;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cli, console" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bug"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e90a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe90a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bug, insect" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-code"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e90b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe90b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="code, embed" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-file-code"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e90c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe90c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="file-code, file7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-file-image"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e90d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe90d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="file-image, file8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-file-zip"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e90e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe90e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="file-zip, file9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-file-audio"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e90f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe90f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="file-audio, file10" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-file-video"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e910" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe910;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="file-video, file11" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-file-preview"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e911" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe911;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="file-preview, file12" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-file-charts"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e912" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe912;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="file-charts, file13" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-file-stats"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e913" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe913;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="file-stats, file14" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-file-spreadsheet"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e914" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe914;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="file-spreadsheet, file15" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-link"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e915" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe915;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="link, url" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-unlink"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e916" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe916;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="unlink, url2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-link2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e917" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe917;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="link2, url3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-unlink2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e918" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe918;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="unlink2, url4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-thumbs-up"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e919" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe919;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="thumbs-up, like" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-thumbs-down"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e91a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe91a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="thumbs-down, dislike" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-thumbs-up2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e91b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe91b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="thumbs-up2, like2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-thumbs-down2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e91c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe91c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="thumbs-down2, dislike2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-thumbs-up3"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e91d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe91d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="thumbs-up3, like3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-thumbs-down3"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e91e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe91e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="thumbs-down3, dislike3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-share"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e91f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe91f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="share2, export" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-share2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e920" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe920;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="share3, social" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-share3"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e921" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe921;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="share4, social2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-magnifier"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e922" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe922;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="magnifier, search" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-file-search"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e923" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe923;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="file-search, file16" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-find-replace"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e924" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe924;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="find-replace, search2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-zoom-in"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e925" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe925;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="zoom-in, magnifier2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-zoom-out"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e926" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe926;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="zoom-out, magnifier3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-loupe"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e927" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe927;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="loupe, magnifier4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-loupe-zoom-in"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e928" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe928;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="loupe-zoom-in, magnifier5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-loupe-zoom-out"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e929" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe929;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="loupe-zoom-out, magnifier6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cross"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e92a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe92a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cross, cancel" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-menu"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e92b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe92b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="menu, options" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-list"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e92c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe92c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="list, options2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-list2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e92d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe92d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="list2, options3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-list3"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e92e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe92e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="list3, options4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-menu2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e92f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe92f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="menu2, options5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-list4"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e930" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe930;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="list4, options6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-menu3"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e931" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe931;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="menu3, dropdown" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-exclamation"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e932" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe932;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="exclamation, alert" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-question"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e933" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe933;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="question, help2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-check"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e934" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe934;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="check, checkmark" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cross2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e935" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe935;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cross2, cancel2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-plus"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e936" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe936;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="plus, add" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-minus"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e937" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe937;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="minus, subtract" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-percent"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e938" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe938;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="percent, discount" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-chevron-up"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e939" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe939;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="chevron-up, up7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-chevron-down"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e93a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe93a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="chevron-down, down7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-chevron-left"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e93b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe93b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="chevron-left, left8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-chevron-right"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e93c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe93c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="chevron-right, right6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-chevrons-expand-vertical"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e93d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe93d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="chevrons-expand-vertical, chevrons" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-chevrons-expand-horizontal"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e93e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe93e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="chevrons-expand-horizontal, chevrons2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-chevrons-contract-vertical"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e93f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe93f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="chevrons-contract-vertical, chevrons3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-chevrons-contract-horizontal"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e940" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe940;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="chevrons-contract-horizontal, chevrons4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-arrow-up"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e941" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe941;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="arrow-up, up8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-arrow-down"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e942" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe942;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="arrow-down, down8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-arrow-left"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e943" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe943;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="arrow-left, left9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-arrow-right"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e944" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe944;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="arrow-right, right7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-arrow-up-right"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e945" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe945;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="arrow-up-right, up-right" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-arrows-merge"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e946" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe946;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="arrows-merge, up9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-arrows-split"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e947" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe947;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="arrows-split, up10" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-arrow-divert"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e948" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe948;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="arrow-divert, reflect" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-arrow-return"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e949" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe949;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="arrow-return, left10" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-expand"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e94a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe94a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="expand, maximize" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-contract"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e94b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe94b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="contract, minimize" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-expand2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e94c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe94c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="expand2, maximize2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-contract2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e94d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe94d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="contract2, minimize2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-move"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e94e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe94e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="move, arrows" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-tab"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e94f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe94f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="tab, switch" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-arrow-wave"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e950" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe950;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="arrow-wave" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-expand3"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e951" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe951;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="expand3, maximize3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-expand4"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e952" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe952;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="expand4, maximize4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-contract3"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e953" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe953;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="contract3, minimize3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-notification"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e954" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe954;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="notification, alert2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-warning"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e955" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe955;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="warning, alert3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-notification-circle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e956" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe956;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="notification-circle, alert4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-question-circle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e957" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe957;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="question-circle, help3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-menu-circle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e958" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe958;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="menu-circle, menu4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-checkmark-circle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e959" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe959;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="checkmark-circle, checkmark2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cross-circle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e95a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe95a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cross-circle, cross3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-plus-circle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e95b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe95b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="plus-circle, plus2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-circle-minus"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e95c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe95c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="circle-minus, minus2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-percent-circle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e95d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe95d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="percent-circle, discount2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-arrow-up-circle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e95e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe95e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="arrow-up-circle, up11" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-arrow-down-circle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e95f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe95f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="arrow-down-circle, down9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-arrow-left-circle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e960" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe960;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="arrow-left-circle, left11" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-arrow-right-circle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e961" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe961;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="arrow-right-circle, right8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-chevron-up-circle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e962" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe962;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="chevron-up-circle, up12" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-chevron-down-circle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e963" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe963;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="chevron-down-circle, down10" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-chevron-left-circle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e964" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe964;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="chevron-left-circle, left12" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-chevron-right-circle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e965" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe965;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="chevron-right-circle, right9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-backward-circle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e966" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe966;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="backward-circle, backward3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-first-circle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e967" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe967;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="first-circle, first" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-previous-circle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e968" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe968;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="previous-circle, previous" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-stop-circle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e969" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe969;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="stop-circle, stop" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-play-circle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e96a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe96a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="play-circle, play2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pause-circle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e96b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe96b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pause-circle, pause" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-next-circle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e96c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe96c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="next-circle, next" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-last-circle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e96d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe96d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="last-circle, last" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-forward-circle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e96e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe96e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="forward-circle, forward" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-eject-circle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e96f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe96f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="eject-circle, eject" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-crop"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e970" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe970;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="crop, cut" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-frame-expand"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e971" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe971;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="frame-expand, maximize5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-frame-contract"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e972" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe972;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="frame-contract, minimize4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-focus"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e973" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe973;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="focus, target2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-transform"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e974" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe974;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="transform, bounding-box" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-grid"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e975" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe975;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="grid3, squares" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-grid-crossed"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e976" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe976;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="grid-crossed, squares2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-layers"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e977" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe977;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="layers, stack3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-layers-crossed"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e978" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe978;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="layers-crossed, stack4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-toggle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e979" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe979;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="toggle4, fold" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-rulers"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e97a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe97a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="rulers, tool14" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-ruler"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e97b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe97b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="ruler, tool15" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-funnel"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e97c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe97c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="funnel, filter" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-flip-horizontal"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e97d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe97d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="flip-horizontal, mirror" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-flip-vertical"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e97e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe97e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="flip-vertical, mirror2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-flip-horizontal2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e97f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe97f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="flip-horizontal2, mirror3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-flip-vertical2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e980" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe980;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="flip-vertical2, mirror4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-angle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e981" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe981;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="angle, measurement" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-angle2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e982" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe982;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="angle2, measurement2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-subtract"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e983" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe983;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="subtract2, boolean-operation" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-combine"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e984" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe984;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="combine, boolean-operation2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-intersect"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e985" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe985;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="intersect, boolean-operation3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-exclude"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e986" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe986;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="exclude, boolean-operation4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-align-center-vertical"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e987" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe987;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="align-center-vertical, align" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-align-right"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e988" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe988;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="align-right, align2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-align-bottom"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e989" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe989;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="align-bottom, align3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-align-left"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e98a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe98a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="align-left, align4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-align-center-horizontal"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e98b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe98b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="align-center-horizontal, align5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-align-top"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e98c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe98c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="align-top, align6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-square"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e98d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe98d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="square, geometry2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-plus-square"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e98e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe98e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="plus-square, add2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-minus-square"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e98f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe98f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="minus-square, subtract3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-percent-square"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e990" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe990;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="percent-square, discount3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-arrow-up-square"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e991" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe991;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="arrow-up-square, up13" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-arrow-down-square"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e992" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe992;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="arrow-down-square, down11" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-arrow-left-square"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e993" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe993;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="arrow-left-square, left13" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-arrow-right-square"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e994" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe994;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="arrow-right-square, right10" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-chevron-up-square"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e995" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe995;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="chevron-up-square, up14" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-chevron-down-square"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e996" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe996;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="chevron-down-square, down12" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-chevron-left-square"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e997" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe997;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="chevron-left-square, left14" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-chevron-right-square"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e998" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe998;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="chevron-right-square, right11" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-check-square"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e999" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe999;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="check-square, checkmark3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-cross-square"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e99a" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe99a;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="cross-square, cross4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-menu-square"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e99b" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe99b;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="menu-square, menu5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-prohibited"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e99c" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe99c;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="prohibited, forbidden" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-circle"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e99d" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe99d;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="circle, geometry3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-radio-button"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e99e" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe99e;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="radio-button" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-ligature"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e99f" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe99f;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="ligature, typography" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-text-format"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9a0" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9a0;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="text-format, typography2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-text-format-remove"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9a1" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9a1;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="text-format-remove, typography3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-text-size"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9a2" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9a2;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="text-size, typography4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-bold"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9a3" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9a3;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="bold, typography5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-italic"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9a4" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9a4;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="italic, typography6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-underline"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9a5" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9a5;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="underline, typography7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-strikethrough"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9a6" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9a6;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="strikethrough, typography8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-highlight"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9a7" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9a7;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="highlight, magic-marker" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-text-align-left"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9a8" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9a8;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="text-align-left, typography9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-text-align-center"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9a9" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9a9;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="text-align-center, typography10" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-text-align-right"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9aa" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9aa;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="text-align-right, typography11" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-text-align-justify"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9ab" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9ab;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="text-align-justify, typography12" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-line-spacing"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9ac" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9ac;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="line-spacing, typography13" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-indent-increase"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9ad" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9ad;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="indent-increase, typography14" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-indent-decrease"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9ae" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9ae;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="indent-decrease, typography15" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-text-wrap"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9af" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9af;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="text-wrap, typography16" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pilcrow"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9b0" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9b0;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pilcrow, typography17" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-direction-ltr"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9b1" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9b1;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="direction-ltr, typography18" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-direction-rtl"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9b2" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9b2;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="direction-rtl, typography19" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-page-break"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9b3" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9b3;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="page-break" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-page-break2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9b4" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9b4;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="page-break2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-sort-alpha-asc"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9b5" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9b5;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="sort-alpha-asc, sort" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-sort-alpha-desc"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9b6" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9b6;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="sort-alpha-desc, sort2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-sort-numeric-asc"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9b7" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9b7;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="sort-numeric-asc, sort3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-sort-numeric-desc"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9b8" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9b8;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="sort-numeric-desc, sort4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-sort-amount-asc"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9b9" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9b9;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="sort-amount-asc, sort5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-sort-amount-desc"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9ba" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9ba;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="sort-amount-desc, sort6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-sort-time-asc"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9bb" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9bb;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="sort-time-asc, sort7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-sort-time-desc"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9bc" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9bc;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="sort-time-desc, sort8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-sigma"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9bd" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9bd;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="sigma, symbols" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pencil-line"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9be" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9be;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pencil-line, border-color" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-hand"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9bf" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9bf;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="hand, drag" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pointer-up"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9c0" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9c0;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pointer-up, hand2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pointer-right"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9c1" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9c1;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pointer-right, hand3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pointer-down"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9c2" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9c2;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pointer-down, hand4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pointer-left"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9c3" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9c3;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pointer-left, hand5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-finger-tap"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9c4" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9c4;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="finger-tap, hand6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-fingers-tap"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9c5" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9c5;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="fingers-tap, hand7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-reminder"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9c6" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9c6;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="reminder, hand8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-fingers-crossed"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9c7" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9c7;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="fingers-crossed, hand9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-fingers-victory"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9c8" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9c8;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="fingers-victory, hand10" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-gesture-zoom"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9c9" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9c9;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="gesture-zoom, hand11" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-gesture-pinch"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9ca" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9ca;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="gesture-pinch, hand12" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-fingers-scroll-horizontal"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9cb" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9cb;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="fingers-scroll-horizontal, hand13" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-fingers-scroll-vertical"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9cc" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9cc;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="fingers-scroll-vertical, hand14" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-fingers-scroll-left"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9cd" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9cd;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="fingers-scroll-left, hand15" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-fingers-scroll-right"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9ce" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9ce;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="fingers-scroll-right, hand16" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-hand2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9cf" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9cf;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="hand17, drag2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pointer-up2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9d0" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9d0;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pointer-up2, hand18" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pointer-right2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9d1" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9d1;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pointer-right2, hand19" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pointer-down2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9d2" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9d2;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pointer-down2, hand20" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-pointer-left2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9d3" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9d3;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="pointer-left2, hand21" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-finger-tap2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9d4" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9d4;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="finger-tap2, hand22" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-fingers-tap2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9d5" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9d5;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="fingers-tap2, hand23" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-reminder2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9d6" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9d6;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="reminder2, hand24" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-gesture-zoom2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9d7" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9d7;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="gesture-zoom2, hand25" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-gesture-pinch2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9d8" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9d8;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="gesture-pinch2, hand26" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-fingers-scroll-horizontal2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9d9" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9d9;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="fingers-scroll-horizontal2, hand27" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-fingers-scroll-vertical2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9da" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9da;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="fingers-scroll-vertical2, hand28" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-fingers-scroll-left2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9db" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9db;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="fingers-scroll-left2, hand29" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-fingers-scroll-right2"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9dc" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9dc;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="fingers-scroll-right2, hand30" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-fingers-scroll-vertical3"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9dd" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9dd;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="fingers-scroll-vertical3, hand31" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-border-style"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9de" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9de;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="border-style, border" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-border-all"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9df" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9df;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="border-all, border2" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-border-outer"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9e0" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9e0;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="border-outer, border3" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-border-inner"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9e1" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9e1;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="border-inner, border4" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-border-top"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9e2" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9e2;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="border-top, border5" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-border-horizontal"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9e3" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9e3;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="border-horizontal, border6" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-border-bottom"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9e4" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9e4;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="border-bottom, border7" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-border-left"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9e5" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9e5;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="border-left, border8" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-border-vertical"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9e6" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9e6;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="border-vertical, border9" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-border-right"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9e7" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9e7;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="border-right, border10" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-border-none"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9e8" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9e8;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="border-none, border11" class="liga unitRight"  />
			</div>
		</div>
		<div class="glyph fs1">
			<div class="clearfix bshadow0 pbs">
				<span onclick="getIcon(this)" class="icon-ellipsis"></span>
			</div>
			<fieldset class="fs0 size1of1 clearfix hidden-false">
				<input type="text" readonly value="e9e9" class="unit size1of2" />
				<input type="text" maxlength="1" readonly value="&#xe9e9;" class="unitRight size1of2 talign-right" />
			</fieldset>
			<div class="fs0 bshadow0 clearfix hidden-false">
				<span class="unit pvs fgc1">liga: </span>
				<input type="text" readonly value="ellipsis, dots" class="liga unitRight"  />
			</div>
		</div>
	</div>';
		return $icons;
	}

}
?>