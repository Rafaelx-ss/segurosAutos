<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\models\Menus;
use app\models\MenusSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;



/**
 * MenusController implements the CRUD actions for Menus model.
 */
class MenusController extends Controller
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
                'only' => ['create', 'update', 'delete', 'index'],
                'rules' => [
                    [
                        'actions' => [$perAlta, $perEdit, $perdel, $perCons, 'change', $perElim],
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
     * Lists all Menus models.
     * @return mixed
     */
	public function actionChange()
	{

			//echo $_POST['item'][0];
		    $i = 1;
		    foreach ($_POST['item'] as $value) {
		        // Build statement:
				$update_contrato = Yii::$app->db->createCommand("UPDATE tbl_itemplate SET Orden_itemplate = ".$i." WHERE Id_itemplate = ".$_POST['item'][($i-1)]."")->query();		        // Execute statement:
		        if($update_contrato) {
			    	echo "Record modified successfully.";
		        } else {
			    	echo "ERROR: Could not execute $sql";
		        }
		        $i++;
		    }
	}
	
	
	
    public function actionIndex()
    {
        $searchModel = new MenusSearch();
      

		if(isset($_GET['clear'])){
			Yii::$app->session['MenusSearch'] = '';
			return $this->redirect(['index']);			
		}else{
			$params = Yii::$app->request->queryParams;

			if(count($params) <= 1){		  
				if(isset(Yii::$app->session['MenusSearch'])) {
					$params = Yii::$app->session['MenusSearch'];
				}else{
					Yii::$app->session['MenusSearch'] = $params;
				}
			}else{	
				if(isset(Yii::$app->request->queryParams['MenusSearch'])){
					Yii::$app->session['MenusSearch'] = $params;
				}else{
					$params = Yii::$app->session['MenusSearch'];
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
     * Displays a single Menus model.
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
     * Creates a new Menus model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Menus();
		
		if($model->load(Yii::$app->request->post())) {
			$fID = Yii::$app->globals->getFormularioToken('menus/index');
			$post = Yii::$app->request->post();
			$model->regEstado = 1;
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = $fID;
			
			if($model->save()){
				
				$rauditoria = Yii::$app->globals->setRegistro(3, 'Alta', 'Menus', $fID);

				Yii::$app->globals->setRmodifica($rauditoria, 'menuID', $model->menuID);
				Yii::$app->globals->setRmodifica($rauditoria, 'nombreMenu', $model->nombreMenu);
				Yii::$app->globals->setRmodifica($rauditoria, 'urlPagina', $model->urlPagina);
				Yii::$app->globals->setRmodifica($rauditoria, 'imagen', $model->imagen);
				Yii::$app->globals->setRmodifica($rauditoria, 'menuPadre', $model->menuPadre);
				Yii::$app->globals->setRmodifica($rauditoria, 'orden', $model->orden);
				Yii::$app->globals->setRmodifica($rauditoria, 'textoID', $model->textoID);
				
				return $this->redirect(['create', 'insert' => 'true', 'id' => $model->menuID]);
			}
		}
		

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Menus model.
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
			
			if($model->save()){
				
				$rauditoria = Yii::$app->globals->setRegistro(5, 'Editar', 'Menus', $fID);

				Yii::$app->globals->setRmodifica($rauditoria, 'menuID', $model->menuID);
				Yii::$app->globals->setRmodifica($rauditoria, 'nombreMenu', $model->nombreMenu);
				Yii::$app->globals->setRmodifica($rauditoria, 'urlPagina', $model->urlPagina);
				Yii::$app->globals->setRmodifica($rauditoria, 'imagen', $model->imagen);
				Yii::$app->globals->setRmodifica($rauditoria, 'menuPadre', $model->menuPadre);
				Yii::$app->globals->setRmodifica($rauditoria, 'orden', $model->orden);
				Yii::$app->globals->setRmodifica($rauditoria, 'textoID', $model->textoID);
				
				return $this->redirect(['update', 'id' => $model->menuID, 'update'=>'true']);
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
			$rauditoria = Yii::$app->globals->setRegistro(4, 'Eliminar', 'Menus', $fID);

			Yii::$app->globals->setRmodifica($rauditoria, 'menuID', $model->menuID);
			
			return $this->redirect(['index', 'delete'=>'true']);
		}
		

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Menus model.
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
					 
					$rauditoria = Yii::$app->globals->setRegistro(4, 'Eliminar', 'Menus', $fID);

					Yii::$app->globals->setRmodifica($rauditoria, 'menuID', $model->menuID);
					 
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
     * Finds the Menus model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Menus the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Menus::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
