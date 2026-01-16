<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\models\Camposgrid;
use app\models\CamposgridSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;



/**
 * CamposgridController implements the CRUD actions for Camposgrid model.
 */
class CamposgridController extends Controller
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
		
		$frmSeguridad = 'catalogos';
		
		$permisosBtn = Yii::$app->db->createCommand("SELECT Acciones.accionID, Acciones.nombreAccion, Formularios.nombreFormulario, Formularios.tipoFormularioID, Formularios.urlArchivo FROM Acciones
			inner join AccionesFormularios on Acciones.accionID=AccionesFormularios.accionID 
			inner join Formularios on AccionesFormularios.formularioID=Formularios.formularioID
			inner join PerfilAccionFormulario on AccionesFormularios.accionFormularioID =PerfilAccionFormulario.accionFormularioID
			inner join PerfilesCompuestos on PerfilAccionFormulario.perfilID=PerfilesCompuestos.perfilID
			inner join Usuarios on Usuarios.usuarioID = PerfilesCompuestos.usuarioID
			where Formularios.urlArchivo='catalogos/index' and AccionesFormularios.estadoAccion=1 and AccionesFormularios.regEstado=1 and PerfilAccionFormulario.regEstado=1 and PerfilAccionFormulario.activoPerfilAccionFormulario=1 and Usuarios.usuarioID='".$usuarioIdToken."' group by AccionesFormularios.accionFormularioID")->queryAll();
			
			foreach($permisosBtn as $dataPbtn){
				$urlSeguridad = explode("/", $dataPbtn['urlArchivo']);
				if(isset($urlSeguridad[0])){
					if($frmSeguridad == $urlSeguridad[0]){
						if(isset($dataPbtn['accionID'])){
							if($dataPbtn['accionID'] == '2'){  }	
							if($dataPbtn['accionID'] == '3'){ $perAlta = 'create'; }	
							if($dataPbtn['accionID'] == '4'){ $perElim = 'deletedata'; }
							if($dataPbtn['accionID'] == '5'){ $perEdit = 'update'; $perCons = 'index'; }
							if($dataPbtn['accionID'] == '6'){ $perExcel = 'xportexcel'; }
							if($dataPbtn['accionID'] == '7'){ $perPdf = 'xportpdf'; }
						}
					}
				}
			}
		
        return [
			 'access' => [
                'class' => AccessControl::className(),
                'only' => ['create', 'update', 'delete', 'index'],
                'rules' => [
                    [
                        'actions' => [$perEdit, 'delete', $perCons],
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
     * Lists all Camposgrid models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CamposgridSearch();
      

		if(isset($_GET['clear'])){
			Yii::$app->session['CamposgridSearch'] = '';
			return $this->redirect(['index&token='.$_GET['token']]);			
		}else{
			$params = Yii::$app->request->queryParams;

			if(count($params) <= 1){		  
				if(isset(Yii::$app->session['CamposgridSearch'])) {
					$params = Yii::$app->session['CamposgridSearch'];
				}else{
					Yii::$app->session['CamposgridSearch'] = $params;
				}
			}else{	
				if(isset(Yii::$app->request->queryParams['CamposgridSearch'])){
					Yii::$app->session['CamposgridSearch'] = $params;
				}else{
					$params = Yii::$app->session['CamposgridSearch'];
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
     * Displays a single Camposgrid model.
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
     * Creates a new Camposgrid model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Camposgrid();
		
		if($model->load(Yii::$app->request->post())) {
			$fID = Yii::$app->globals->getFormularioToken('catalogos/index');
			$post = Yii::$app->request->post();
			$model->regEstado = 1;
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = $fID;
			
			if($model->save()){
				
				$rauditoria = Yii::$app->globals->setRegistro(3, 'Alta', 'CamposGrid', $fID);

				Yii::$app->globals->setRmodifica($rauditoria, 'campoGridID', $model->campoGridID);
				Yii::$app->globals->setRmodifica($rauditoria, 'nombreCampo', $model->nombreCampo);
				Yii::$app->globals->setRmodifica($rauditoria, 'searchVisible', $model->searchVisible);
				Yii::$app->globals->setRmodifica($rauditoria, 'visible', $model->visible);
				Yii::$app->globals->setRmodifica($rauditoria, 'catalogoID', $model->catalogoID);
				Yii::$app->globals->setRmodifica($rauditoria, 'textoID', $model->textoID);
				
				return $this->redirect(['create', 'insert' => 'true', 'id' => $model->campoGridID]);
			}
		}
		

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Camposgrid model.
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
			$fID = Yii::$app->globals->getFormularioToken('catalogos/index');
			$post = Yii::$app->request->post();
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = $version + 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = $fID;
			
			if($model->save()){
				
				$rauditoria = Yii::$app->globals->setRegistro(5, 'Editar', 'CamposGrid', $fID);

				Yii::$app->globals->setRmodifica($rauditoria, 'campoGridID', $model->campoGridID);
				Yii::$app->globals->setRmodifica($rauditoria, 'nombreCampo', $model->nombreCampo);
				Yii::$app->globals->setRmodifica($rauditoria, 'searchVisible', $model->searchVisible);
				Yii::$app->globals->setRmodifica($rauditoria, 'visible', $model->visible);
				Yii::$app->globals->setRmodifica($rauditoria, 'catalogoID', $model->catalogoID);
				Yii::$app->globals->setRmodifica($rauditoria, 'textoID', $model->textoID);
				
				return $this->redirect(['update', 'id' => $model->campoGridID, 'token'=>$_GET['token'], 'update'=>'true']);
			}
		}

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Camposgrid model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
   
				
	public function actionDelete(){				
		$true = 0;	
		if ($selection=(array)Yii::$app->request->post('selection')) {
			$fID = Yii::$app->globals->getFormularioToken('catalogos/index');
			foreach($selection as $id){
				$model = $this->findModel($id);					
				$model->load(Yii::$app->request->post());	        
				$model->regEstado= '0';	
				
				 if($model->save()){
					 
					$rauditoria = Yii::$app->globals->setRegistro(4, 'Eliminar', 'CamposGrid', $fID);
					Yii::$app->globals->setRmodifica($rauditoria, 'campoGridID', $model->campoGridID);
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
     * Finds the Camposgrid model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Camposgrid the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Camposgrid::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
