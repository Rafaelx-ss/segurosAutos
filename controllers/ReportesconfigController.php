<?php


namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\models\Reportesconfig;
use app\models\ReportesconfigSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use sam0786\fpdf\FPDFBitacora; 
use sam0786\phpexcel\PHPExcel;
use sam0786\fpdf\FPDFReporte;



/**
 * ReportesconfigController implements the CRUD actions for Reportesconfig model.
 */
class ReportesconfigController extends Controller
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
		
		
		if(isset($_GET['f']) and isset($_GET['r'])){
			$frmSeguridad = explode("/", $_GET['r']);
			
			$permisosBtn = Yii::$app->db->createCommand("SELECT Acciones.accionID, Acciones.nombreAccion, Formularios.nombreFormulario, Formularios.tipoFormularioID, Formularios.urlArchivo FROM Acciones
			inner join AccionesFormularios on Acciones.accionID=AccionesFormularios.accionID 
			inner join Formularios on AccionesFormularios.formularioID=Formularios.formularioID
			inner join PerfilAccionFormulario on AccionesFormularios.accionFormularioID =PerfilAccionFormulario.accionFormularioID
			inner join PerfilesCompuestos on PerfilAccionFormulario.perfilID=PerfilesCompuestos.perfilID
			inner join Usuarios on Usuarios.usuarioID = PerfilesCompuestos.usuarioID
			where md5(Formularios.formularioID)='".$_GET['f']."' and AccionesFormularios.estadoAccion=1 and AccionesFormularios.regEstado=1 and PerfilAccionFormulario.regEstado=1 and PerfilAccionFormulario.activoPerfilAccionFormulario=1 and Usuarios.usuarioID='".$usuarioIdToken."' group by AccionesFormularios.accionFormularioID")->queryAll();
			
			foreach($permisosBtn as $dataPbtn){
				$urlSeguridad = explode("/", $dataPbtn['urlArchivo']);
				if(isset($urlSeguridad[0]) and isset($frmSeguridad[0])){
					if($frmSeguridad[0] == $urlSeguridad[0]){
						if(isset($dataPbtn['accionID'])){
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
		}

        return [
			 'access' => [
                'class' => AccessControl::className(),
                'only' => ['create', 'update', 'delete', 'index', 'deletedata', 'createform', 'getselect', 'xportpdf', 'xportexcel', 'getcombo', 'getpdf', 'getexcel', 'updatecampos', 'reportes'],
                'rules' => [
                    [
						 'actions' => [$perAlta, $perEdit, 'delete', $perCons, $perElim , 'createform', 'getselect', $perPdf, $perExcel, 'getcombo', 'getdatacombo', 'getpdf', 'getexcel', 'updatecampos', 'reportes', 'getreport'],
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
	
	public function actionGetreport(){
		//echo "llegaron los datos";
		if(isset($_GET['send'])){
			$qryReporte = "";
			$qryCount = "";
			$arrayCampos = array();
			$arrayfCampos = array();
			$arrayPost = array();
			$cadQry = "";

			$errorPermisos = "false";

			if(isset($_GET['id']) ){
				//echo "SELECT * FROM ReportesConfiguraciones where md5(reporteConfiguracionID)='".$_GET['id']."'";
				$reporte = Yii::$app->db->createCommand("SELECT * FROM ReportesConfiguraciones where md5(reporteConfiguracionID)='".$_GET['id']."'")->queryOne();
				//print_r($reporte);
				
				if(isset($reporte['reporteConfiguracionID'])){		
					$qryRep = str_replace("FROM", "from", $reporte['queryReporte']);

					$qcount = explode("from", $qryRep);
					if(isset($qcount[1])){

						//echo "SELECT * FROM ReportesCampos where reporteConfiguracionID='".$reporte['reporteConfiguracionID']."'  order by orden";

						$rCampos = Yii::$app->db->createCommand("SELECT * FROM ReportesCampos where reporteConfiguracionID='".$reporte['reporteConfiguracionID']."'  order by orden")->queryAll();

						$adnCont = 1;
						foreach($rCampos as $rowCampos){

							if($rowCampos['searchVisible'] == 1){
								$arrayfCampos[] = array('tipoControl'=>$rowCampos['tipoControl'], 'nombreCampo'=>$rowCampos['nombreCampo'], 'traduccion'=>Yii::$app->globals->getTraductor($rowCampos['textoID'], Yii::$app->session['idiomaId'], $rowCampos['nombreCampo']), 'controlQuery'=>$rowCampos['controlQuery'], 'queryValor'=>$rowCampos['queryValor']);

								if($rowCampos['tipoControl'] == 'date'){
									//if(isset($_GET[$rowCampos['nombreCampo']."_ini"]) and isset($_GET[$rowCampos['nombreCampo']."_fin"])){

									if(isset($_GET[$rowCampos['nombreCampo']."_ini"]) and !empty($_GET[$rowCampos['nombreCampo']."_ini"]) and !is_null($_GET[$rowCampos['nombreCampo']."_ini"]) and isset($_GET[$rowCampos['nombreCampo']."_fin"]) and !empty($_GET[$rowCampos['nombreCampo']."_fin"]) and !is_null($_GET[$rowCampos['nombreCampo']."_fin"])){

										if($_GET[$rowCampos['nombreCampo']."_ini"] != ''){
											$arrayPost[$rowCampos['nombreCampo']."_ini"] = $_GET[$rowCampos['nombreCampo']."_ini"];
										}

										if($_GET[$rowCampos['nombreCampo']."_fin"] != ''){
											$arrayPost[$rowCampos['nombreCampo']."_fin"] = $_GET[$rowCampos['nombreCampo']."_fin"];
										}

										if($rowCampos['aliasTabla'] != ''){
											$aliast = $rowCampos['aliasTabla'].".";
										}else{
											$aliast = "";
										}

										if($adnCont == 1){								
											$cadQry .= $aliast.$rowCampos['nombreCampo']." >= '".$_GET[$rowCampos['nombreCampo']."_ini"]."' and ".$aliast.$rowCampos['nombreCampo']." <= '".$_GET[$rowCampos['nombreCampo']."_fin"]."'";
											$adnCont++;
										}else{
											//echo $adnCont."<br>";
											$cadQry .= " and  ".$aliast.$rowCampos['nombreCampo']." >= '".$_GET[$rowCampos['nombreCampo']."_ini"]."' and ".$aliast.$rowCampos['nombreCampo']." <= '".$_GET[$rowCampos['nombreCampo']."_fin"]."'";
											$adnCont++;
										}

									}


								}elseif($rowCampos['tipoControl'] == 'datetime'){
									//if(isset($_GET[$rowCampos['nombreCampo']."_ini"]) and isset($_GET[$rowCampos['nombreCampo']."_fin"])){

									if(isset($_GET[$rowCampos['nombreCampo']."_ini"]) and !empty($_GET[$rowCampos['nombreCampo']."_ini"]) and !is_null($_GET[$rowCampos['nombreCampo']."_ini"]) and isset($_GET[$rowCampos['nombreCampo']."_fin"]) and !empty($_GET[$rowCampos['nombreCampo']."_fin"]) and !is_null($_GET[$rowCampos['nombreCampo']."_fin"])){

										if($_GET[$rowCampos['nombreCampo']."_ini"] != ''){
											$arrayPost[$rowCampos['nombreCampo']."_ini"] = $_GET[$rowCampos['nombreCampo']."_ini"];
										}

										if($_GET[$rowCampos['nombreCampo']."_fin"] != ''){
											$arrayPost[$rowCampos['nombreCampo']."_fin"] = $_GET[$rowCampos['nombreCampo']."_fin"];
										}

										if($rowCampos['aliasTabla'] != ''){
											$aliast = $rowCampos['aliasTabla'].".";
										}else{
											$aliast = "";
										}
										
										if(isset($_GET['time_ini'])){
											$timeIni = $_GET['time_ini'];
										}
										
										if(isset($_GET['time_fin'])){
											$timeFin = $_GET['time_fin'];
										}

										if($adnCont == 1){								
											$cadQry .= $aliast.$rowCampos['nombreCampo']." >= '".$_GET[$rowCampos['nombreCampo']."_ini"]." ".$timeIni."' and ".$aliast.$rowCampos['nombreCampo']." <= '".$_GET[$rowCampos['nombreCampo']."_fin"]." ".$timeFin."'";
											$adnCont++;
										}else{
											//echo $adnCont."<br>";
											$cadQry .= " and  ".$aliast.$rowCampos['nombreCampo']." >= '".$_GET[$rowCampos['nombreCampo']."_ini"]." ".$timeIni."' and ".$aliast.$rowCampos['nombreCampo']." <= '".$_GET[$rowCampos['nombreCampo']."_fin"]." ".$timeFin."'";
											$adnCont++;
										}

									}


								}else if($rowCampos['tipoControl'] == 'select' or $rowCampos['tipoControl'] == 'array'){
									if(isset($_GET[$rowCampos['nombreCampo']]) and !empty($_GET[$rowCampos['nombreCampo']]) and !is_null($_GET[$rowCampos['nombreCampo']])){

											$arrayPost[$rowCampos['nombreCampo']] = $_GET[$rowCampos['nombreCampo']];

											if($rowCampos['aliasTabla'] != ''){
												$aliast = $rowCampos['aliasTabla'].".";
											}else{
												$aliast = "";
											}

											if($adnCont == 1){
												$cadQry .= $aliast.$rowCampos['nombreCampo']." = '".$_GET[$rowCampos['nombreCampo']]."' ";
												$adnCont++;
											}else{
												$cadQry .= " and ".$aliast.$rowCampos['nombreCampo']." = '".$_GET[$rowCampos['nombreCampo']]."' ";
												$adnCont++;
											}


									}

								}else if($rowCampos['tipoControl'] == 'checkbox'){
									if(isset($_GET[$rowCampos['nombreCampo']]) and !empty($_GET[$rowCampos['nombreCampo']]) and !is_null($_GET[$rowCampos['nombreCampo']])){

											$arrayPost[$rowCampos['nombreCampo']] = $_GET[$rowCampos['nombreCampo']];

											if($rowCampos['aliasTabla'] != ''){
												$aliast = $rowCampos['aliasTabla'].".";
											}else{
												$aliast = "";
											}

											if($adnCont == 1){
												$cadQry .= $aliast.$rowCampos['nombreCampo']." = '".$_GET[$rowCampos['nombreCampo']]."' ";
												$adnCont++;
											}else{
												$cadQry .= " and ".$aliast.$rowCampos['nombreCampo']." = '".$_GET[$rowCampos['nombreCampo']]."' ";
												$adnCont++;
											}


									}else{
										if($rowCampos['searchVisible'] == 1){
											if($rowCampos['aliasTabla'] != ''){
												$aliast = $rowCampos['aliasTabla'].".";
											}else{
												$aliast = "";
											}

											if($adnCont == 1){
												$cadQry .= $aliast.$rowCampos['nombreCampo']." = 0 ";
												$adnCont++;
											}else{
												$cadQry .= " and ".$aliast.$rowCampos['nombreCampo']." = 0 ";
												$adnCont++;
											}
										}
									}

								}else{
									if(isset($_GET[$rowCampos['nombreCampo']]) and !empty($_GET[$rowCampos['nombreCampo']]) and !is_null($_GET[$rowCampos['nombreCampo']])){

											$arrayPost[$rowCampos['nombreCampo']] = $_GET[$rowCampos['nombreCampo']];

											if($rowCampos['aliasTabla'] != ''){
												$aliast = $rowCampos['aliasTabla'].".";
											}else{
												$aliast = "";
											}

											if($adnCont == 1){
												$cadQry .= $aliast.$rowCampos['nombreCampo']." LIKE '%".$_GET[$rowCampos['nombreCampo']]."%' ";
												$adnCont++;
											}else{
												$cadQry .= " and ".$aliast.$rowCampos['nombreCampo']." LIKE '%".$_GET[$rowCampos['nombreCampo']]."%' ";
												$adnCont++;
											}



									}

								}


							}

							if($rowCampos['visible'] == 1){
								$cvisi = true;
							}else{
								$cvisi = false;
							}

							if($rowCampos['sumarCampo'] == 1){
								$arrayCampos[] = array('label'=>Yii::$app->globals->getTraductor($rowCampos['textoID'], Yii::$app->session['idiomaId'], $rowCampos['nombreCampo']), 'attribute'=>$rowCampos['nombreCampo'], 'value'=>$rowCampos['nombreCampo'], 'visible'=>$cvisi, 'suma'=>'Si');
							}else{
								$arrayCampos[] = array('label'=>Yii::$app->globals->getTraductor($rowCampos['textoID'], Yii::$app->session['idiomaId'], $rowCampos['nombreCampo']), 'attribute'=>$rowCampos['nombreCampo'], 'value'=>$rowCampos['nombreCampo'], 'visible'=>$cvisi, 'suma'=>'No');
							}


						}
						//getTotal($model, $count)
						//$dataProvider->models, $rowCampos['nombreCampo']

						$qryCounTemp = 0;
						if($cadQry == ''){
							$qryReporte = str_replace("?1", "1", $reporte['queryReporte']);

							$qryCounTemp = "Select count(*) FROM ".$qcount[1];	
							$qryCount = str_replace("?1", "1", $qryCounTemp);

						}else{
							$qryReporte = str_replace("?1", $cadQry, $reporte['queryReporte']);

							$qryCounTemp = "Select count(*) FROM ".$qcount[1];
							$qryCount = str_replace("?1", $cadQry, $qryCounTemp);
						}



					}

				}
			}

			//echo $qryReporte;
			$queryRow =  Yii::$app->db->createCommand($qryReporte)->queryAll();
			$arrayShow = array();
			
			foreach($queryRow as $row){
				$arrayNew = array();
				foreach($arrayCampos as $rcampos){
					if($rcampos['visible'] == 1){
						$arrayNew[$rcampos['label']] = $row[$rcampos['attribute']];
					}				
				}
				$arrayShow[] = $arrayNew;
			}
			
			
			//echo "<pre>";

			echo json_encode($arrayShow);
		}else{
			$arrayShow = array();
			echo json_encode($arrayShow);
		}
		exit;
	}
	
	 public function actionReportes($f, $id){
		 ini_set('memory_limit', '-1');   
		ini_set('max_execution_time', '0');  
		set_time_limit(0);
		 
        $searchModel = new ReportesconfigSearch();
      

		if(isset($_GET['clear'])){
			Yii::$app->session['ReportesconfigSearch'] = '';
			return $this->redirect(['index&f='.$f]);				
		}else{
			$params = Yii::$app->request->queryParams;

			if(count($params) <= 1){		  
				if(isset(Yii::$app->session['ReportesconfigSearch'])) {
					$params = Yii::$app->session['ReportesconfigSearch'];
				}else{
					Yii::$app->session['ReportesconfigSearch'] = $params;
				}
			}else{	
				if(isset(Yii::$app->request->queryParams['ReportesconfigSearch'])){
					Yii::$app->session['ReportesconfigSearch'] = $params;
				}else{
					$params = Yii::$app->session['ReportesconfigSearch'];
				}		
			}
		}
		
				 
        return $this->render('reportes', [
            'searchModel' => $searchModel,
			//'dataProvider' => $dataProvider,
        ]);
    }

	public function actionUpdatecampos($id, $f){
		$qry = Yii::$app->db->createCommand("SELECT * FROM ReportesConfiguraciones where reporteConfiguracionID='".$id."'")->queryOne();
		if(isset($qry['reporteConfiguracionID'])){
			$qrysel = str_replace("?1", "1", $qry['queryReporte']);
			$cabecera = Yii::$app->db->createCommand($qrysel)->queryOne();
			
			$arrayNames = array();
			
			//print_r($cabecera);
			$nameColum = array_keys($cabecera);
			
			foreach($nameColum as $naC){				
				$arrayNames[] =  $naC;
			}
			
			$arrayCampos = array();
			$campos = Yii::$app->db->createCommand("SELECT * FROM ReportesCampos where reporteConfiguracionID='".$id."'")->queryAll();
			
			foreach($campos as $rcampos){
				$arrayCampos[] = $rcampos['nombreCampo'];
			}
			
			$array_temp = array_intersect_assoc($arrayCampos, $arrayNames);
			$array_del = array_diff($arrayCampos, $array_temp);
			$array_insert = array_diff($arrayNames, $array_temp);
			
			//print_r($arrayNames);
			
			$c_del = count($array_del);
			if($c_del != 0){
				foreach($array_del as $val_del){
					Yii::$app->db->createCommand("DELETE FROM ReportesCampos WHERE reporteConfiguracionID='".$id."' and nombreCampo='".$val_del."'")->query();
				}
			}
			
			
			$c_insert = count($array_insert);
			if($c_insert != 0){
				$fID = Yii::$app->globals->getFormulario($f);
				
				$regEstado = 1;
				$regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
				$regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
				$versionRegistro = 1;
				$regVersionUltimaModificacion = Yii::$app->globals->getVersion();
				$regFormularioUltimaModificacion = $fID;
				
				$numIcr=1;
				foreach($array_insert as $val_insert){
					Yii::$app->db->createCommand("INSERT INTO ReportesCampos(reporteConfiguracionID, nombreCampo, visible, searchVisible, orden, textoID, tipoControl, sumarCampo, controlQuery, queryValor, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) VALUES ('".$id."', '".$val_insert."', 0, 0, '".$numIcr."', 1, 'text', 0, '', '', 1, 1, '".$regFechaUltimaModificacion."', '".$regUsuarioUltimaModificacion."', '".$regFormularioUltimaModificacion."', '".$regVersionUltimaModificacion ."')")->query();
					$numIcr++;
				}
			}
			
			//print_r($array_del);
			return $this->redirect(['update',  'f'=>$f, 'id'=>$id, 'update'=>'true']);
			
		}else{
			return $this->redirect(['update',  'f'=>$f, 'id'=>$id, 'update'=>'false']);
		}
	}

	public function actionGetpdf($f, $token, $data){
		//Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
		ini_set('memory_limit', '-1');   
		ini_set('max_execution_time', '0');  
		set_time_limit(0);
		Yii::$app->response->headers->add('Content-Type', 'application/pdf');
		
		
		$qry = Yii::$app->db->createCommand("SELECT * FROM ReportesConfiguraciones where md5(reporteConfiguracionID)='".$token."'")->queryOne();
		//print_r($qry);
		if(isset($qry['reporteConfiguracionID'])){
						
			$cabeceras = Yii::$app->db->createCommand("SELECT * FROM ReportesCampos where reporteConfiguracionID='".$qry['reporteConfiguracionID']."' and visible=1 order by orden")->queryAll();
			
			$arrayNames = array();
							
			foreach($cabeceras as $rowCampos){
				$arrayNames[] = array('campo'=>$rowCampos['nombreCampo'], 'traduccion'=>Yii::$app->globals->getTraductor($rowCampos['textoID'], Yii::$app->session['idiomaId'], $rowCampos['nombreCampo']), 'suma'=>$rowCampos['sumarCampo']);
			}
			
			
			$template = Yii::$app->db->createCommand("SELECT * FROM TemplatesReportes where templateReporteID='".$qry['templateReporteID']."'")->queryOne();
			
			$space = ' ';
			
			
			if($template['colorTituloTexto'] == ''){ $colorTablat = $this->hexToRgb("#ffffff"); }else{
				$colorTablat = $this->hexToRgb($template['colorTituloTexto']);
			}
			
			if($template['colorLinea'] == ''){ $colorLinea = $this->hexToRgb("#bbbbbb"); }else{
				$colorLinea = $this->hexToRgb($template['colorLinea']);
			}
			
			if($template['colorTituloTabla'] == ''){ $colorTabla = $this->hexToRgb("#848484"); }else{
				$colorTabla = $this->hexToRgb($template['colorTituloTabla']);
			}
			
			if($template['colorTextoFooter'] == ''){ $colorFooter = $this->hexToRgb("#ffffff"); }else{
				$colorFooter = $this->hexToRgb($template['colorTextoFooter']);
			}
			
								
			
			$c_11 = 229;
			$c_21 = 229;
			$c_31 = 229;
			
			
			$c_1 = 229;
			$c_2 = 229;
			$c_3 = 229;
			
			if(isset($colorLinea['r'])){ $c_1 = $colorLinea['r']; }
			if(isset($colorLinea['g'])){ $c_2 = $colorLinea['g']; }
			if(isset($colorLinea['b'])){ $c_3 = $colorLinea['b']; }
			
			// Creación del objeto de la clase heredada
			
			//aqui orientacion de la pagina
			
			if($qry['orientacionPagina'] == 'Vertical'){
				$orientacion = 'P';
				$posFecha = 122;
				$posPagerq = 205;
				$posFooterq = 211;
				$posContenido = 227;
				$posEncabezado = 210;
			}else{
				$orientacion = 'L';
				$posFecha = 182;
				$posPagerq = 265;
				$posFooterq = 275;
				$posContenido = 269;
				$posEncabezado = 270;
			}
			$pdf = new FPDFReporte($orientacion, 'mm', 'Letter');
			#Establecemos los márgenes izquierda, arriba y derecha: 
			$pdf->SetMargins(4, 10 , 5); 
			#Establecemos el margen inferior: 
			$pdf->SetAutoPageBreak(true,25);

			
			$pdf->posicionFecha = $posFecha;
			$pdf->posPager = $posPagerq;
			$pdf->posFooter = $posFooterq;
			
			$pdf->folio = 1;
			$pdf->fecha = date('Y-m-d H:i:s');
			$pdf->n_sitio = Yii::$app->user->identity->nombreUsuario;
			$pdf->logoReporte = $template['logoTemplateReporte'];
			$pdf->fl1 = $template['pieTemplateReporteL1'];
			$pdf->fl2 = $template['pieTemplateReporteL2'];
			$pdf->fl3 = $template['pieTemplateReporteL3'];
			
			if(isset($colorLinea['r'])){ $pdf->cl_1 = $colorLinea['r']; }
			if(isset($colorLinea['g'])){ $pdf->cl_2 = $colorLinea['g']; }
			if(isset($colorLinea['b'])){ $pdf->cl_3 = $colorLinea['b']; }
			
			$pdf->textColor = $colorFooter;
			$pdf->tablaColor = $colorTabla;
			
			$pdf->logoImpr =  $qry['imprimirLogoPdf'];
			$pdf->fechaImpr =  $qry['imprimirFechaHora'];
			$pdf->impUser =  $qry['imprimirNombreUsuario'];
			$pdf->impPie =  $qry['imprimirPie'];
			//$pdf->img_logo = $baseUrl.'/require/images/logo_pdf.jpg';
			//$pdf->Header($id);
			$pdf->SetDrawColor($c_11, $c_21, $c_31);	
			$pdf->AliasNbPages();
			$pdf->AddPage();

			if($qry['imprimirEncabezado'] == 1){
				$pdf->SetFont('Arial','',14);
				$pdf->Cell($posEncabezado,6,$template['encabezadoTemplateReporte'], 0,0,'C');
				$pdf->Ln(10);
			}
			
			//inicia el contenido
			
			
			$miCabecera = array();
			$sumaTraducion = array();
			//227
			$espaciosEle = $posContenido/count($arrayNames);
			$dEspEl = array();
			foreach($arrayNames as $rName){					
				$miCabecera[] =  $rName['traduccion'];
				$dEspEl[] = $espaciosEle;
				
				if($rName['suma']==1){
					$sumaTraducion[$rName['campo']] = $rName['traduccion'];
				}
			}	
				
		    $pdf->SetTextColor($colorTablat['r'], $colorTablat['g'], $colorTablat['b']);
			$pdf->cabeceraHorizontal($miCabecera, $posContenido);
			$pdf->Ln(7);
			
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetFont('Arial', '', 10);
			$pdf->SetWidths($dEspEl);
			
			
			if($data == ""){
				$qryReporte = str_replace("?1", "1", $qry['queryReporte']);
				if(isset($_GET['cvacio'])){
					$qryAll = Yii::$app->db->createCommand($qryReporte)->queryAll();

					$arraySuma = array();
					foreach($qryAll as $rowData){
						$arLinea = array();
						foreach($arrayNames as $rname){
							$arLinea[] = $rowData[$rname['campo']];
							if($rname['suma']==1){
								$arraySuma[$rname['campo']][] = $rowData[$rname['campo']];
							}
							//$objSheet->setCellValue($numAlf.$cdata, $rowData[$rname]);					
						}
						$pdf->Row($arLinea);

					}
				}
				
			}else{
				$qryReporte = str_replace("?1", html_entity_decode($data), $qry['queryReporte']);
									
				$qryAll = Yii::$app->db->createCommand($qryReporte)->queryAll();
				$arraySuma = array();
				foreach($qryAll as $rowData){
					$arLinea = array();
					foreach($arrayNames as $rname){
						$arLinea[] = $rowData[$rname['campo']];
						//$objSheet->setCellValue($numAlf.$cdata, $rowData[$rname]);
						if($rname['suma']==1){
							$arraySuma[$rname['campo']][] = $rowData[$rname['campo']];
						}
					}
					$pdf->Row($arLinea);

				}
			}
			$pdf->Ln(10);
			//print_r($sumaTraducion);
			foreach($arraySuma as $key=>$value){
				//echo ;
				$totalSVal = 0;
				foreach($value as $sumaVal){
					$totalSVal = $totalSVal + $sumaVal;
				}
				//echo $totalSVal."<br>";
				$pdf->Cell(10, 6 , "Total ".$sumaTraducion[$key]." = ".$totalSVal, 0,0,'L');
				$pdf->Ln(7);
			}
			$pdf->Output();	
			exit;
		}else{
			return $this->redirect(['reportes',  'id'=>$token, 'f'=>$f, 'exportar'=>'false']);
		}
	}
	
	public function actionGetexcel($f, $token, $data){
		ini_set('memory_limit', '-1');   
		ini_set('max_execution_time', '0');  
		set_time_limit(0);
		$qry = Yii::$app->db->createCommand("SELECT * FROM ReportesConfiguraciones where md5(reporteConfiguracionID)='".$token."'")->queryOne();
		//print_r($qry);
		if(isset($qry['reporteConfiguracionID'])){
			$cabeceras = Yii::$app->db->createCommand("SELECT * FROM ReportesCampos where reporteConfiguracionID='".$qry['reporteConfiguracionID']."' and visible=1 order by orden")->queryAll();
			
			$arrayNames = array();
							
			foreach($cabeceras as $rowCampos){
				$arrayNames[] = array('campo'=>$rowCampos['nombreCampo'], 'traduccion'=>Yii::$app->globals->getTraductor($rowCampos['textoID'], Yii::$app->session['idiomaId'], $rowCampos['nombreCampo']));
			}
			
			$objPHPExcel = new \PHPExcel();
			$objSheet = $objPHPExcel->setActiveSheetIndex(0);

			$objPHPExcel->getProperties()
							->setCreator("reporte")
							->setLastModifiedBy("reporte")
							->setTitle("Reporte")
							->setSubject("Reporte")
							->setDescription("Reporte")
							->setCategory("Reportes");
			$template = Yii::$app->db->createCommand("SELECT * FROM TemplatesReportes where templateReporteID='".$qry['templateReporteID']."'")->queryOne();
			
			if($qry['imprimirLogoExcel'] == 1){
				$objDrawing = new \PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('logo Empresa');
				$objDrawing->setDescription('logo Empresa');
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
				
				$nombre_fichero = Yii::$app->basePath."/web/tlogos/".$template['logoTemplateReporte'];
				if (file_exists($nombre_fichero)) {
					$objDrawing->setPath(Yii::$app->basePath.'/web/tlogos/'.$template['logoTemplateReporte']);
				}else{
					$objDrawing->setPath(Yii::$app->basePath.'/web/tlogos/logo_blanco.jpg');
				}
			
				
				$objDrawing->setWidth(160);
				$objDrawing->setCoordinates('B2');
			}
			
			$objPHPExcel->getActiveSheet()->getStyle('B2:J2')->getFont()->setBold(true);
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B2:J2');
			$objPHPExcel->getActiveSheet()->getStyle('B2')->getAlignment()->applyFromArray(
				array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
			);
			
			$objPHPExcel->getActiveSheet()->getStyle('B3:J3')->getFont()->setBold(true);
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B3:J3');
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->applyFromArray(
				array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
			);
			
			$objPHPExcel->getActiveSheet()->getStyle("B2:J2")->getFont()->setSize(18);
			$objPHPExcel->getActiveSheet()->getStyle("B3:J3")->getFont()->setSize(14);
			
			if($qry['imprimirEncabezadoExcel'] == 1){
				$objSheet->setCellValue('B2', $template['encabezadoTemplateReporte']);
			}
			
			if($qry['imprimirFechaHoraExcel'] == 1){
				$objSheet->setCellValue('B3', date('Y-m-d H:i:s'));
			}
			
			$total_names = count($arrayNames);
			$num = 1;
			for ($i="B" ; $i!="AZY" ; $i++) {
				if($num == $total_names){
					$letra = $i;
					break;
				}
				$num++;				
			}
			
			$objPHPExcel->getActiveSheet()->getStyle('B5:'.$letra.'5')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('B5:'.$letra.'5')->getAlignment()->applyFromArray(
				array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
			);
			$objPHPExcel->getActiveSheet()->getStyle("B5:".$letra."5")->getFont()->setSize(11);

			$objPHPExcel->getActiveSheet()
				->getStyle('B5:'.$letra.'5')
				->getFill()
				->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)
				->getStartColor()
				->setRGB('e5e5e5');
			
			$border_style= array('borders' => array('right' => array('style' => \PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => '000000')), 'left' => array('style' => \PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => '000000')), 'top' => array('style' => \PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => '000000')), 'bottom' => array('style' => \PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => '000000')),));
			$sheet = $objPHPExcel->getActiveSheet();
			
			$num = 1;
			for ($i="B"; $i!="AZY" ; $i++) {
				$sheet->getStyle($i.'5')->applyFromArray($border_style);
				if($num == $total_names){
					break;
				}
				$num++;				
			}
			
			$sheet->getStyle("B5:".$letra."5")->applyFromArray($border_style);
			$objPHPExcel->getActiveSheet()->getRowDimension('5')->setRowHeight(35);
			$style = array(
				'alignment' => array(
					'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
				)
			);

				
			
			
			$num = 1;
			$dnum = 0;
			for ($i="B" ; $i!="AZY" ; $i++) {
				$objSheet->setCellValue($i.'5', $arrayNames[$dnum]['traduccion']);
				if($num == $total_names){
					//$letra = $i;					
					break;
				}
				$num++;
				$dnum ++;
			}
			
			//$qryAll = Yii::$app->db->createCommand($qry['queryReporte'])->queryAll();
			
			
			
			if($data == ""){
				$qryReporte = str_replace("?1", "1", $qry['queryReporte']);
				if(isset($_GET['cvacio'])){
					$qryAll = Yii::$app->db->createCommand($qryReporte)->queryAll();

					$qryAll = Yii::$app->db->createCommand($qryReporte)->queryAll();
					$cdata = 6;
					$num = 1;
					$numName = 0;

					foreach($qryAll as $rowData){
						$numAlf = 'B';
						foreach($arrayNames as $rname){
							$objSheet->setCellValue($numAlf.$cdata, $rowData[$rname['campo']]);
							$numAlf ++;
						}

						$num ++;
						$cdata ++;				
					}
				}
				
			}else{				
				$qryReporte = str_replace("?1",  html_entity_decode($data), $qry['queryReporte']);
			
			
				$qryAll = Yii::$app->db->createCommand($qryReporte)->queryAll();
				$cdata = 6;
				$num = 1;
				$numName = 0;

				foreach($qryAll as $rowData){
					$numAlf = 'B';
					foreach($arrayNames as $rname){
						$objSheet->setCellValue($numAlf.$cdata, $rowData[$rname['campo']]);
						$numAlf ++;
					}

					$num ++;
					$cdata ++;				
				}
			}
			
			
			$numd = 1;
			for ($i="B" ; $i!="AZY" ; $i++) {
				//aqui se pone el resize
				$objPHPExcel->getActiveSheet()->getColumnDimension($i) ->setAutoSize(true);
				if($numd == $total_names){
					//$letra = $i;
					break;
				}
				$numd++;				
			}
			
			
			//imprimir reporte en excel
			$objPHPExcel->getActiveSheet()->setTitle('Reportes_'.date('Ymdhi'));
			$objPHPExcel->setActiveSheetIndex(0);




			Yii::$app->response->headers->add('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="Reportes_'.date('ymdHis').'.xlsx"');
			header('Cache-Control: max-age=0');
			$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('php://output');			
			exit;
			
			
		}else{
			return $this->redirect(['reportes',  'f'=>$f, 'exportar'=>'false']);
		}
	}
	
	
	public function actionGetdatacombo($q = null, $id = null, $campo, $consulta){
		 
		
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;//restituisco json
        $out = ['results' => [$campo => '', 'text' => '']];
        if (!is_null($q)) {
		   $query_search = str_replace("?1", "'%".$q."%'", $consulta);

           $data = Yii::$app->db->createCommand($query_search)->queryAll();
		   $out['results'] = array_values($data);
        }
        elseif ($id > 0) {
            $out['results'] = [$campo => '', 'text' => 'No encontramos coincidencias'];
        }

        return $out;


	}

	//get Combo anidado 
	 public function actionGetcombo(){
		 if(isset($_GET['token']) and isset($_GET['idCombo'])){
			 $combo = Yii::$app->db->createCommand("SELECT * FROM CombosAnidados where comboAnidadoID='".$_GET['idCombo']."'")->queryOne();
			 $query_search = str_replace("'?'", "'".$_GET['token']."'", $combo['controlQuery']);

			 if(!empty($combo['parametrosQuery']) and !is_null($combo['parametrosQuery'])){
				 $parametros = explode(",", $combo['parametrosQuery']);
				 
				 $np = 1;
				 foreach($parametros as $rparam){
					 $query_search = str_replace("'?".$np."'", "'".$_GET['k'.$np]."'", $query_search);
				 }
			 }
			
			 $campos = Yii::$app->db->createCommand($query_search)->queryAll();
			 echo '<option value=""> -- Selecciona -- </option>';
			 foreach($campos as $row){
				 echo '<option value="'.$row[$combo['queryValue']].'">'.$row[$combo['queryText']].'</option>';				 
			 }
		 }else{
			  echo '<option value=""> -- Selecciona -- </option>';
		 }
	 }

	//get select 
	 public function actionGetselect(){
		 if(isset($_POST['qry'])){
			 $campos = Yii::$app->db->createCommand($_POST['qry'])->queryAll();
			 echo '<option value=""> -- Selecciona -- </option>';
			 foreach($campos as $row){
				 echo '<option value="'.$row[$_POST['valueField']].'">'.$row[$_POST['textField']].'</option>';				 
			 }
		 }
	 }



	public function actionXportpdf($f){
		ini_set('memory_limit', '-1');   
		ini_set('max_execution_time', '0');  
		set_time_limit(0);
		$searchModel = new ReportesconfigSearch();
		
		if(isset(Yii::$app->session['ReportesconfigSearch'])) {

			$fID = Yii::$app->globals->getFormulario($f);
			$rauditoria = Yii::$app->globals->setRegistro(7, 'PDF', '', $fID);
			
			$catalogo = Yii::$app->db->createCommand("SELECT catalogoID FROM Catalogos where nombreModelo='Reportesconfig'")->queryOne();
			if(isset($catalogo['catalogoID'])){
				$cabecera = Yii::$app->db->createCommand("SELECT cabeceraExportar FROM ExportarCatalogos where catalogoID='".$catalogo['catalogoID']."'")->queryOne();
				
				if(isset($cabecera['cabeceraExportar'])){ $dCabecera = $cabecera['cabeceraExportar']; }else{ $dCabecera = 'Reporte Reportesconfig'; }
				$camposGrid = Yii::$app->db->createCommand("SELECT * FROM CamposGrid where catalogoID='".$catalogo['catalogoID']."' and visible='1' order by orden")->queryAll();
				
				$arrayNames = array();
				foreach($camposGrid as $rcgrid){
					if($rcgrid['tipoControl'] == 'select'){
						$catModel = Yii::$app->db->createCommand("SELECT nombreModelo FROM Catalogos where catalogoID='".$rcgrid['catalogoReferenciaID']."'")->queryOne();
						
						if(isset($catModel['nombreModelo'])){
							$arrayNames[] = array($rcgrid['textoID'], $rcgrid['nombreCampo'], 'id'.$catModel['nombreModelo'], $rcgrid['textField']);
						}else{
							$arrayNames[] = array($rcgrid['textoID'], $rcgrid['nombreCampo'], $rcgrid['nombreCampo']);
						}
					}else{
						$arrayNames[] = array($rcgrid['textoID'], $rcgrid['nombreCampo'], $rcgrid['nombreCampo']);
					}					
				}
				
				Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
  				Yii::$app->response->headers->add('Content-Type', 'application/pdf');
				//Yii::$app->response->headers->add('Content-Type', 'application/pdf');
				$space = ' ';
				
				// Creación del objeto de la clase heredada
				$pdf = new FPDFBitacora('P', 'mm', 'Letter');
				#Establecemos los márgenes izquierda, arriba y derecha: 
				$pdf->SetMargins(5, 10 , 5); 
				#Establecemos el margen inferior: 
				$pdf->SetAutoPageBreak(true,25);

				$pdf->titulo = $dCabecera;
				
				$pdf->AliasNbPages();
				$pdf->AddPage();
				
				$miCabecera = array();
				$espaciosEle = 205/count($arrayNames);
				$dEspEl = array();
				foreach($arrayNames as $rName){					
					$miCabecera[] =  Yii::$app->globals->getTraductor($rName[0], Yii::$app->session['idiomaId'], $rName[1]);
					$dEspEl[] = $espaciosEle;
				}	
				
				$pdf->cabeceraHorizontal($miCabecera);
				$pdf->Ln(7);
				
				
				$pdf->SetFont('Arial', '', 10);
				$pdf->SetWidths($dEspEl);
				
				$dataProvider = $searchModel->search(Yii::$app->session['ReportesconfigSearch']);
				$search = $dataProvider->query->all();
				
				foreach($search as $row){
					$arLinea = array();
					foreach($arrayNames as $rowName){
						if(isset($rowName[3])){
							$arLinea[] = $row[$rowName[2]][$rowName[3]];
						}else{
							$arLinea[] = $row[$rowName[2]];
						}	
					}
					$pdf->Row($arLinea);
				}
				
				$pdf->Output();	
				
				
				
			}else{
				return $this->redirect(['index',  'f'=>$f, 'exportar'=>'false']);
			}
		}else{
			return $this->redirect(['index',  'f'=>$f, 'exportar'=>'false']);
		}
		
		
	}
    /**
     * Lists all Almacenes models.
     * @return mixed
     */
	public function actionXportexcel($f){
		ini_set('memory_limit', '-1');   
		ini_set('max_execution_time', '0');  
		set_time_limit(0);
		$searchModel = new ReportesconfigSearch();
		
		if(isset(Yii::$app->session['ReportesconfigSearch'])) {

			$fID = Yii::$app->globals->getFormulario($f);
			$rauditoria = Yii::$app->globals->setRegistro(6, 'Excel', '', $fID);
			
			$catalogo = Yii::$app->db->createCommand("SELECT catalogoID FROM Catalogos where nombreModelo='Reportesconfig'")->queryOne();
			if(isset($catalogo['catalogoID'])){
				$cabecera = Yii::$app->db->createCommand("SELECT cabeceraExportar FROM ExportarCatalogos where catalogoID='".$catalogo['catalogoID']."'")->queryOne();
				
				if(isset($cabecera['cabeceraExportar'])){ $dCabecera = $cabecera['cabeceraExportar']; }else{ $dCabecera = 'Reporte Reportesconfig'; }
				$camposGrid = Yii::$app->db->createCommand("SELECT * FROM CamposGrid where catalogoID='".$catalogo['catalogoID']."' and visible='1' order by orden")->queryAll();
				
				$arrayNames = array();
				foreach($camposGrid as $rcgrid){
					if($rcgrid['tipoControl'] == 'select'){
						$catModel = Yii::$app->db->createCommand("SELECT nombreModelo FROM Catalogos where catalogoID='".$rcgrid['catalogoReferenciaID']."'")->queryOne();
						
						if(isset($catModel['nombreModelo'])){
							$arrayNames[] = array($rcgrid['textoID'], $rcgrid['nombreCampo'], 'id'.$catModel['nombreModelo'], $rcgrid['textField']);
						}else{
							$arrayNames[] = array($rcgrid['textoID'], $rcgrid['nombreCampo'], $rcgrid['nombreCampo']);
						}
					}else{
						$arrayNames[] = array($rcgrid['textoID'], $rcgrid['nombreCampo'], $rcgrid['nombreCampo']);
					}					
				}
					
				//inician los datos
				$objPHPExcel = new \PHPExcel();
	
				$objSheet = $objPHPExcel->setActiveSheetIndex(0);

				$objPHPExcel->getProperties()
							->setCreator("reporte")
							->setLastModifiedBy("reporte")
							->setTitle("Reporte")
							->setSubject("Reporte")
							->setDescription("Reporte")
							->setCategory("Reportes");

				
				$num = 1;
				$totalElementos = count($arrayNames);
				for ($i="B" ; $i!="DY" ; $i++) {
					if($num == $totalElementos){
						$letra = $i;
						break;
					}
					$num++;				
				}
				
				$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B1:'.$letra.'1');
				$objPHPExcel->getActiveSheet()->getStyle('B1')->getAlignment()->applyFromArray(
					array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
				);

				$objPHPExcel->getActiveSheet()->getStyle("B1")->getFont()->setBold(true);

				$objSheet->setCellValue('B1', $dCabecera);
						//c4d79b
				$objPHPExcel->getActiveSheet()
							->getStyle('B3:'.$letra.'3')
							->applyFromArray(
								array(
									'fill' => array(
										'type' => \PHPExcel_Style_Fill::FILL_SOLID,
										'color' => array('rgb' => 'b61111')
									)
								)
							);

				$styleArray = array(
						  'borders' => array(
							'allborders' => array(
								'style' => \PHPExcel_Style_Border::BORDER_THIN,
								'color' => array('rgb' => '000000')
							)
						  )
				);

				$styleArrayt = array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => 'ffffff'),
							'size'  => 12,
				));

				$objPHPExcel->getActiveSheet()->getStyle("B3:".$letra."3")->applyFromArray($styleArray);
				$objPHPExcel->getActiveSheet()->getStyle("B3:".$letra."3")->applyFromArray($styleArrayt);

				$objPHPExcel->setActiveSheetIndex(0);
				
				$titulos = array();
				foreach($arrayNames as $rName){
					$titulos[] =  Yii::$app->globals->getTraductor($rName[0], Yii::$app->session['idiomaId'], $rName[1]);			
				}	
				

				$tnum = 0;
				for ($i="B"; $i!="DY"; $i++) {
					if(isset($titulos[$tnum])){
						$objPHPExcel->getActiveSheet()->SetCellValue($i.'3', $titulos[$tnum]);
					}
					$tnum ++;
				}

				/* inicia el contenido */


				/* finaliza el contenido */

				for ($i="B" ; $i!="DY" ; $i++) {
					$objPHPExcel->getActiveSheet()->getColumnDimension($i)->setAutoSize(true);
				}
				
				//$objPHPExcel->getActiveSheet()->SetCellValue('AI'.$cdata, $row['Monto_gad']);
				
				$dataProvider = $searchModel->search(Yii::$app->session['ReportesconfigSearch']);
				$search = $dataProvider->query->all();
				$cdata = 4;
				
				foreach($search as $row){
					$alFbt="B";
					foreach($arrayNames as $rowName){
						if(isset($rowName[3])){
							//echo $row[$rowName[2]][$rowName[3]]."<br>";
							$objPHPExcel->getActiveSheet()->SetCellValue($alFbt.$cdata, $row[$rowName[2]][$rowName[3]]);
						}else{
							//echo $row[$rowName[2]]."<br>";
							$objPHPExcel->getActiveSheet()->SetCellValue($alFbt.$cdata,  $row[$rowName[2]]);
						}	
						$alFbt++;
					}
					$cdata++;
				}


				$objPHPExcel->getActiveSheet()->setTitle('Reportes_'.date('Ymdhi'));
				$objPHPExcel->setActiveSheetIndex(0);




				Yii::$app->response->headers->add('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="Reportesconfig_'.date('ymdHis').'.xlsx"');
				header('Cache-Control: max-age=0');
				$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				$objWriter->save('php://output');			
				exit;
								
				
				//terminan los datos
			}else{
				return $this->redirect(['index',  'f'=>$f, 'exportar'=>'false']);
			}			
		}else{
			return $this->redirect(['index',  'f'=>$f, 'exportar'=>'false']);
		}
		
		//$dataProvider = $searchModel->search($params);
		//$dataProvider->pagination->pageSize = Yii::$app->params['npag'];
	}

    /**
     * Lists all Reportesconfig models.
     * @return mixed
     */
    public function actionIndex($f)
    {
        $searchModel = new ReportesconfigSearch();
      

		if(isset($_GET['clear'])){
			Yii::$app->session['ReportesconfigSearch'] = '';
			return $this->redirect(['index&f='.$f]);				
		}else{
			$params = Yii::$app->request->queryParams;

			if(count($params) <= 1){		  
				if(isset(Yii::$app->session['ReportesconfigSearch'])) {
					$params = Yii::$app->session['ReportesconfigSearch'];
				}else{
					Yii::$app->session['ReportesconfigSearch'] = $params;
				}
			}else{	
				if(isset(Yii::$app->request->queryParams['ReportesconfigSearch'])){
					Yii::$app->session['ReportesconfigSearch'] = $params;
				}else{
					$params = Yii::$app->session['ReportesconfigSearch'];
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
     * Displays a single Reportesconfig model.
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
							
	//formulario para crate ajax
	public function actionCreateform()
    {
        $model = new Reportesconfig();
				
		if($model->load(Yii::$app->request->post())) {
			$post = Yii::$app->request->post();
			$model->regEstado = 1;
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = 1;
			$model->columnasReporte = "NA";
			
			if($model->save()){
								
				echo 'true';
			}else{
				echo 'false';
			}
		}else{
			echo 'false';
		}
    }

    /**
     * Creates a new Reportesconfig model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($f)
    {
        $model = new Reportesconfig();
				
		if($model->load(Yii::$app->request->post())) {
			$fID = Yii::$app->globals->getFormulario($f);
				
			$post = Yii::$app->request->post();
			$model->regEstado = 1;
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = $fID;
			$model->columnasReporte = 'NA';
			
			if($model->save()){
				
				$rauditoria = Yii::$app->globals->setRegistro(3, 'Alta', '', $fID);				
				return $this->redirect(['create', 'f'=>$f, 'insert' => 'true', 'id'=>$model->reporteConfiguracionID]);
			}
		}
		

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Reportesconfig model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($f, $id)
    {
        $model = $this->findModel($id);
		$version = $model->versionRegistro;		
		if($model->load(Yii::$app->request->post())) {
			$fID = Yii::$app->globals->getFormulario($f);
				
			$post = Yii::$app->request->post();
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = $version + 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = $fID;
			
			if($model->save()){
				
				$rauditoria = Yii::$app->globals->setRegistro(5, 'Editar', '', $fID);	
				return $this->redirect(['update', 'f'=>$f, 'id' => $model->reporteConfiguracionID, 'update'=>'true']);
			}
		}

        return $this->render('update', [
            'model' => $model,
        ]);
    }
				
	 public function actionDeletedata($f, $id)
    {
		$fID = Yii::$app->globals->getFormulario($f);
        $model = $this->findModel($id);
		$version = $model->versionRegistro;	
				
		$model->regEstado = 0;	
		$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
		$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
		$model->versionRegistro = $version + 1;
		$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
		$model->regFormularioUltimaModificacion = $fID;
		if($model->save()){
			
			$rauditoria = Yii::$app->globals->setRegistro(4, 'Eliminar', '', $fID);	
			return $this->redirect(['index',  'f'=>$f, 'delete'=>'true']);
		}
		

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Reportesconfig model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
   
				
	public function actionDelete(){				
		$true = 0;	
		if ($selection=(array)Yii::$app->request->post('selection')) {
			$fID = Yii::$app->globals->getFormulario($_POST['f']);
				
			$arrayReg = array();
			$registros = Yii::$app->db->createCommand('SELECT nombreCampo, campoPK FROM Campos WHERE catalogoID="" and campoPK="1"')->queryAll();
				
			foreach($registros as $row){
				$arrayReg[] = $row['nombreCampo'];
			}
				
			foreach($selection as $id){
				$model = $this->findModel($id);		
				$version = $model->versionRegistro;	
				
				$model->load(Yii::$app->request->post());	        
				$model->regEstado= '0';	
				$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
				$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
				$model->versionRegistro = $version+1;
				$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
				$model->regFormularioUltimaModificacion = $fID;
				
				 if($model->save()){					
					$rauditoria = Yii::$app->globals->setRegistro(4, 'Eliminar', '', $fID);
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
	
	public function hexToRgb($hex, $alpha = false){
	   $hex      = str_replace('#', '', $hex);
	   $length   = strlen($hex);
	   $rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
	   $rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
	   $rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
	   if ( $alpha ) {
		  $rgb['a'] = $alpha;
	   }
	   return $rgb;
	}

    /**
     * Finds the Reportesconfig model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Reportesconfig the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Reportesconfig::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
