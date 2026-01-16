<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\models\Slider;
use app\models\SliderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;



/**
 * SliderController implements the CRUD actions for Slider model.
 */
class SliderController extends Controller
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
		
		$frmSeguridad = 'slider';
		
		$permisosBtn = Yii::$app->db->createCommand("SELECT Acciones.accionID, Acciones.nombreAccion, Formularios.nombreFormulario, Formularios.tipoFormularioID, Formularios.urlArchivo FROM Acciones
			inner join AccionesFormularios on Acciones.accionID=AccionesFormularios.accionID 
			inner join Formularios on AccionesFormularios.formularioID=Formularios.formularioID
			inner join PerfilAccionFormulario on AccionesFormularios.accionFormularioID =PerfilAccionFormulario.accionFormularioID
			inner join PerfilesCompuestos on PerfilAccionFormulario.perfilID=PerfilesCompuestos.perfilID
			inner join Usuarios on Usuarios.usuarioID = PerfilesCompuestos.usuarioID
			where Formularios.urlArchivo='slider/index' and AccionesFormularios.estadoAccion=1 and AccionesFormularios.regEstado=1 and PerfilAccionFormulario.regEstado=1 and PerfilAccionFormulario.activoPerfilAccionFormulario=1 and Usuarios.usuarioID='".$usuarioIdToken."' group by AccionesFormularios.accionFormularioID")->queryAll();
			
			foreach($permisosBtn as $dataPbtn){
				$urlSeguridad = explode("/", $dataPbtn['urlArchivo']);
				if(isset($urlSeguridad[0])){
					if($frmSeguridad == $urlSeguridad[0]){
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
		
        return [
			 'access' => [
                'class' => AccessControl::className(),
                'only' => ['create', 'update', 'delete', 'index', 'deletedata', 'createform', 'getselect'],
                'rules' => [
                    [
                        'actions' => [$perAlta, $perEdit, $perdel, $perCons, $perElim, 'createform', 'getselect'],
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

    /**
     * Lists all Slider models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SliderSearch();
      

		if(isset($_GET['clear'])){
			Yii::$app->session['SliderSearch'] = '';
			return $this->redirect(['index']);			
		}else{
			$params = Yii::$app->request->queryParams;

			if(count($params) <= 1){		  
				if(isset(Yii::$app->session['SliderSearch'])) {
					$params = Yii::$app->session['SliderSearch'];
				}else{
					Yii::$app->session['SliderSearch'] = $params;
				}
			}else{	
				if(isset(Yii::$app->request->queryParams['SliderSearch'])){
					Yii::$app->session['SliderSearch'] = $params;
				}else{
					$params = Yii::$app->session['SliderSearch'];
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
     * Displays a single Slider model.
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
        $model = new Slider();
				
		if($model->load(Yii::$app->request->post())) {
			$post = Yii::$app->request->post();
			$model->regEstado = 1;
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = 1;
			
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
     * Creates a new Slider model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Slider();	
	
		if($model->load(Yii::$app->request->post())) {
			$fID = Yii::$app->globals->getFormularioToken('slider/index');
			$post = Yii::$app->request->post();	
			$model->regEstado = 1;
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = $fID;
			
			if(is_uploaded_file($_FILES['img_data']['tmp_name'])){
				if($model->save()){
						
						$explode = explode(".", $_FILES['img_data']['name']);
						$extencion = end($explode);
					
						$dir =  Yii::$app->basePath.'/slider/';
						$name_file    =  'slide_'.$model->configuracionesSliderID.'_'.date('Ymdhis').'.'.$extencion;
						$archivo = $dir.basename($name_file);
						if(move_uploaded_file($_FILES['img_data']['tmp_name'], $archivo)){
							Yii::$app->db->createCommand("UPDATE ConfiguracionesSlider SET imagenSlider='".$name_file."' WHERE configuracionesSliderID ='".$model->configuracionesSliderID."'")->execute(); 
						}
					
					
					$rauditoria = Yii::$app->globals->setRegistro(3, 'Alta', 'configuracionesslider', $fID);

					Yii::$app->globals->setRmodifica($rauditoria, 'configuracionesSliderID', $model->configuracionesSliderID);
					Yii::$app->globals->setRmodifica($rauditoria, 'tituloSlider', $model->tituloSlider);
					Yii::$app->globals->setRmodifica($rauditoria, 'contenidoSlider', $model->contenidoSlider);
					Yii::$app->globals->setRmodifica($rauditoria, 'imagenSlider', $model->imagenSlider);
					Yii::$app->globals->setRmodifica($rauditoria, 'ordenSlider', $model->ordenSlider);
					Yii::$app->globals->setRmodifica($rauditoria, 'activoConfiguracionesSlider', $model->activoConfiguracionesSlider);
					
					
						return $this->redirect(['create', 'insert' => 'true', 'id'=>$model->configuracionesSliderID]);
				}
			}else{
				if($model->save()){
					
					
					$rauditoria = Yii::$app->globals->setRegistro(3, 'Alta', 'configuracionesslider', $fID);

					Yii::$app->globals->setRmodifica($rauditoria, 'configuracionesSliderID', $model->configuracionesSliderID);
					Yii::$app->globals->setRmodifica($rauditoria, 'tituloSlider', $model->tituloSlider);
					Yii::$app->globals->setRmodifica($rauditoria, 'contenidoSlider', $model->contenidoSlider);
					Yii::$app->globals->setRmodifica($rauditoria, 'imagenSlider', $model->imagenSlider);
					Yii::$app->globals->setRmodifica($rauditoria, 'ordenSlider', $model->ordenSlider);
					Yii::$app->globals->setRmodifica($rauditoria, 'activoConfiguracionesSlider', $model->activoConfiguracionesSlider);
					
					 return $this->redirect(['create', 'insert' => 'true', 'id'=>$model->configuracionesSliderID]);
				}
			}
							
		}
		

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Slider model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
		$version = $model->versionRegistro;		
			
		if($model->load(Yii::$app->request->post())) {
			$fID = Yii::$app->globals->getFormularioToken('slider/index');
			$post = Yii::$app->request->post();
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = $version + 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = $fID;
			
			if(is_uploaded_file($_FILES['img_data']['tmp_name'])){
				if($model->save()){
						$explode = explode(".", $_FILES['img_data']['name']);
						$extencion = end($explode);
					
						$dir =  Yii::$app->basePath.'/slider/';
						$name_file    =  'slide_'.$model->configuracionesSliderID.'_'.date('Ymdhis').'.'.$extencion;
						$archivo = $dir.basename($name_file);
						if(move_uploaded_file($_FILES['img_data']['tmp_name'], $archivo)){
							Yii::$app->db->createCommand("UPDATE ConfiguracionesSlider SET imagenSlider='".$name_file."' WHERE configuracionesSliderID ='".$model->configuracionesSliderID."'")->execute(); 
						}
					
						
					$rauditoria = Yii::$app->globals->setRegistro(5, 'Editar', 'configuracionesslider', $fID);

					Yii::$app->globals->setRmodifica($rauditoria, 'configuracionesSliderID', $model->configuracionesSliderID);
					Yii::$app->globals->setRmodifica($rauditoria, 'tituloSlider', $model->tituloSlider);
					Yii::$app->globals->setRmodifica($rauditoria, 'contenidoSlider', $model->contenidoSlider);
					Yii::$app->globals->setRmodifica($rauditoria, 'imagenSlider', $model->imagenSlider);
					Yii::$app->globals->setRmodifica($rauditoria, 'ordenSlider', $model->ordenSlider);
					Yii::$app->globals->setRmodifica($rauditoria, 'activoConfiguracionesSlider', $model->activoConfiguracionesSlider);
					
						return $this->redirect(['update', 'id'=>$model->configuracionesSliderID, 'update'=>'true']);
				}
			}else{
				if($model->save()){
										
					$rauditoria = Yii::$app->globals->setRegistro(5, 'Editar', 'configuracionesslider', $fID);

					Yii::$app->globals->setRmodifica($rauditoria, 'configuracionesSliderID', $model->configuracionesSliderID);
					Yii::$app->globals->setRmodifica($rauditoria, 'tituloSlider', $model->tituloSlider);
					Yii::$app->globals->setRmodifica($rauditoria, 'contenidoSlider', $model->contenidoSlider);
					Yii::$app->globals->setRmodifica($rauditoria, 'imagenSlider', $model->imagenSlider);
					Yii::$app->globals->setRmodifica($rauditoria, 'ordenSlider', $model->ordenSlider);
					Yii::$app->globals->setRmodifica($rauditoria, 'activoConfiguracionesSlider', $model->activoConfiguracionesSlider);
					
					 return $this->redirect(['update', 'id'=>$model->configuracionesSliderID, 'update'=>'true']);
				}
			}
							
		}

        return $this->render('update', [
            'model' => $model,
        ]);
    }
				
	 public function actionDeletedata($id)
    {
        $model = $this->findModel($id);		
		$model->regEstado = 0;			
		if($model->save()){
			return $this->redirect(['index', 'delete'=>'true']);
		}
		

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Slider model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
   
				
	public function actionDelete(){				
		$true = 0;	
		if ($selection=(array)Yii::$app->request->post('selection')) {
			$fID = Yii::$app->globals->getFormularioToken('slider/index');
			foreach($selection as $id){
				$model = $this->findModel($id);					
				$model->load(Yii::$app->request->post());	        
				$model->regEstado= '0';	
				
				 if($model->save()){
					 
					 
					 $rauditoria = Yii::$app->globals->setRegistro(4, 'Eliminar', 'configuracionesslider', $fID);

					 Yii::$app->globals->setRmodifica($rauditoria, 'configuracionesSliderID', $model->configuracionesSliderID);
					 
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
     * Finds the Slider model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Slider the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Slider::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
