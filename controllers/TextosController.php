<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\models\Textos;
use app\models\TextosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;



/**
 * TextosController implements the CRUD actions for Textos model.
 */
class TextosController extends Controller
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
		
		$frmSeguridad = 'textos';
		
		$permisosBtn = Yii::$app->db->createCommand("SELECT Acciones.accionID, Acciones.nombreAccion, Formularios.nombreFormulario, Formularios.tipoFormularioID, Formularios.urlArchivo FROM Acciones
			inner join AccionesFormularios on Acciones.accionID=AccionesFormularios.accionID 
			inner join Formularios on AccionesFormularios.formularioID=Formularios.formularioID
			inner join PerfilAccionFormulario on AccionesFormularios.accionFormularioID =PerfilAccionFormulario.accionFormularioID
			inner join PerfilesCompuestos on PerfilAccionFormulario.perfilID=PerfilesCompuestos.perfilID
			inner join Usuarios on Usuarios.usuarioID = PerfilesCompuestos.usuarioID
			where Formularios.urlArchivo='textos/index' and AccionesFormularios.estadoAccion=1 and AccionesFormularios.regEstado=1 and PerfilAccionFormulario.regEstado=1 and PerfilAccionFormulario.activoPerfilAccionFormulario=1 and Usuarios.usuarioID='".$usuarioIdToken."' group by AccionesFormularios.accionFormularioID")->queryAll();
			
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
                'only' => ['create', 'update', 'delete', 'index', 'createform', 'getselect'],
                'rules' => [
                    [
						'actions' => [$perAlta, $perEdit, $perdel, $perCons, 'createform', 'getselect'],
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
     * Lists all Textos models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TextosSearch();
      

		if(isset($_GET['clear'])){
			Yii::$app->session['TextosSearch'] = '';
			return $this->redirect(['index']);			
		}else{
			$params = Yii::$app->request->queryParams;

			if(count($params) <= 1){		  
				if(isset(Yii::$app->session['TextosSearch'])) {
					$params = Yii::$app->session['TextosSearch'];
				}else{
					Yii::$app->session['TextosSearch'] = $params;
				}
			}else{	
				if(isset(Yii::$app->request->queryParams['TextosSearch'])){
					Yii::$app->session['TextosSearch'] = $params;
				}else{
					$params = Yii::$app->session['TextosSearch'];
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
     * Displays a single Textos model.
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
     * Creates a new Textos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Textos();
		
		if($model->load(Yii::$app->request->post())) {
			$fID = Yii::$app->globals->getFormularioToken('textos/index');
			$post = Yii::$app->request->post();
			$model->regEstado = 1;
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = $fID;
			
			if($model->save()){
				
				
				$rauditoria = Yii::$app->globals->setRegistro(3, 'Alta', 'Textos', $fID);

				Yii::$app->globals->setRmodifica($rauditoria, 'textoID', $model->textoID);
				Yii::$app->globals->setRmodifica($rauditoria, 'tipoTexto', $model->tipoTexto);
				Yii::$app->globals->setRmodifica($rauditoria, 'nombreTexto', $model->nombreTexto);
				Yii::$app->globals->setRmodifica($rauditoria, 'activoTexto', $model->activoTexto);
				
				
				return $this->redirect(['create', 'insert' => 'true', 'id' => $model->textoID]);
			}
		}
		
		

        return $this->render('create', [
            'model' => $model,
        ]);
    }
	
	
	
	//formulario para crate ajax
	public function actionCreateform()
    {
        $model = new Textos();
				
		if($model->load(Yii::$app->request->post())) {
			
			$post = Yii::$app->request->post();
			$model->regEstado = 1;
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = 4;
			
			if($model->save()){
				
				$rauditoria = Yii::$app->globals->setRegistro(3, 'Alta', 'Textos', 4);

				Yii::$app->globals->setRmodifica($rauditoria, 'textoID', $model->textoID);
				Yii::$app->globals->setRmodifica($rauditoria, 'tipoTexto', $model->tipoTexto);
				Yii::$app->globals->setRmodifica($rauditoria, 'nombreTexto', $model->nombreTexto);
				Yii::$app->globals->setRmodifica($rauditoria, 'activoTexto', $model->activoTexto);
				
				echo 'true';
			}else{
				echo 'false';
			}
		}else{
			echo 'false';
		}
    }
    /**
     * Updates an existing Textos model.
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
			$fID = Yii::$app->globals->getFormularioToken('textos/index');
			$post = Yii::$app->request->post();
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = $version + 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = $fID;
			
			if($model->save()){
				
				$rauditoria = Yii::$app->globals->setRegistro(5, 'Editar', 'Textos', $fID);

				Yii::$app->globals->setRmodifica($rauditoria, 'textoID', $model->textoID);
				Yii::$app->globals->setRmodifica($rauditoria, 'tipoTexto', $model->tipoTexto);
				Yii::$app->globals->setRmodifica($rauditoria, 'nombreTexto', $model->nombreTexto);
				Yii::$app->globals->setRmodifica($rauditoria, 'activoTexto', $model->activoTexto);
				
				return $this->redirect(['update', 'id' => $model->textoID, 'update'=>'true']);
			}
		}

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Textos model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
   
				
	public function actionDelete(){				
		$true = 0;	
		if ($selection=(array)Yii::$app->request->post('selection')) {
			 $fID = Yii::$app->globals->getFormularioToken('textos/index');
			
			foreach($selection as $id){
				$model = $this->findModel($id);					
				$model->load(Yii::$app->request->post());	        
				$model->regEstado= '0';	
				
				 if($model->save()){
					
					$rauditoria = Yii::$app->globals->setRegistro(4, 'Eliminar', 'Textos', $fID);
					Yii::$app->globals->setRmodifica($rauditoria, 'textoID', $model->textoID);
					 
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
     * Finds the Textos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Textos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Textos::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
