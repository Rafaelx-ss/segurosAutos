<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\models\Stylos;
use app\models\StylosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;



/**
 * StylosController implements the CRUD actions for Stylos model.
 */
class StylosController extends Controller
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
		
		$frmSeguridad = 'stylos';
		
		$permisosBtn = Yii::$app->db->createCommand("SELECT Acciones.accionID, Acciones.nombreAccion, Formularios.nombreFormulario, Formularios.tipoFormularioID, Formularios.urlArchivo FROM Acciones
			inner join AccionesFormularios on Acciones.accionID=AccionesFormularios.accionID 
			inner join Formularios on AccionesFormularios.formularioID=Formularios.formularioID
			inner join PerfilAccionFormulario on AccionesFormularios.accionFormularioID =PerfilAccionFormulario.accionFormularioID
			inner join PerfilesCompuestos on PerfilAccionFormulario.perfilID=PerfilesCompuestos.perfilID
			inner join Usuarios on Usuarios.usuarioID = PerfilesCompuestos.usuarioID
			where Formularios.urlArchivo='stylos/update&id=1' and AccionesFormularios.estadoAccion=1 and AccionesFormularios.regEstado=1 and PerfilAccionFormulario.regEstado=1 and PerfilAccionFormulario.activoPerfilAccionFormulario=1 and Usuarios.usuarioID='".$usuarioIdToken."' group by AccionesFormularios.accionFormularioID")->queryAll();
			
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
                        'actions' => ['', $perEdit, '', '', '', '', ''],
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
     * Lists all Stylos models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StylosSearch();
      

		if(isset($_GET['clear'])){
			Yii::$app->session['StylosSearch'] = '';
			return $this->redirect(['index']);			
		}else{
			$params = Yii::$app->request->queryParams;

			if(count($params) <= 1){		  
				if(isset(Yii::$app->session['StylosSearch'])) {
					$params = Yii::$app->session['StylosSearch'];
				}else{
					Yii::$app->session['StylosSearch'] = $params;
				}
			}else{	
				if(isset(Yii::$app->request->queryParams['StylosSearch'])){
					Yii::$app->session['StylosSearch'] = $params;
				}else{
					$params = Yii::$app->session['StylosSearch'];
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
     * Displays a single Stylos model.
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
        $model = new Stylos();
				
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
     * Creates a new Stylos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Stylos();
				
		if($model->load(Yii::$app->request->post())) {
			$fID = Yii::$app->globals->getFormularioToken('stylos/update');
			$post = Yii::$app->request->post();
			$model->regEstado = 1;
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = $fID;
			
			if($model->save()){
				
				
				$rauditoria = Yii::$app->globals->setRegistro(3, 'Alta', 'ConfiguracionesSistema', $fID);

				Yii::$app->globals->setRmodifica($rauditoria, 'configuracionesSistemaID', $model->configuracionesSistemaID);
				Yii::$app->globals->setRmodifica($rauditoria, 'logoLogin', $model->logoLogin);
				Yii::$app->globals->setRmodifica($rauditoria, 'tiempoSesion', $model->tiempoSesion);
				Yii::$app->globals->setRmodifica($rauditoria, 'logoBanner', $model->logoBanner);
				Yii::$app->globals->setRmodifica($rauditoria, 'iconoMenu', $model->iconoMenu);
				Yii::$app->globals->setRmodifica($rauditoria, 'temaBanner', $model->temaBanner);
				Yii::$app->globals->setRmodifica($rauditoria, 'temaMenu', $model->temaMenu);
				Yii::$app->globals->setRmodifica($rauditoria, 'temaContenido', $model->temaContenido);
				
				return $this->redirect(['create', 'insert' => 'true', 'id'=>$model->configuracionesSistemaID]);
			}
		}
		

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Stylos model.
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
			$fID = Yii::$app->globals->getFormularioToken('stylos/update');
			$post = Yii::$app->request->post();
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = $version + 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = $fID;
			
			$dir =  Yii::$app->basePath.'/web/logos/';
			
			if(is_uploaded_file($_FILES['img_login']['tmp_name'])){
				$explode = explode(".", $_FILES['img_login']['name']);
				$extencion = end($explode);
					
				
				$name_file    =  'login_'.$model->configuracionesSistemaID.'_'.date('Ymdhis').'.'.$extencion;
				$archivo = $dir.basename($name_file);
				if(move_uploaded_file($_FILES['img_login']['tmp_name'], $archivo)){
					$model->logoLogin = $name_file;
				}
			}
			
			if(is_uploaded_file($_FILES['img_banner']['tmp_name'])){
				$explode1 = explode(".", $_FILES['img_banner']['name']);
				$extencion1 = end($explode1);
					
				$name_file1    =  'menu_'.$model->configuracionesSistemaID.'_'.date('Ymdhis').'.'.$extencion1;
				$archivo1 = $dir.basename($name_file1);
				if(move_uploaded_file($_FILES['img_banner']['tmp_name'], $archivo1)){
					$model->logoBanner = $name_file1;
				}
			}
			
			if(is_uploaded_file($_FILES['img_icono']['tmp_name'])){
				$explode2 = explode(".", $_FILES['img_icono']['name']);
				$extencion2 = end($explode2);
					
				$name_file2    =  'icono_'.$model->configuracionesSistemaID.'_'.date('Ymdhis').'.'.$extencion2;
				$archivo2 = $dir.basename($name_file2);
				if(move_uploaded_file($_FILES['img_icono']['tmp_name'], $archivo2)){
					$model->iconoMenu = $name_file2;
				}
			}
			
			if(is_uploaded_file($_FILES['img_footer']['tmp_name'])){
				$explode2 = explode(".", $_FILES['img_footer']['name']);
				$extencion2 = end($explode2);
					
				$name_file2    =  'footer_'.$model->configuracionesSistemaID.'_'.date('Ymdhis').'.'.$extencion2;
				$archivo2 = $dir.basename($name_file2);
				if(move_uploaded_file($_FILES['img_footer']['tmp_name'], $archivo2)){
					$model->logoFooter = $name_file2;
				}
			}
			
			if(is_uploaded_file($_FILES['img_favicon']['tmp_name'])){
				$explode2 = explode(".", $_FILES['img_favicon']['name']);
				$extencion2 = end($explode2);
					
				$name_file2    =  'favicon_'.$model->configuracionesSistemaID.'_'.date('Ymdhis').'.'.$extencion2;
				$archivo2 = $dir.basename($name_file2);
				if(move_uploaded_file($_FILES['img_favicon']['tmp_name'], $archivo2)){
					$model->favIcon = $name_file2;
				}
			}
			
			if($model->save()){
				
				/*
				$rauditoria = Yii::$app->globals->setRegistro(5, 'Editar', 'ConfiguracionesSistema', $fID);

				Yii::$app->globals->setRmodifica($rauditoria, 'configuracionesSistemaID', $model->configuracionesSistemaID);
				Yii::$app->globals->setRmodifica($rauditoria, 'logoLogin', $model->logoLogin);
				Yii::$app->globals->setRmodifica($rauditoria, 'logoBanner', $model->logoBanner);
				Yii::$app->globals->setRmodifica($rauditoria, 'iconoMenu', $model->iconoMenu);
				Yii::$app->globals->setRmodifica($rauditoria, 'temaBanner', $model->temaBanner);
				Yii::$app->globals->setRmodifica($rauditoria, 'temaMenu', $model->temaMenu);
				Yii::$app->globals->setRmodifica($rauditoria, 'temaContenido', $model->temaContenido);
				Yii::$app->globals->setRmodifica($rauditoria, 'tiempoSesion', $model->tiempoSesion);
				*/
				
				return $this->redirect(['update', 'id'=>$model->configuracionesSistemaID, 'update'=>'true']);
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
     * Deletes an existing Stylos model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
   
				
	public function actionDelete(){				
		$true = 0;	
		if ($selection=(array)Yii::$app->request->post('selection')) {
			foreach($selection as $id){
				$model = $this->findModel($id);					
				$model->load(Yii::$app->request->post());	        
				$model->regEstado= '0';	
				
				 if($model->save()){
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
     * Finds the Stylos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Stylos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Stylos::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
