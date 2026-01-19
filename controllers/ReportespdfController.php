<?php
namespace app\controllers;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\filters\AccessControl;
use app\models\Reportespdf;
use app\models\ReportespdfSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use sam0786\fpdf\FPDFBitacora; 
use sam0786\phpexcel\PHPExcel; 

use Dompdf\Dompdf;
use Dompdf\Options;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

/**
 * ReportespdfController implements the CRUD actions for Reportespdf model.
 */
class ReportespdfController extends Controller
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
					 [
						'actions' => ['pdf'],
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
			 $combo = Yii::$app->db->createCommand("SELECT * FROM CombosAnidados where comboAnidadoID='".$_GET['idCombo']."' and regEstado=1 and activoCombo=1")->queryOne();
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

		$searchModel = new ReportespdfSearch();
		
		if(isset(Yii::$app->session['ReportespdfSearch'])) {

			$fID = Yii::$app->globals->getFormulario($f);
			$rauditoria = Yii::$app->globals->setRegistro(7, 'PDF', 'ReportesPdf', $fID);
			
			$catalogo = Yii::$app->db->createCommand("SELECT catalogoID FROM Catalogos where nombreModelo='Reportespdf' and regEstado=1 and activoCatalogo=1")->queryOne();
			if(isset($catalogo['catalogoID'])){
				$cabecera = Yii::$app->db->createCommand("SELECT cabeceraExportar FROM ExportarCatalogos where catalogoID='".$catalogo['catalogoID']."' and regEstado=1")->queryOne();
				
				if(isset($cabecera['cabeceraExportar'])){ $dCabecera = $cabecera['cabeceraExportar']; }else{ $dCabecera = 'Reporte Reportespdf'; }
				$camposGrid = Yii::$app->db->createCommand("SELECT * FROM CamposGrid where regEstado=1 and catalogoID='".$catalogo['catalogoID']."' and visible='1' order by orden")->queryAll();
				
				$arrayNames = array();
				foreach($camposGrid as $rcgrid){
					if($rcgrid['tipoControl'] == 'select'){
						$catModel = Yii::$app->db->createCommand("SELECT * FROM Catalogos where catalogoID='".$rcgrid['catalogoReferenciaID']."' and regEstado=1 and activoCatalogo=1")->queryOne();
						
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
				
				$dataProvider = $searchModel->search(Yii::$app->session['ReportespdfSearch']);
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

		$searchModel = new ReportespdfSearch();
		
		if(isset(Yii::$app->session['ReportespdfSearch'])) {

			$fID = Yii::$app->globals->getFormulario($f);
			$rauditoria = Yii::$app->globals->setRegistro(6, 'Excel', 'ReportesPdf', $fID);
			
			$catalogo = Yii::$app->db->createCommand("SELECT catalogoID FROM Catalogos where nombreModelo='Reportespdf' and regEstado=1 and activoCatalogo=1")->queryOne();
			if(isset($catalogo['catalogoID'])){
				$cabecera = Yii::$app->db->createCommand("SELECT cabeceraExportar FROM ExportarCatalogos where catalogoID='".$catalogo['catalogoID']."' and regEstado=1")->queryOne();
				
				if(isset($cabecera['cabeceraExportar'])){ $dCabecera = $cabecera['cabeceraExportar']; }else{ $dCabecera = 'Reporte Reportespdf'; }
				$camposGrid = Yii::$app->db->createCommand("SELECT * FROM CamposGrid where  regEstado=1 and catalogoID='".$catalogo['catalogoID']."' and visible='1' order by orden")->queryAll();
				
				$arrayNames = array();
				foreach($camposGrid as $rcgrid){
					if($rcgrid['tipoControl'] == 'select'){
						$catModel = Yii::$app->db->createCommand("SELECT * FROM Catalogos where catalogoID='".$rcgrid['catalogoReferenciaID']."' and regEstado=1 and activoCatalogo=1")->queryOne();
						
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
				
				$dataProvider = $searchModel->search(Yii::$app->session['ReportespdfSearch']);
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
				header('Content-Disposition: attachment;filename="Reportespdf_'.date('ymdHis').'.xlsx"');
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
     * Lists all Reportespdf models.
     * @return mixed
     */
    public function actionIndex($f)
    {
        $searchModel = new ReportespdfSearch();
      

		if(isset($_GET['clear'])){
			Yii::$app->session['ReportespdfSearch'] = '';
			return $this->redirect(['index&f='.$f]);				
		}else{
			$params = Yii::$app->request->queryParams;

			if(count($params) <= 1){		  
				if(isset(Yii::$app->session['ReportespdfSearch'])) {
					$params = Yii::$app->session['ReportespdfSearch'];
				}else{
					Yii::$app->session['ReportespdfSearch'] = $params;
				}
			}else{	
				if(isset(Yii::$app->request->queryParams['ReportespdfSearch'])){
					Yii::$app->session['ReportespdfSearch'] = $params;
				}else{
					$params = Yii::$app->session['ReportespdfSearch'];
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
        $searchModel = new ReportespdfSearch();
      

		if(isset($_GET['clear'])){
			Yii::$app->session['ReportespdfSearch'] = '';
			return $this->redirect(['eliminados&f='.$f]);				
		}else{
			$params = Yii::$app->request->queryParams;

			if(count($params) <= 1){		  
				if(isset(Yii::$app->session['ReportespdfSearch'])) {
					$params = Yii::$app->session['ReportespdfSearch'];
				}else{
					Yii::$app->session['ReportespdfSearch'] = $params;
				}
			}else{	
				if(isset(Yii::$app->request->queryParams['ReportespdfSearch'])){
					Yii::$app->session['ReportespdfSearch'] = $params;
				}else{
					$params = Yii::$app->session['ReportespdfSearch'];
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
     * Displays a single Reportespdf model.
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
        $model = new Reportespdf();
				
		if($model->load(Yii::$app->request->post())) {
			$post = Yii::$app->request->post();
			$model->regEstado = 1;
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = 1;
			
			if($model->save()){
				
				$rauditoria = Yii::$app->globals->setRegistro(3, 'Alta', 'ReportesPdf', '1');
				$registros = Yii::$app->db->createCommand('SELECT nombreCampo, campoPK FROM Campos WHERE catalogoID="100049"')->queryAll();
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
     * Creates a new Reportespdf model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($f)
    {
        $model = new Reportespdf();
				
		if($model->load(Yii::$app->request->post())) {
			$fID = Yii::$app->globals->getFormulario($f);
				
			$post = Yii::$app->request->post();
			$model->regEstado = 1;
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = $fID;
			$model->arrayCampos = Html::encode($post['Reportespdf']['arrayCampos']);
			
			if($model->save()){
				
				$rauditoria = Yii::$app->globals->setRegistro(3, 'Alta', 'ReportesPdf', $fID);				
				$registros = Yii::$app->db->createCommand('SELECT nombreCampo, campoPK FROM Campos WHERE catalogoID="100049"')->queryAll();
				foreach($registros as $row){
					Yii::$app->globals->setRmodifica($rauditoria, $row['nombreCampo'], $model[$row['nombreCampo']]);					
				}
				return $this->redirect(['create', 'f'=>$f, 'insert' => 'true', 'id'=>$model->reportesPdfID]);
			}
		}
		

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Reportespdf model.
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
			$model->arrayCampos = Html::encode($post['Reportespdf']['arrayCampos']);
			
			if($model->save()){
				
				$rauditoria = Yii::$app->globals->setRegistro(5, 'Editar', 'ReportesPdf', $fID);				
				$registros = Yii::$app->db->createCommand('SELECT nombreCampo, campoPK FROM Campos WHERE catalogoID="100049"')->queryAll();
				foreach($registros as $row){
					Yii::$app->globals->setRmodifica($rauditoria, $row['nombreCampo'], $model[$row['nombreCampo']]);					
				}
				return $this->redirect(['update', 'f'=>$f, 'id' => $model->reportesPdfID, 'update'=>'true']);
			}
		}

        return $this->render('update', [
            'model' => $model,
        ]);
    }
	
	public function actionUploadfile()
    {
		$allowedExts = array("gif", "jpeg", "jpg", "png");
		$temp = explode(".", $_FILES["file"]["name"]);
		$extension = end($temp);	
		
		if (in_array($extension, $allowedExts)) {
			// Generate new random name.
			$name = sha1(microtime()) . "." . $extension;
			// Save file in the uploads folder.
			move_uploaded_file($_FILES["file"]["tmp_name"], getcwd() . "/pdfreport/" . $name);

			// Generate response.
			$response = new \StdClass;
			//$response->link = "".Yii::$app->request->baseUrl."/pdfreport/".$name;
			
			$response->link = Url::to('@web/pdfreport/'.$name, true);
			echo stripslashes(json_encode($response));
		}
	}
	
	public function actionPdf($f, $id)
    {
        $model = $this->findModel($id);
		$model->codigoReporte = Html::decode($model->codigoReporte);
		$version = $model->versionRegistro;		
		
		if($model->load(Yii::$app->request->post())) {
			$fID = Yii::$app->globals->getFormulario($f);
				
			$post = Yii::$app->request->post();
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = $version + 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = $fID;
			$model->codigoReporte = Html::encode($post['Reportespdf']['codigoReporte']);
			
			if($model->save()){				
				$rauditoria = Yii::$app->globals->setRegistro(5, 'Editar', 'ReportesPdf', $fID);				
				$registros = Yii::$app->db->createCommand('SELECT nombreCampo, campoPK FROM Campos WHERE catalogoID="100049"')->queryAll();
				foreach($registros as $row){
					Yii::$app->globals->setRmodifica($rauditoria, $row['nombreCampo'], $model[$row['nombreCampo']]);					
				}
				return $this->redirect(['pdf', 'f'=>$f, 'id' => $model->reportesPdfID, 'update'=>'true']);
			}
		}

        return $this->render('pdf', [
            'model' => $model,
        ]);
    }
	
	
	public function actionHeader($f, $id)
    {
        $model = $this->findModel($id);
		$model->headerReporte = Html::decode($model->headerReporte);
		$version = $model->versionRegistro;		
		
		if($model->load(Yii::$app->request->post())) {
			$fID = Yii::$app->globals->getFormulario($f);
				
			$post = Yii::$app->request->post();
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = $version + 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = $fID;
			$model->headerReporte = Html::encode($post['Reportespdf']['headerReporte']);
			
			if($model->save()){				
				$rauditoria = Yii::$app->globals->setRegistro(5, 'Editar', 'ReportesPdf', $fID);				
				$registros = Yii::$app->db->createCommand('SELECT nombreCampo, campoPK FROM Campos WHERE catalogoID="100049"')->queryAll();
				foreach($registros as $row){
					Yii::$app->globals->setRmodifica($rauditoria, $row['nombreCampo'], $model[$row['nombreCampo']]);					
				}
				return $this->redirect(['header', 'f'=>$f, 'id' => $model->reportesPdfID, 'update'=>'true']);
			}
		}

        return $this->render('header', [
            'model' => $model,
        ]);
    }
	
	
	public function actionFooter($f, $id)
    {
        $model = $this->findModel($id);
		$model->footerReporte = Html::decode($model->footerReporte);
		$version = $model->versionRegistro;		
		
		if($model->load(Yii::$app->request->post())) {
			$fID = Yii::$app->globals->getFormulario($f);
				
			$post = Yii::$app->request->post();
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = $version + 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = $fID;
			$model->footerReporte = Html::encode($post['Reportespdf']['footerReporte']);
			
			if($model->save()){				
				$rauditoria = Yii::$app->globals->setRegistro(5, 'Editar', 'ReportesPdf', $fID);				
				$registros = Yii::$app->db->createCommand('SELECT nombreCampo, campoPK FROM Campos WHERE catalogoID="100049"')->queryAll();
				foreach($registros as $row){
					Yii::$app->globals->setRmodifica($rauditoria, $row['nombreCampo'], $model[$row['nombreCampo']]);					
				}
				return $this->redirect(['footer', 'f'=>$f, 'id' => $model->reportesPdfID, 'update'=>'true']);
			}
		}

        return $this->render('footer', [
            'model' => $model,
        ]);
    }
	
	
	 public function actionSendfile(){
		 if(isset($_POST['folio']) and isset($_POST['json']) and isset($_POST['nombreArchivo']) and isset($_POST['carpeta'])){
			 	$folio = $_POST['folio'];
			 	$json = $_POST['json'];
			 	$nombreArchivo = $_POST['nombreArchivo'];
			 	$carpeta = $_POST['carpeta'];
			 
			 	$reporte = Yii::$app->db->createCommand('SELECT * FROM ReportesPdf WHERE folioReporte="'.$folio.'"')->queryOne();
			 
				if(isset($reporte['codigoReporte'])){
					
				$reporteCodigo = '<!doctype html>
										<html>
										<head>
										<meta charset="utf-8">
										<title>Reporte</title>
										<style>
											@page {
												margin: 100px 25px;
											}

											header {
												position: fixed;
												top: -60px;
												height: '.$reporte['altoHeader'].'px;
												text-align: center;
												line-height: 12px;
												width: 100%;
											}

											footer {
												position: fixed;
												bottom: -60px;
												height:  '.$reporte['altoFooter'].'px;
												text-align: center;
												line-height: 12px;
												width: 100%;
											}
											
											
											}
									  </style>
									</head>
									<body>';
										
					$new_text = $reporte['codigoReporte'];
					$new_header = $reporte['headerReporte'];
					$new_footer = $reporte['footerReporte'];
					$jsonEncode = json_decode($json, true);
					//echo $jsonEncode[0]['Año'];

					//$https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?id=5803EB8D-81CD-4557-8719-26632D2FA434&re=VISM990101474&rr=XAXX010101000&tt=0000014300.000000&fe=rH8/bw==
					$idQr = "";
					$reQr = "";
					$rrQr = "";
					$ttQr = "";
					$feQr = "";

					$urlQr = "Error_data";

					foreach($jsonEncode[0] as $index => $value){
						$regex = '|{{'.trim($index).'}}|'; 
						if(trim($index) == 'Año'){
							$regex = '|{{A&amp;ntilde;o}}|'; 
						}
						if($index == 'cadenaOriginal' or $index == 'selloCFD' or $index == 'selloSAT'){
							$value = wordwrap($value, 100, "<br/>",TRUE);					
						}	

						if($index == 'UUID'){ $idQr = $value; }
						if($index == 'rfc'){ $reQr = $value; }
						if($index == 'rfcReceptor'){ $rrQr = $value; }
						if($index == 'total'){ $ttQr = $value; }
						if($index == 'selloCFD'){ $feQr = substr($value,-8); }


						$new_text = preg_replace($regex, $value, $new_text);
						$new_header = preg_replace($regex, $value, $new_header);
						$new_footer = preg_replace($regex, $value, $new_footer);
					}


					$encFile =  Yii::$app->basePath.'/web/phpqrcode/qrlib.php';
					require_once($encFile);
					$urlQr = "https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?id=".$idQr."&re=".$reQr."&rr=".$rrQr."&tt=".$ttQr."&fe=".$feQr;
					$codesDir = Yii::$app->basePath."/web/qrcodes/"; 
					$codeFile = date('d-m-Y-h-i-s').'.png';
					\QRcode::png($urlQr, $codesDir.$codeFile, 'M', 3); 

					$img_qr =  Url::to('@web/qrcodes/'.$codeFile, true);
					$link_qr = '<img src="'.$img_qr.'"  class="fr-fic fr-dib">';

					$regex = '|{{QR}}|'; 
					$new_text = preg_replace($regex, $link_qr, $new_text);

					$reporteCodigo .= Html::decode('<header>'.$new_header.'</header>');
					$reporteCodigo .= Html::decode($new_text);
					$reporteCodigo .= Html::decode('<footer>'.$new_footer.'</footer>');
					
					$reporteCodigo .= '</body></html>';

					$options = new Options();
					$options->set('isRemoteEnabled', true);

					$dompdf = new Dompdf($options);
					//$dompdf->setBasePath('http://10.128.5.242/');
					 $dompdf->setHttpContext(
								  stream_context_create([
									  'ssl' => [
										  'allow_self_signed'=> TRUE,
										  'verify_peer' => FALSE,
										  'verify_peer_name' => FALSE,
									  ]
								  ])
								);
					$dompdf->loadHtml($reporteCodigo);
					$dompdf->setPaper('letter', 'portrait');

					$dompdf->render();
					$canvas = $dompdf->get_canvas();
					//$font = $dompdf->get_font("Arial");
					if($reporte['paginacionReporte'] == 1){
						$canvas->page_text(500, 770, "Pag. {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0,0,0));
					}
										
					// Output the generated PDF to Browser
					//$dompdf->output();
					//$dompdf->stream();
					$output = $dompdf->output();
					$rutaGuardado = Yii::$app->basePath."/web/pdfdownload/";
					//$nombreArchivo = date('ymdhis').".pdf";
					file_put_contents( $rutaGuardado.$nombreArchivo.".pdf", $output);

					//echo Url::to('@web/pdfdownload/'.$nombreArchivo, true);

					if (file_exists($codesDir.$codeFile)) {
						unlink($codesDir.$codeFile);
					}
					
					//$sendFile = 1;
					$sendFile = $this->Sendfl($nombreArchivo, $carpeta);
					if($sendFile == 'ok'){
						$array = array('envio'=>true, 'Mensaje'=>'Archivo creado y enviado con exito');
					}else{
						$array = array('envio'=>false, 'Mensaje'=>'No se completo el envio del archivo');
					}
					 
				}else{
					 $array = array('envio'=>false, 'Mensaje'=>'El folio del reporte no se encuentra');
				}
			
		 }else{
			$array = array('envio'=>false, 'Mensaje'=>'Falta campo folio');
		 }
		 
		 return json_encode( $array);
		 exit;
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
			
			$rauditoria = Yii::$app->globals->setRegistro(4, 'Eliminar', 'ReportesPdf', $fID);				
			$registros = Yii::$app->db->createCommand('SELECT nombreCampo, campoPK FROM Campos WHERE catalogoID="100049" and campoPK="1"')->queryAll();
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
     * Deletes an existing Reportespdf model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
   
				
	public function actionDelete(){				
		$true = 0;	
		if ($selection=(array)Yii::$app->request->get('selection')) {
			$fID = Yii::$app->globals->getFormulario($_GET['f']);
				
			$arrayReg = array();
			$registros = Yii::$app->db->createCommand('SELECT nombreCampo, campoPK FROM Campos WHERE catalogoID="100049" and campoPK="1"')->queryAll();
				
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
					$rauditoria = Yii::$app->globals->setRegistro(4, 'Eliminar', 'ReportesPdf', $fID);
									
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
     * Finds the Reportespdf model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Reportespdf the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Reportespdf::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
