<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\models\Formularios;
use app\models\FormulariosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;



/**
 * FormulariosController implements the CRUD actions for Formularios model.
 */
class FormulariosController extends Controller
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
		
		$frmSeguridad = 'menus';
		
		$permisosBtn = Yii::$app->db->createCommand("SELECT Acciones.accionID, Acciones.nombreAccion, Formularios.nombreFormulario, Formularios.tipoFormularioID, Formularios.urlArchivo FROM Acciones
			inner join AccionesFormularios on Acciones.accionID=AccionesFormularios.accionID 
			inner join Formularios on AccionesFormularios.formularioID=Formularios.formularioID
			inner join PerfilAccionFormulario on AccionesFormularios.accionFormularioID =PerfilAccionFormulario.accionFormularioID
			inner join PerfilesCompuestos on PerfilAccionFormulario.perfilID=PerfilesCompuestos.perfilID
			inner join Usuarios on Usuarios.usuarioID = PerfilesCompuestos.usuarioID
			where Formularios.urlArchivo='menus/index' and AccionesFormularios.estadoAccion=1 and AccionesFormularios.regEstado=1 and PerfilAccionFormulario.regEstado=1 and PerfilAccionFormulario.activoPerfilAccionFormulario=1 and Usuarios.usuarioID='".$usuarioIdToken."' group by AccionesFormularios.accionFormularioID")->queryAll();
			
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
                'only' => ['create', 'update', 'delete', 'index', 'deletedata'],
                'rules' => [
                    [
						'actions' => [$perAlta, $perEdit, $perdel, $perCons, $perElim],
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

    /**
     * Lists all Formularios models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FormulariosSearch();
      

		if(isset($_GET['clear'])){
			Yii::$app->session['FormulariosSearch'] = '';
			return $this->redirect(['index']);			
		}else{
			$params = Yii::$app->request->queryParams;

			if(count($params) <= 1){		  
				if(isset(Yii::$app->session['FormulariosSearch'])) {
					$params = Yii::$app->session['FormulariosSearch'];
				}else{
					Yii::$app->session['FormulariosSearch'] = $params;
				}
			}else{	
				if(isset(Yii::$app->request->queryParams['FormulariosSearch'])){
					Yii::$app->session['FormulariosSearch'] = $params;
				}else{
					$params = Yii::$app->session['FormulariosSearch'];
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
     * Displays a single Formularios model.
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

    /**
     * Creates a new Formularios model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
   public function actionCreate()
    {
        $model = new Formularios();
				
		if($model->load(Yii::$app->request->post())) {
			$fID = Yii::$app->globals->getFormularioToken('menus/index');
			$post = Yii::$app->request->post();
			$model->regEstado = 1;
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = $fID;
			$model->formID = 1;
			if(is_null($post['Formularios']['formID']) or empty($post['Formularios']['formID'])){
				$model->formID = 1;
			}
			
			if($model->save()){
				
				$rauditoria = Yii::$app->globals->setRegistro(3, 'Alta', 'Formularios', $fID);

				Yii::$app->globals->setRmodifica($rauditoria, 'formularioID', $model->formularioID);
				Yii::$app->globals->setRmodifica($rauditoria, 'tipoMenu', $model->tipoMenu);
				Yii::$app->globals->setRmodifica($rauditoria, 'formID', $model->formID);
				Yii::$app->globals->setRmodifica($rauditoria, 'nombreFormulario', $model->nombreFormulario);
				Yii::$app->globals->setRmodifica($rauditoria, 'urlArchivo', $model->urlArchivo);
				Yii::$app->globals->setRmodifica($rauditoria, 'estadoFormulario', $model->estadoFormulario);
				Yii::$app->globals->setRmodifica($rauditoria, 'icono', $model->icono);
				Yii::$app->globals->setRmodifica($rauditoria, 'menuID', $model->menuID);
				Yii::$app->globals->setRmodifica($rauditoria, 'aplicacionID', $model->aplicacionID);
				Yii::$app->globals->setRmodifica($rauditoria, 'catalogoID', $model->catalogoID);
				Yii::$app->globals->setRmodifica($rauditoria, 'textoID', $model->textoID);
				
				return $this->redirect(['create', 'insert' => 'true', 'id' => $model->formularioID]);
			}
		}
		

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Formularios model.
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
			$fID = Yii::$app->globals->getFormularioToken('menus/index');
			$post = Yii::$app->request->post();
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = $version + 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = $fID;
			
			if(is_null($post['Formularios']['formID']) or empty($post['Formularios']['formID'])){
				$model->formID = 1;
			}
			//$model->formID = 1;
			
			if($model->save()){
				
				$rauditoria = Yii::$app->globals->setRegistro(5, 'Editar', 'Formularios', $fID);

				Yii::$app->globals->setRmodifica($rauditoria, 'formularioID', $model->formularioID);
				Yii::$app->globals->setRmodifica($rauditoria, 'tipoMenu', $model->tipoMenu);
				Yii::$app->globals->setRmodifica($rauditoria, 'formID', $model->formID);
				Yii::$app->globals->setRmodifica($rauditoria, 'nombreFormulario', $model->nombreFormulario);
				Yii::$app->globals->setRmodifica($rauditoria, 'urlArchivo', $model->urlArchivo);
				Yii::$app->globals->setRmodifica($rauditoria, 'estadoFormulario', $model->estadoFormulario);
				Yii::$app->globals->setRmodifica($rauditoria, 'icono', $model->icono);
				Yii::$app->globals->setRmodifica($rauditoria, 'menuID', $model->menuID);
				Yii::$app->globals->setRmodifica($rauditoria, 'aplicacionID', $model->aplicacionID);
				Yii::$app->globals->setRmodifica($rauditoria, 'catalogoID', $model->catalogoID);
				Yii::$app->globals->setRmodifica($rauditoria, 'textoID', $model->textoID);
				
				return $this->redirect(['update', 'id' => $model->formularioID, 'update'=>'true']);
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
			$fID = Yii::$app->globals->getFormularioToken('menus/index');
			$rauditoria = Yii::$app->globals->setRegistro(4, 'Eliminar', 'Formularios', $fID);

			Yii::$app->globals->setRmodifica($rauditoria, 'formularioID', $model->formularioID);
			
			return $this->redirect(['index', 'delete'=>'true']);
		}
		

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Formularios model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
   
				
	public function actionDelete(){				
		$true = 0;	
		if ($selection=(array)Yii::$app->request->post('selection')) {
			$fID = Yii::$app->globals->getFormularioToken('menus/index');
			foreach($selection as $id){
				$model = $this->findModel($id);					
				$model->load(Yii::$app->request->post());	        
				$model->regEstado= '0';	
				
				 if($model->save()){
					 $rauditoria = Yii::$app->globals->setRegistro(4, 'Eliminar', 'Formularios', $fID);
					Yii::$app->globals->setRmodifica($rauditoria, 'formularioID', $model->formularioID);
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
     * Finds the Formularios model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Formularios the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Formularios::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
