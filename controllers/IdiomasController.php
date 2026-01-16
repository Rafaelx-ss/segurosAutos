<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\models\Idiomas;
use app\models\IdiomasSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;



/**
 * IdiomasController implements the CRUD actions for Idiomas model.
 */
class IdiomasController extends Controller
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
		
		$frmSeguridad = 'idiomas';
		
		$permisosBtn = Yii::$app->db->createCommand("SELECT Acciones.accionID, Acciones.nombreAccion, Formularios.nombreFormulario, Formularios.tipoFormularioID, Formularios.urlArchivo FROM Acciones
			inner join AccionesFormularios on Acciones.accionID=AccionesFormularios.accionID 
			inner join Formularios on AccionesFormularios.formularioID=Formularios.formularioID
			inner join PerfilAccionFormulario on AccionesFormularios.accionFormularioID =PerfilAccionFormulario.accionFormularioID
			inner join PerfilesCompuestos on PerfilAccionFormulario.perfilID=PerfilesCompuestos.perfilID
			inner join Usuarios on Usuarios.usuarioID = PerfilesCompuestos.usuarioID
			where Formularios.urlArchivo='idiomas/index' and AccionesFormularios.estadoAccion=1 and AccionesFormularios.regEstado=1 and PerfilAccionFormulario.regEstado=1 and PerfilAccionFormulario.activoPerfilAccionFormulario=1 and Usuarios.usuarioID='".$usuarioIdToken."' group by AccionesFormularios.accionFormularioID")->queryAll();
			
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
						'actions' => [$perAlta, $perEdit, $perdel, $perCons, 'changeidioma', 'archivotraduccion', 'test'],
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
	
	public function actionTest(){
		print_r(Yii::$app->session['trasleteSelct']);
	}
	
	public function actionArchivotraduccion(){
		$dirTraduccion = Yii::$app->basePath.'/traducciones/';	
		
		
		
		$idioma = Yii::$app->db->createCommand('SELECT * FROM Idiomas where regEstado=1 and activoIdioma=1')->queryAll();

		foreach($idioma  as $row){
$array = "";
			$fichero = 'traduccion_'.$row['idiomaID'].'.php';
			$texto = Yii::$app->db->createCommand('SELECT Textos.textoID, TextosIdiomas.texto FROM Textos inner join TextosIdiomas on Textos.textoID=TextosIdiomas.textoID  where TextosIdiomas.idiomaID="'.$row['idiomaID'].'"')->queryAll();
$array .= "<?php 
//archivo de lenguaje generado el dia ".date('Y-m-d H:i:s')."
//mensajes para traduccion 
\$mensajes = [";			
			foreach($texto as $data){
$array .= "'".$data['textoID']."'=>'".$data['texto']."',";
			}
$array .= "];
?>";
			file_put_contents($dirTraduccion.$fichero, $array, LOCK_EX);
		}
	
		Yii::$app->globals->cargarTraductor(Yii::$app->session['idiomaId']);
		return $this->redirect(['index', 'create' => 'true']);
	}
	
	 public function actionChangeidioma()
    {
		$session = Yii::$app->session;
		 
		if(isset($_POST['id']) and isset($_POST['flag'])){
			Yii::$app->session->set('idiomaId', $_POST['id']);
			Yii::$app->session->set('idiomaFlag', $_POST['flag']);
			Yii::$app->globals->cargarTraductor($_POST['id']);
		}else{
			Yii::$app->session->set('idiomaId', 1); 
			Yii::$app->session->set('idiomaFlag', 'MX');
		 }
		 
		 echo 'true';
	 }
    /**
     * Lists all Idiomas models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new IdiomasSearch();
      

		if(isset($_GET['clear'])){
			Yii::$app->session['IdiomasSearch'] = '';
			return $this->redirect(['index']);			
		}else{
			$params = Yii::$app->request->queryParams;

			if(count($params) <= 1){		  
				if(isset(Yii::$app->session['IdiomasSearch'])) {
					$params = Yii::$app->session['IdiomasSearch'];
				}else{
					Yii::$app->session['IdiomasSearch'] = $params;
				}
			}else{	
				if(isset(Yii::$app->request->queryParams['IdiomasSearch'])){
					Yii::$app->session['IdiomasSearch'] = $params;
				}else{
					$params = Yii::$app->session['IdiomasSearch'];
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
     * Displays a single Idiomas model.
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
     * Creates a new Idiomas model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Idiomas();
		
		if($model->load(Yii::$app->request->post())) {
			$fID = Yii::$app->globals->getFormularioToken('idiomas/index');
			$post = Yii::$app->request->post();
			$model->regEstado = 1;
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = $fID ;
			
			if($model->save()){
				$rauditoria = Yii::$app->globals->setRegistro(3, 'Alta', 'Idiomas', $fID);

				Yii::$app->globals->setRmodifica($rauditoria, 'idiomaID', $model->idiomaID);
				Yii::$app->globals->setRmodifica($rauditoria, 'nombreIdioma', $model->nombreIdioma);
				Yii::$app->globals->setRmodifica($rauditoria, 'iconIdioma', $model->iconIdioma);
				Yii::$app->globals->setRmodifica($rauditoria, 'activoIdioma', $model->activoIdioma);
				
				return $this->redirect(['create', 'insert' => 'true', 'id' => $model->idiomaID]);
			}
		}
		

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Idiomas model.
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
			$fID = Yii::$app->globals->getFormularioToken('idiomas/index');
			$post = Yii::$app->request->post();
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = $version + 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = $fID;
			
			if($model->save()){
				
				$rauditoria = Yii::$app->globals->setRegistro(5, 'Editar', 'Idiomas', $fID);

				Yii::$app->globals->setRmodifica($rauditoria, 'idiomaID', $model->idiomaID);
				Yii::$app->globals->setRmodifica($rauditoria, 'nombreIdioma', $model->nombreIdioma);
				Yii::$app->globals->setRmodifica($rauditoria, 'iconIdioma', $model->iconIdioma);
				Yii::$app->globals->setRmodifica($rauditoria, 'activoIdioma', $model->activoIdioma);
				
				return $this->redirect(['update', 'id' => $model->idiomaID, 'update'=>'true']);
			}
		}

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Idiomas model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
   
				
	public function actionDelete(){				
		$true = 0;	
		if ($selection=(array)Yii::$app->request->post('selection')) {
			$fID = Yii::$app->globals->getFormularioToken('idiomas/index');
				
			foreach($selection as $id){
				$model = $this->findModel($id);					
				$model->load(Yii::$app->request->post());	        
				$model->regEstado= '0';	
				
				 if($model->save()){
					 $rauditoria = Yii::$app->globals->setRegistro(4, 'Eliminar', 'Idiomas', $fID);
				     Yii::$app->globals->setRmodifica($rauditoria, 'idiomaID', $model->idiomaID);
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
     * Finds the Idiomas model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Idiomas the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Idiomas::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
