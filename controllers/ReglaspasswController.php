<?php


namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\models\Reglaspassw;
use app\models\ReglaspasswSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use sam0786\fpdf\FPDFBitacora; 
use sam0786\phpexcel\PHPExcel; 

/**
 * ReglaspasswController implements the CRUD actions for Reglaspassw model.
 */
class ReglaspasswController extends Controller
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

		$actions = Yii::$app->globals->getPaginaInicialControlador();
		if(isset($_GET['f']) and isset($_GET['r'])){						
			$permisosBtn = Yii::$app->globals->getPermisoControlador($_GET['f'], $usuarioIdToken, $_GET['r']);	
			$actions = array_merge($actions, $permisosBtn);			
		}		
				
        return [
			 'access' => [
                'class' => AccessControl::className(),
                'only' => Yii::$app->globals->getPaginasControlador(),
                'rules' => [
                    [
						'actions' => $actions,
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
		exit;

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

		exit;
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
		exit;
	 }



	public function actionXportpdf($f){
		ini_set('memory_limit', '-1');   
		ini_set('max_execution_time', '0');  
		set_time_limit(0);

		$searchModel = new ReglaspasswSearch();
		
		if(isset(Yii::$app->session['ReglaspasswSearch'])) {

			$fID = Yii::$app->globals->getFormulario($f);
			$rauditoria = Yii::$app->globals->setRegistro(7, 'PDF', 'ReglasPassw', $fID);
			
			$catalogo = Yii::$app->db->createCommand("SELECT catalogoID FROM Catalogos where nombreModelo='Reglaspassw'")->queryOne();
			if(isset($catalogo['catalogoID'])){
				$cabecera = Yii::$app->db->createCommand("SELECT cabeceraExportar FROM ExportarCatalogos where catalogoID='".$catalogo['catalogoID']."'")->queryOne();
				
				if(isset($cabecera['cabeceraExportar'])){ $dCabecera = $cabecera['cabeceraExportar']; }else{ $dCabecera = 'Reporte Reglaspassw'; }
				$camposGrid = Yii::$app->db->createCommand("SELECT * FROM CamposGrid where catalogoID='".$catalogo['catalogoID']."' and visible='1' order by orden")->queryAll();
				
				$arrayNames = array();
				foreach($camposGrid as $rcgrid){
					if($rcgrid['tipoControl'] == 'select'){
						$catModel = Yii::$app->db->createCommand("SELECT * FROM Catalogos where catalogoID='".$rcgrid['catalogoReferenciaID']."'")->queryOne();
						
						if(isset($catModel['nombreModelo'])){
							$arrayNames[] = array($rcgrid['textoID'], $rcgrid['nombreCampo'], 'id'.$catModel['nombreModelo'], $rcgrid['textField'], $rcgrid['tipoControl']);
						}else{
							$arrayNames[] = array($rcgrid['textoID'], $rcgrid['nombreCampo'], $rcgrid['nombreCampo'], 'Na', 'text');
						}						
					}elseif($rcgrid['tipoControl'] == 'consulta'){
						$arrayNames[] = array($rcgrid['textoID'], $rcgrid['nombreCampo'], $rcgrid['valorDefault'], $rcgrid['textField'], $rcgrid['tipoControl'], $rcgrid['queryValor'], $rcgrid['controlQuery']);
					}else{
						$arrayNames[] = array($rcgrid['textoID'], $rcgrid['nombreCampo'], $rcgrid['nombreCampo'], 'Na', $rcgrid['tipoControl']);
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
				
				$dataProvider = $searchModel->search(Yii::$app->session['ReglaspasswSearch']);
				$search = $dataProvider->query->all();
				
				foreach($search as $row){
					$arLinea = array();
					foreach($arrayNames as $rowName){
						if(isset($rowName[4])){
							if($rowName[4] == 'select'){
								if(isset($row[$rowName[2]][$rowName[3]])){
									$arLinea[] = $row[$rowName[2]][$rowName[3]];
								}else{
									$arLinea[] = 'N/D';
								}
							}elseif($rowName[4] == 'consulta'){
								$valores = explode(",", $rowName[5]);
								
								if(count($valores) == 1){
									$nQry = str_replace("'?1'", "'".$row[$valores[0]]."'", $rowName[6]);
								}else{
									$inc = 1;
									$nQry = $rowName[6];
									foreach($valores as $dataVal){
										$nQry = str_replace("'?".$inc."'", "'".$row[$dataVal]."'", $nQry);
										$inc++;
									}
								}
								
								$dataQury = Yii::$app->db->createCommand($nQry)->queryOne();
								if(isset($dataQury[$rowName[2]])){
									$arLinea[] = $dataQury[$rowName[2]];
								}else{
									$arLinea[] = "N/D";
								}
								
							}elseif($rowName[4] == 'checkbox'){
								$valChk = 'No';
								if($row[$rowName[2]] == 1){
									$valChk = 'Si';
								}
								
								$arLinea[] = $valChk;
							}else{
								$arLinea[] = $row[$rowName[2]];
							}
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

		$searchModel = new ReglaspasswSearch();
		
		if(isset(Yii::$app->session['ReglaspasswSearch'])) {

			$fID = Yii::$app->globals->getFormulario($f);
			$rauditoria = Yii::$app->globals->setRegistro(6, 'Excel', 'ReglasPassw', $fID);
			
			$catalogo = Yii::$app->db->createCommand("SELECT catalogoID FROM Catalogos where nombreModelo='Reglaspassw'")->queryOne();
			if(isset($catalogo['catalogoID'])){
				$cabecera = Yii::$app->db->createCommand("SELECT cabeceraExportar FROM ExportarCatalogos where catalogoID='".$catalogo['catalogoID']."'")->queryOne();
				
				if(isset($cabecera['cabeceraExportar'])){ $dCabecera = $cabecera['cabeceraExportar']; }else{ $dCabecera = 'Reporte Reglaspassw'; }
				$camposGrid = Yii::$app->db->createCommand("SELECT * FROM CamposGrid where catalogoID='".$catalogo['catalogoID']."' and visible='1' order by orden")->queryAll();
				
				$arrayNames = array();
				foreach($camposGrid as $rcgrid){
					if($rcgrid['tipoControl'] == 'select'){
						$catModel = Yii::$app->db->createCommand("SELECT * FROM Catalogos where catalogoID='".$rcgrid['catalogoReferenciaID']."'")->queryOne();
						
						if(isset($catModel['nombreModelo'])){
							$arrayNames[] = array($rcgrid['textoID'], $rcgrid['nombreCampo'], 'id'.$catModel['nombreModelo'], $rcgrid['textField'], $rcgrid['tipoControl']);
						}else{
							$arrayNames[] = array($rcgrid['textoID'], $rcgrid['nombreCampo'], $rcgrid['nombreCampo'], 'Na', 'text');
						}						
					}elseif($rcgrid['tipoControl'] == 'consulta'){
						$arrayNames[] = array($rcgrid['textoID'], $rcgrid['nombreCampo'], $rcgrid['valorDefault'], $rcgrid['textField'], $rcgrid['tipoControl'], $rcgrid['queryValor'], $rcgrid['controlQuery']);
					}else{
						$arrayNames[] = array($rcgrid['textoID'], $rcgrid['nombreCampo'], $rcgrid['nombreCampo'], 'Na', $rcgrid['tipoControl']);
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
				
				$dataProvider = $searchModel->search(Yii::$app->session['ReglaspasswSearch']);
				$search = $dataProvider->query->all();
				$cdata = 4;
				
				foreach($search as $row){
					$alFbt="B";
					foreach($arrayNames as $rowName){
						//echo $rowName[4]."<br>";
						if(isset($rowName[4])){
							if($rowName[4] == 'select'){
								//echo $row[$rowName[2]][$rowName[3]]."<br>";
								if(isset($row[$rowName[2]][$rowName[3]])){
									$objPHPExcel->getActiveSheet()->SetCellValue($alFbt.$cdata, $row[$rowName[2]][$rowName[3]]);
								}else{
									$objPHPExcel->getActiveSheet()->SetCellValue($alFbt.$cdata, 'N/D');
								}								
							}elseif($rowName[4] == 'consulta'){
								$valores = explode(",", $rowName[5]);
								//echo $row[$valores[0]]."<br>";
								
								if(count($valores) == 1){
									$nQry = str_replace("'?1'", "'".$row[$valores[0]]."'", $rowName[6]);
								}else{
									$inc = 1;
									$nQry = $rowName[6];
									foreach($valores as $dataVal){
										$nQry = str_replace("'?".$inc."'", "'".$row[$dataVal]."'", $nQry);
										$inc++;
									}
								}
								
								$dataQury = Yii::$app->db->createCommand($nQry)->queryOne();
								//print_r($dataQury);
								//echo $rowName[2]."<br>";
								//echo $dataQury[$rowName[2]]."<br>";
								if(isset($dataQury[$rowName[2]])){
									$objPHPExcel->getActiveSheet()->SetCellValue($alFbt.$cdata,  $dataQury[$rowName[2]]);
								}else{
									$objPHPExcel->getActiveSheet()->SetCellValue($alFbt.$cdata,  'N/D');
								}
							}elseif($rowName[4] == 'checkbox'){
								$valChk = 'No';
								if($row[$rowName[2]] == 1){
									$valChk = 'Si';
								}
								$objPHPExcel->getActiveSheet()->SetCellValue($alFbt.$cdata,  $valChk);
							}else{
								//echo $row[$rowName[2]]."<br>";
								$objPHPExcel->getActiveSheet()->SetCellValue($alFbt.$cdata,  $row[$rowName[2]]);
							}
						}else{
							//echo $row[$rowName[2]]."<br>";
							$objPHPExcel->getActiveSheet()->SetCellValue($alFbt.$cdata,  $row[$rowName[2]]);
						}
						
						$alFbt++;
					}
					$cdata++;
				}


				$objPHPExcel->getActiveSheet()->setTitle('Reporte_'.date('Ymdhi'));
				$objPHPExcel->setActiveSheetIndex(0);




				Yii::$app->response->headers->add('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="Reglaspassw_'.date('ymdHis').'.xlsx"');
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
     * Lists all Reglaspassw models.
     * @return mixed
     */
    public function actionIndex($f)
    {
        $searchModel = new ReglaspasswSearch();
      

		if(isset($_GET['clear'])){
			Yii::$app->session['ReglaspasswSearch'] = '';
			return $this->redirect(['index&f='.$f]);				
		}else{
			$params = Yii::$app->request->queryParams;

			if(count($params) <= 1){		  
				if(isset(Yii::$app->session['ReglaspasswSearch'])) {
					$params = Yii::$app->session['ReglaspasswSearch'];
				}else{
					Yii::$app->session['ReglaspasswSearch'] = $params;
				}
			}else{	
				if(isset(Yii::$app->request->queryParams['ReglaspasswSearch'])){
					Yii::$app->session['ReglaspasswSearch'] = $params;
				}else{
					$params = Yii::$app->session['ReglaspasswSearch'];
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
				
				
				
	 public function actionEliminados($f)
    {
        $searchModel = new ReglaspasswSearch();
      

		if(isset($_GET['clear'])){
			Yii::$app->session['ReglaspasswSearch'] = '';
			return $this->redirect(['eliminados&f='.$f]);				
		}else{
			$params = Yii::$app->request->queryParams;

			if(count($params) <= 1){		  
				if(isset(Yii::$app->session['ReglaspasswSearch'])) {
					$params = Yii::$app->session['ReglaspasswSearch'];
				}else{
					Yii::$app->session['ReglaspasswSearch'] = $params;
				}
			}else{	
				if(isset(Yii::$app->request->queryParams['ReglaspasswSearch'])){
					Yii::$app->session['ReglaspasswSearch'] = $params;
				}else{
					$params = Yii::$app->session['ReglaspasswSearch'];
				}		
			}
		}
		
		$dataProvider = $searchModel->searchelimina($params);
		$dataProvider->pagination->pageSize = Yii::$app->params['npag'];

        return $this->render('eliminados', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Reglaspassw model.
     * @param integer $minimioLongitudPassw
     * @param integer $maximoIntentosFallidos
     * @param integer $tiempoCaducidadCodigoRecuperacionPassw
     * @param integer $tiempoCaducidadInactivadadPassw
     * @param integer $contieneMayuscula
     * @param integer $contieneCaracteresEspeciales
     * @param integer $contieneNumeros
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
        $model = new Reglaspassw();
				
		if($model->load(Yii::$app->request->post())) {
			$post = Yii::$app->request->post();
			$model->regEstado = 1;
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = 1;
			
			if($model->save()){
				
				$rauditoria = Yii::$app->globals->setRegistro(3, 'Alta', 'ReglasPassw', '1');
				$registros = Yii::$app->db->createCommand('SELECT nombreCampo, campoPK FROM Campos WHERE catalogoID="32"')->queryAll();
				foreach($registros as $row){
					Yii::$app->globals->setRmodifica($rauditoria, $row['nombreCampo'], $model[$row['nombreCampo']]);					
				}
				
				echo 'true';
			}else{
				echo 'false';
			}
		}else{
			echo 'false';
		}
    }

    /**
     * Creates a new Reglaspassw model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($f)
    {
        $model = new Reglaspassw();
				
		if($model->load(Yii::$app->request->post())) {
			$fID = Yii::$app->globals->getFormulario($f);
				
			$post = Yii::$app->request->post();
			$model->regEstado = 1;
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = $fID;
			
			if($model->save()){
				
				$rauditoria = Yii::$app->globals->setRegistro(3, 'Alta', 'ReglasPassw', $fID);				
				$registros = Yii::$app->db->createCommand('SELECT nombreCampo, campoPK FROM Campos WHERE catalogoID="32"')->queryAll();
				foreach($registros as $row){
					Yii::$app->globals->setRmodifica($rauditoria, $row['nombreCampo'], $model[$row['nombreCampo']]);					
				}
				return $this->redirect(['create', 'f'=>$f, 'insert' => 'true', 'id'=>$model->minimioLongitudPassw]);
			}
		}
		

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Reglaspassw model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $minimioLongitudPassw
     * @param integer $maximoIntentosFallidos
     * @param integer $tiempoCaducidadCodigoRecuperacionPassw
     * @param integer $tiempoCaducidadInactivadadPassw
     * @param integer $contieneMayuscula
     * @param integer $contieneCaracteresEspeciales
     * @param integer $contieneNumeros
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($f, $minimioLongitudPassw, $maximoIntentosFallidos, $tiempoCaducidadCodigoRecuperacionPassw, $tiempoCaducidadInactivadadPassw, $contieneMayuscula, $contieneCaracteresEspeciales, $contieneNumeros)
    {
        $model = $this->findModel($minimioLongitudPassw, $maximoIntentosFallidos, $tiempoCaducidadCodigoRecuperacionPassw, $tiempoCaducidadInactivadadPassw, $contieneMayuscula, $contieneCaracteresEspeciales, $contieneNumeros);
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
				
				$rauditoria = Yii::$app->globals->setRegistro(5, 'Editar', 'ReglasPassw', $fID);				
				$registros = Yii::$app->db->createCommand('SELECT nombreCampo, campoPK FROM Campos WHERE catalogoID="32"')->queryAll();
				foreach($registros as $row){
					Yii::$app->globals->setRmodifica($rauditoria, $row['nombreCampo'], $model[$row['nombreCampo']]);					
				}
				return $this->redirect(['update', 'f'=>$f, 'minimioLongitudPassw' => $model->minimioLongitudPassw, 'maximoIntentosFallidos' => $model->maximoIntentosFallidos, 'tiempoCaducidadCodigoRecuperacionPassw' => $model->tiempoCaducidadCodigoRecuperacionPassw, 'tiempoCaducidadInactivadadPassw' => $model->tiempoCaducidadInactivadadPassw, 'contieneMayuscula' => $model->contieneMayuscula, 'contieneCaracteresEspeciales' => $model->contieneCaracteresEspeciales, 'contieneNumeros' => $model->contieneNumeros, 'update'=>'true']);
			}
		}

        return $this->render('update', [
            'model' => $model,
        ]);
    }
				
	 public function actionDeletedata($f, $minimioLongitudPassw, $maximoIntentosFallidos, $tiempoCaducidadCodigoRecuperacionPassw, $tiempoCaducidadInactivadadPassw, $contieneMayuscula, $contieneCaracteresEspeciales, $contieneNumeros)
    {
		$fID = Yii::$app->globals->getFormulario($f);
        $model = $this->findModel($minimioLongitudPassw, $maximoIntentosFallidos, $tiempoCaducidadCodigoRecuperacionPassw, $tiempoCaducidadInactivadadPassw, $contieneMayuscula, $contieneCaracteresEspeciales, $contieneNumeros);
		$version = $model->versionRegistro;	
				
		$model->regEstado = 0;	
		$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
		$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
		$model->versionRegistro = $version + 1;
		$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
		$model->regFormularioUltimaModificacion = $fID;
		if($model->save()){
			
			$rauditoria = Yii::$app->globals->setRegistro(4, 'Eliminar', 'ReglasPassw', $fID);				
			$registros = Yii::$app->db->createCommand('SELECT nombreCampo, campoPK FROM Campos WHERE catalogoID="32" and campoPK="1"')->queryAll();
			foreach($registros as $row){
				Yii::$app->globals->setRmodifica($rauditoria, $row['nombreCampo'], $model[$row['nombreCampo']]);
			}
			return $this->redirect(['index',  'f'=>$f, 'delete'=>'true']);
		}
		

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Reglaspassw model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $minimioLongitudPassw
     * @param integer $maximoIntentosFallidos
     * @param integer $tiempoCaducidadCodigoRecuperacionPassw
     * @param integer $tiempoCaducidadInactivadadPassw
     * @param integer $contieneMayuscula
     * @param integer $contieneCaracteresEspeciales
     * @param integer $contieneNumeros
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
   
				
	public function actionDelete(){				
		$true = 0;	
		if ($selection=(array)Yii::$app->request->get('selection')) {
			$fID = Yii::$app->globals->getFormulario($_GET['f']);
				
			$arrayReg = array();
			$registros = Yii::$app->db->createCommand('SELECT nombreCampo, campoPK FROM Campos WHERE catalogoID="32" and campoPK="1"')->queryAll();
				
			foreach($registros as $row){
				$arrayReg[] = $row['nombreCampo'];
			}
				
			foreach($selection as $id){
				$model = $this->findModel($id);		
				$version = $model->versionRegistro;	
				
				$model->load(Yii::$app->request->get());	        
				$model->regEstado= '0';	
				$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
				$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
				$model->versionRegistro = $version+1;
				$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
				$model->regFormularioUltimaModificacion = $fID;
				
				 if($model->save()){					
					$rauditoria = Yii::$app->globals->setRegistro(4, 'Eliminar', 'ReglasPassw', $fID);
									
					foreach($arrayReg as $rowReg){
						Yii::$app->globals->setRmodifica($rauditoria, $rowReg, $model[$rowReg]);
					}
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

    /**
     * Finds the Reglaspassw model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $minimioLongitudPassw
     * @param integer $maximoIntentosFallidos
     * @param integer $tiempoCaducidadCodigoRecuperacionPassw
     * @param integer $tiempoCaducidadInactivadadPassw
     * @param integer $contieneMayuscula
     * @param integer $contieneCaracteresEspeciales
     * @param integer $contieneNumeros
     * @return Reglaspassw the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($minimioLongitudPassw, $maximoIntentosFallidos, $tiempoCaducidadCodigoRecuperacionPassw, $tiempoCaducidadInactivadadPassw, $contieneMayuscula, $contieneCaracteresEspeciales, $contieneNumeros)
    {
        if (($model = Reglaspassw::findOne(['minimioLongitudPassw' => $minimioLongitudPassw, 'maximoIntentosFallidos' => $maximoIntentosFallidos, 'tiempoCaducidadCodigoRecuperacionPassw' => $tiempoCaducidadCodigoRecuperacionPassw, 'tiempoCaducidadInactivadadPassw' => $tiempoCaducidadInactivadadPassw, 'contieneMayuscula' => $contieneMayuscula, 'contieneCaracteresEspeciales' => $contieneCaracteresEspeciales, 'contieneNumeros' => $contieneNumeros])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
