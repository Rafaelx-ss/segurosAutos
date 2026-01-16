<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\models\Pcompuestos;
use app\models\PcompuestosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;



/**
 * PcompuestosController implements the CRUD actions for Pcompuestos model.
 */
class PcompuestosController extends Controller
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
		
		$frmSeguridad = 'perfiles';
		
		$permisosBtn = Yii::$app->db->createCommand("SELECT Acciones.accionID, Acciones.nombreAccion, Formularios.nombreFormulario, Formularios.tipoFormularioID, Formularios.urlArchivo FROM Acciones
			inner join AccionesFormularios on Acciones.accionID=AccionesFormularios.accionID 
			inner join Formularios on AccionesFormularios.formularioID=Formularios.formularioID
			inner join PerfilAccionFormulario on AccionesFormularios.accionFormularioID =PerfilAccionFormulario.accionFormularioID
			inner join PerfilesCompuestos on PerfilAccionFormulario.perfilID=PerfilesCompuestos.perfilID
			inner join Usuarios on Usuarios.usuarioID = PerfilesCompuestos.usuarioID
			where Formularios.urlArchivo='perfiles/index' and AccionesFormularios.estadoAccion=1 and AccionesFormularios.regEstado=1 and PerfilAccionFormulario.regEstado=1 and PerfilAccionFormulario.activoPerfilAccionFormulario=1 and Usuarios.usuarioID='".$usuarioIdToken."' group by AccionesFormularios.accionFormularioID")->queryAll();
			
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
     * Lists all Pcompuestos models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PcompuestosSearch();
      

		if(isset($_GET['clear'])){
			Yii::$app->session['PcompuestosSearch'] = '';
			return $this->redirect(['index']);			
		}else{
			$params = Yii::$app->request->queryParams;

			if(count($params) <= 1){		  
				if(isset(Yii::$app->session['PcompuestosSearch'])) {
					$params = Yii::$app->session['PcompuestosSearch'];
				}else{
					Yii::$app->session['PcompuestosSearch'] = $params;
				}
			}else{	
				if(isset(Yii::$app->request->queryParams['PcompuestosSearch'])){
					Yii::$app->session['PcompuestosSearch'] = $params;
				}else{
					$params = Yii::$app->session['PcompuestosSearch'];
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
     * Displays a single Pcompuestos model.
     * @param integer $perfilCompuestoID
     * @param integer $perfilID
     * @param integer $establecimientoID
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
        $model = new Pcompuestos();
				
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
     * Creates a new Pcompuestos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Pcompuestos();
				
		if($model->load(Yii::$app->request->post())) {
			$fID = Yii::$app->globals->getFormularioToken('perfiles/index');
			$post = Yii::$app->request->post();
			$model->regEstado = 1;
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = $fID;
			
			if($model->save()){
				
				$rauditoria = Yii::$app->globals->setRegistro(3, 'Alta', 'PerfilesCompuestos', $fID);

				Yii::$app->globals->setRmodifica($rauditoria, 'perfilCompuestoID', $model->perfilCompuestoID);
				Yii::$app->globals->setRmodifica($rauditoria, 'usuarioID', $model->usuarioID);
				Yii::$app->globals->setRmodifica($rauditoria, 'perfilID', $model->perfilID);
				
				return $this->redirect(['create', 'insert' => 'true', 'id'=>$model->perfilCompuestoID]);
			}
		}
		

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Pcompuestos model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $perfilCompuestoID
     * @param integer $perfilID
     * @param integer $establecimientoID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
		$version = $model->versionRegistro;		
		if($model->load(Yii::$app->request->post())) {
			$fID = Yii::$app->globals->getFormularioToken('perfiles/index');
			$post = Yii::$app->request->post();
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = $version + 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = $fID;
			
			if($model->save()){
				
				$rauditoria = Yii::$app->globals->setRegistro(5, 'Editar', 'PerfilesCompuestos', $fID);

				Yii::$app->globals->setRmodifica($rauditoria, 'perfilCompuestoID', $model->perfilCompuestoID);
				Yii::$app->globals->setRmodifica($rauditoria, 'usuarioID', $model->usuarioID);
				Yii::$app->globals->setRmodifica($rauditoria, 'perfilID', $model->perfilID);
				
				return $this->redirect(['update', 'id'=>$model->perfilCompuestoID, 'update'=>'true']);
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
			$fID = Yii::$app->globals->getFormularioToken('perfiles/index');
			$rauditoria = Yii::$app->globals->setRegistro(4, 'Eliminar', 'PerfilesCompuestos', $fID);

			Yii::$app->globals->setRmodifica($rauditoria, 'perfilCompuestoID', $model->perfilCompuestoID);
			
			return $this->redirect(['index', 'delete'=>'true']);
		}
		

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Pcompuestos model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $perfilCompuestoID
     * @param integer $perfilID
     * @param integer $establecimientoID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
   
				
	public function actionDelete(){				
		$true = 0;	
		if ($selection=(array)Yii::$app->request->post('selection')) {
			$fID = Yii::$app->globals->getFormularioToken('perfiles/index');
			foreach($selection as $id){
				$model = $this->findModel($id);					
				$model->load(Yii::$app->request->post());	        
				$model->regEstado= '0';	
				
				 if($model->save()){
					 
					 $rauditoria = Yii::$app->globals->setRegistro(4, 'Eliminar', 'PerfilesCompuestos', $fID);

					 Yii::$app->globals->setRmodifica($rauditoria, 'perfilCompuestoID', $model->perfilCompuestoID);
					 
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
     * Finds the Pcompuestos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $perfilCompuestoID
     * @param integer $perfilID
     * @param integer $establecimientoID
     * @return Pcompuestos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Pcompuestos::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
