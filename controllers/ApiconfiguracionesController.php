<?php


namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\models\Apiconfiguraciones;
use app\models\ApiconfiguracionesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use sam0786\fpdf\FPDFBitacora; 
use sam0786\phpexcel\PHPExcel; 

/**
 * ApiconfiguracionesController implements the CRUD actions for Apiconfiguraciones model.
 */
class ApiconfiguracionesController extends Controller
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
                'only' => ['create', 'update', 'delete', 'index', 'deletedata', 'createform', 'getselect', 'xportpdf', 'xportexcel', 'getcombo'],
                'rules' => [
                    [
						 'actions' => [$perAlta, $perEdit, 'delete', $perCons, $perElim , 'createform', 'getselect', $perPdf, $perExcel, 'getcombo', 'getdatacombo'],
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
		
		$searchModel = new ApiconfiguracionesSearch();
		
		if(isset(Yii::$app->session['ApiconfiguracionesSearch'])) {

			$fID = Yii::$app->globals->getFormulario($f);
			$rauditoria = Yii::$app->globals->setRegistro(7, 'PDF', '', $fID);
			
			$catalogo = Yii::$app->db->createCommand("SELECT catalogoID FROM Catalogos where nombreModelo='Apiconfiguraciones'")->queryOne();
			if(isset($catalogo['catalogoID'])){
				$cabecera = Yii::$app->db->createCommand("SELECT cabeceraExportar FROM ExportarCatalogos where catalogoID='".$catalogo['catalogoID']."'")->queryOne();
				
				if(isset($cabecera['cabeceraExportar'])){ $dCabecera = $cabecera['cabeceraExportar']; }else{ $dCabecera = 'Reporte Apiconfiguraciones'; }
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
				
				$dataProvider = $searchModel->search(Yii::$app->session['ApiconfiguracionesSearch']);
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
		$searchModel = new ApiconfiguracionesSearch();
		
		if(isset(Yii::$app->session['ApiconfiguracionesSearch'])) {

			$fID = Yii::$app->globals->getFormulario($f);
			$rauditoria = Yii::$app->globals->setRegistro(6, 'Excel', '', $fID);
			
			$catalogo = Yii::$app->db->createCommand("SELECT catalogoID FROM Catalogos where nombreModelo='Apiconfiguraciones'")->queryOne();
			if(isset($catalogo['catalogoID'])){
				$cabecera = Yii::$app->db->createCommand("SELECT cabeceraExportar FROM ExportarCatalogos where catalogoID='".$catalogo['catalogoID']."'")->queryOne();
				
				if(isset($cabecera['cabeceraExportar'])){ $dCabecera = $cabecera['cabeceraExportar']; }else{ $dCabecera = 'Reporte Apiconfiguraciones'; }
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
				
				$dataProvider = $searchModel->search(Yii::$app->session['ApiconfiguracionesSearch']);
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


				$objPHPExcel->getActiveSheet()->setTitle('Apiconfiguraciones_'.date('Ymdhis'));
				$objPHPExcel->setActiveSheetIndex(0);




				Yii::$app->response->headers->add('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="Apiconfiguraciones_'.date('ymdHis').'.xlsx"');
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
     * Lists all Apiconfiguraciones models.
     * @return mixed
     */
    public function actionIndex($f)
    {
        $searchModel = new ApiconfiguracionesSearch();
      

		if(isset($_GET['clear'])){
			Yii::$app->session['ApiconfiguracionesSearch'] = '';
			return $this->redirect(['index&f='.$f]);				
		}else{
			$params = Yii::$app->request->queryParams;

			if(count($params) <= 1){		  
				if(isset(Yii::$app->session['ApiconfiguracionesSearch'])) {
					$params = Yii::$app->session['ApiconfiguracionesSearch'];
				}else{
					Yii::$app->session['ApiconfiguracionesSearch'] = $params;
				}
			}else{	
				if(isset(Yii::$app->request->queryParams['ApiconfiguracionesSearch'])){
					Yii::$app->session['ApiconfiguracionesSearch'] = $params;
				}else{
					$params = Yii::$app->session['ApiconfiguracionesSearch'];
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
     * Displays a single Apiconfiguraciones model.
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
        $model = new Apiconfiguraciones();
				
		if($model->load(Yii::$app->request->post())) {
			$post = Yii::$app->request->post();
			$model->regEstado = 1;
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = 1;
			
			if($model->save()){
				
				$rauditoria = Yii::$app->globals->setRegistro(3, 'Alta', '', '1');
				$registros = Yii::$app->db->createCommand('SELECT nombreCampo, campoPK FROM Campos WHERE catalogoID=""')->queryAll();
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
     * Creates a new Apiconfiguraciones model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($f)
    {
        $model = new Apiconfiguraciones();
				
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
				
				$rauditoria = Yii::$app->globals->setRegistro(3, 'Alta', '', $fID);				
				$registros = Yii::$app->db->createCommand('SELECT nombreCampo, campoPK FROM Campos WHERE catalogoID=""')->queryAll();
				foreach($registros as $row){
					Yii::$app->globals->setRmodifica($rauditoria, $row['nombreCampo'], $model[$row['nombreCampo']]);					
				}
				return $this->redirect(['create', 'f'=>$f, 'insert' => 'true', 'id'=>$model->apiListaConfiguracionID]);
			}
		}
		

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Apiconfiguraciones model.
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
				$registros = Yii::$app->db->createCommand('SELECT nombreCampo, campoPK FROM Campos WHERE catalogoID=""')->queryAll();
				foreach($registros as $row){
					Yii::$app->globals->setRmodifica($rauditoria, $row['nombreCampo'], $model[$row['nombreCampo']]);					
				}
				return $this->redirect(['update', 'f'=>$f, 'id' => $model->apiListaConfiguracionID, 'update'=>'true']);
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
			$registros = Yii::$app->db->createCommand('SELECT nombreCampo, campoPK FROM Campos WHERE catalogoID="" and campoPK="1"')->queryAll();
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
     * Deletes an existing Apiconfiguraciones model.
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
     * Finds the Apiconfiguraciones model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Apiconfiguraciones the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Apiconfiguraciones::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
