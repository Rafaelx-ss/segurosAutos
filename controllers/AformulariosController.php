<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\models\Aformularios;
use app\models\AformulariosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;



/**
 * AformulariosController implements the CRUD actions for Aformularios model.
 */
class AformulariosController extends Controller
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
     * Lists all Aformularios models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AformulariosSearch();
      

		if(isset($_GET['clear'])){
			Yii::$app->session['AformulariosSearch'] = '';
			return $this->redirect(['index']);			
		}else{
			$params = Yii::$app->request->queryParams;

			if(count($params) <= 1){		  
				if(isset(Yii::$app->session['AformulariosSearch'])) {
					$params = Yii::$app->session['AformulariosSearch'];
				}else{
					Yii::$app->session['AformulariosSearch'] = $params;
				}
			}else{	
				if(isset(Yii::$app->request->queryParams['AformulariosSearch'])){
					Yii::$app->session['AformulariosSearch'] = $params;
				}else{
					$params = Yii::$app->session['AformulariosSearch'];
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
     * Displays a single Aformularios model.
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
     * Creates a new Aformularios model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
   public function actionCreate()
    {
        $model = new Aformularios();
				
		if($model->load(Yii::$app->request->post())) {
			$post = Yii::$app->request->post();		
			$fID = Yii::$app->globals->getFormularioToken('menus/index');
			
			$formulario = Yii::$app->db->createCommand("SELECT nombreFormulario FROM Formularios where formularioID='".$post['Aformularios']['formularioID']."'")->queryOne();
			
				
					
					$formAcciones = Yii::$app->db->createCommand("SELECT * FROM Acciones where accionID !=1 and estadoAccion=1 and regEstado=1")->queryAll();	
					foreach($formAcciones as $axn){
						$name = "";
						if(isset($_POST['chk_'.$axn['accionID']])){
							if($_POST['chk_'.$axn['accionID']] == 'ok'){
								$name = $formulario['nombreFormulario']."-".$axn['nombreAccion'];
								
								$insrt = "INSERT INTO AccionesFormularios (claveAccion, estadoAccion, accionID, formularioID, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion)VALUES('".$name."', 1, '".$axn['accionID']."', '".$post['Aformularios']['formularioID']."', 1, 1, NOW(), '".Yii::$app->user->identity->usuarioID."', 1, '".Yii::$app->globals->getVersion()."')";								
								Yii::$app->db->createCommand($insrt)->query();
								
								$rauditoria = Yii::$app->globals->setRegistro(3, 'Alta', 'AccionesFormularios', $fID);

								Yii::$app->globals->setRmodifica($rauditoria, 'claveAccion', $name);
								Yii::$app->globals->setRmodifica($rauditoria, 'estadoAccion', 1);
								Yii::$app->globals->setRmodifica($rauditoria, 'accionID', $axn['accionID']);
								Yii::$app->globals->setRmodifica($rauditoria, 'formularioID', $post['Aformularios']['formularioID']);
								
								
							}
						}
					}
				
			/*
				if(isset($_POST['pcon'])){
					if($_POST['pcon'] == 'ok'){
						$name1 = $formulario['nombreFormulario']."-Consulta";
						$inse1 = "INSERT INTO AccionesFormularios (claveAccion, estadoAccion, accionID, formularioID, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion)VALUES('".$name1."', 1, 2, '".$post['Aformularios']['formularioID']."', 1, 1, NOW(), '".Yii::$app->user->identity->usuarioID."', 1, '".Yii::$app->globals->getVersion()."')";
						Yii::$app->db->createCommand($inse1)->query();
						
						
						$rauditoria = Yii::$app->globals->setRegistro(3, 'Alta', 'AccionesFormularios', $fID);

						Yii::$app->globals->setRmodifica($rauditoria, 'claveAccion', $name1);
						Yii::$app->globals->setRmodifica($rauditoria, 'estadoAccion', 1);
						Yii::$app->globals->setRmodifica($rauditoria, 'accionID', 2);
						Yii::$app->globals->setRmodifica($rauditoria, 'formularioID', $post['Aformularios']['formularioID']);
					}
				}
				
				if(isset($_POST['palta'])){
					if($_POST['palta'] == 'ok'){						
						$name2 = $formulario['nombreFormulario']."-Alta";
						$inse2 = "INSERT INTO AccionesFormularios (claveAccion, estadoAccion, accionID, formularioID, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion)VALUES('".$name2."', 1, 3, '".$post['Aformularios']['formularioID']."', 1, 1, NOW(), '".Yii::$app->user->identity->usuarioID."', 1, '".Yii::$app->globals->getVersion()."')";
						Yii::$app->db->createCommand($inse2)->query();
						
						$rauditoria = Yii::$app->globals->setRegistro(3, 'Alta', 'AccionesFormularios', $fID);

						Yii::$app->globals->setRmodifica($rauditoria, 'claveAccion', $name2);
						Yii::$app->globals->setRmodifica($rauditoria, 'estadoAccion', 1);
						Yii::$app->globals->setRmodifica($rauditoria, 'accionID', 3);
						Yii::$app->globals->setRmodifica($rauditoria, 'formularioID', $post['Aformularios']['formularioID']);
					}
				}
				
				if(isset($_POST['pdel'])){
					if($_POST['pdel'] == 'ok'){
						$name3 = $formulario['nombreFormulario']."-Eliminar";
						$inse3 = "INSERT INTO AccionesFormularios (claveAccion, estadoAccion, accionID, formularioID, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion)VALUES('".$name3."', 1, 4, '".$post['Aformularios']['formularioID']."', 1, 1, NOW(), '".Yii::$app->user->identity->usuarioID."', 1, '".Yii::$app->globals->getVersion()."')";
						Yii::$app->db->createCommand($inse3)->query();
						
						$rauditoria = Yii::$app->globals->setRegistro(3, 'Alta', 'AccionesFormularios', $fID);

						Yii::$app->globals->setRmodifica($rauditoria, 'claveAccion', $name3);
						Yii::$app->globals->setRmodifica($rauditoria, 'estadoAccion', 1);
						Yii::$app->globals->setRmodifica($rauditoria, 'accionID', 4);
						Yii::$app->globals->setRmodifica($rauditoria, 'formularioID', $post['Aformularios']['formularioID']);
					}
				}
				
				if(isset($_POST['pedit'])){					
					if($_POST['pedit'] == 'ok'){						
						$name5 = $formulario['nombreFormulario']."-Editar";
						$inse5 = "INSERT INTO AccionesFormularios (claveAccion, estadoAccion, accionID, formularioID, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion)VALUES('".$name5."', 1, 5, '".$post['Aformularios']['formularioID']."', 1, 1, NOW(), '".Yii::$app->user->identity->usuarioID."', 1, '".Yii::$app->globals->getVersion()."')";
						Yii::$app->db->createCommand($inse5)->query();
						
						$rauditoria = Yii::$app->globals->setRegistro(3, 'Alta', 'AccionesFormularios', $fID);

						Yii::$app->globals->setRmodifica($rauditoria, 'claveAccion', $name3);
						Yii::$app->globals->setRmodifica($rauditoria, 'estadoAccion', 1);
						Yii::$app->globals->setRmodifica($rauditoria, 'accionID', 4);
						Yii::$app->globals->setRmodifica($rauditoria, 'formularioID', $post['Aformularios']['formularioID']);
					}
				}
				
				if(isset($_POST['pexcel'])){					
					if($_POST['pexcel'] == 'ok'){
						$name6 = $formulario['nombreFormulario']."-Excel";
						$inse6 = "INSERT INTO AccionesFormularios (claveAccion, estadoAccion, accionID, formularioID, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion)VALUES('".$name6."', 1, 6, '".$post['Aformularios']['formularioID']."', 1, 1, NOW(), '".Yii::$app->user->identity->usuarioID."', 1, '".Yii::$app->globals->getVersion()."')";
						Yii::$app->db->createCommand($inse6)->query();
					}
				}
				
				if(isset($_POST['ppdf'])){					
					if($_POST['ppdf'] == 'ok'){
						$name7 = $formulario['nombreFormulario']."-PDF";
						$inse7 = "INSERT INTO AccionesFormularios (claveAccion, estadoAccion, accionID, formularioID, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion)VALUES('".$name7."', 1, 7, '".$post['Aformularios']['formularioID']."', 1, 1, NOW(), '".Yii::$app->user->identity->usuarioID."', 1, '".Yii::$app->globals->getVersion()."')";
						Yii::$app->db->createCommand($inse7)->query();
					}
				}
			
				if(isset($_POST['psalir'])){					
					if($_POST['psalir'] == 'ok'){
						$name7 = $formulario['nombreFormulario']."-Salir";
						$inse8 = "INSERT INTO AccionesFormularios (claveAccion, estadoAccion, accionID, formularioID, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion)VALUES('".$name7."', 1, 8, '".$post['Aformularios']['formularioID']."', 1, 1, NOW(), '".Yii::$app->user->identity->usuarioID."', 1, '".Yii::$app->globals->getVersion()."')";
						Yii::$app->db->createCommand($inse8)->query();
					}
				}
				*/
				
				
			
				return $this->redirect(['create', 'insert' => 'true', 'id' => 1]);
			
		}
		

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Aformularios model.
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
				
				$rauditoria = Yii::$app->globals->setRegistro(5, 'Editar', 'AccionesFormularios', $fID);
				
				Yii::$app->globals->setRmodifica($rauditoria, 'accionFormularioID', $model->accionFormularioID);
				Yii::$app->globals->setRmodifica($rauditoria, 'claveAccion', $model->claveAccion);
				Yii::$app->globals->setRmodifica($rauditoria, 'estadoAccion', $model->estadoAccion);
				Yii::$app->globals->setRmodifica($rauditoria, 'accionID', $model->accionID);
				Yii::$app->globals->setRmodifica($rauditoria, 'formularioID', $model->formularioID);
				
				return $this->redirect(['update', 'id' => $model->accionFormularioID, 'update'=>'true']);
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
			$rauditoria = Yii::$app->globals->setRegistro(4, 'Eliminar', 'AccionesFormularios', $fID);
			Yii::$app->globals->setRmodifica($rauditoria, 'accionFormularioID', $model->accionFormularioID);
			return $this->redirect(['index', 'delete'=>'true']);
		}
		

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Aformularios model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
   
				
	public function actionDelete(){				
		$true = 0;	
		$fID = Yii::$app->globals->getFormularioToken('menus/index');
		if ($selection=(array)Yii::$app->request->post('selection')) {
			foreach($selection as $id){
				$model = $this->findModel($id);					
				$model->load(Yii::$app->request->post());	        
				$model->regEstado= '0';	
				
				 if($model->save()){					 
					$rauditoria = Yii::$app->globals->setRegistro(4, 'Eliminar', 'AccionesFormularios', $fID);
					Yii::$app->globals->setRmodifica($rauditoria, 'accionFormularioID', $model->accionFormularioID);
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
     * Finds the Aformularios model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Aformularios the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Aformularios::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
