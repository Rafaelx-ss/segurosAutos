<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\models\Tformularios;
use app\models\TformulariosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;



/**
 * TformulariosController implements the CRUD actions for Tformularios model.
 */
class TformulariosController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
			 'access' => [
                'class' => AccessControl::className(),
                'only' => ['create', 'update', 'delete', 'index'],
                'rules' => [
                    [
                        'actions' => [],
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
     * Lists all Tformularios models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TformulariosSearch();
      

		if(isset($_GET['clear'])){
			Yii::$app->session['TformulariosSearch'] = '';
			return $this->redirect(['index']);			
		}else{
			$params = Yii::$app->request->queryParams;

			if(count($params) <= 1){		  
				if(isset(Yii::$app->session['TformulariosSearch'])) {
					$params = Yii::$app->session['TformulariosSearch'];
				}else{
					Yii::$app->session['TformulariosSearch'] = $params;
				}
			}else{	
				if(isset(Yii::$app->request->queryParams['TformulariosSearch'])){
					Yii::$app->session['TformulariosSearch'] = $params;
				}else{
					$params = Yii::$app->session['TformulariosSearch'];
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
     * Displays a single Tformularios model.
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
     * Creates a new Tformularios model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Tformularios();

        if($model->load(Yii::$app->request->post())) {
			$post = Yii::$app->request->post();
			$model->regEstado = 1;
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = 1;
			
			if($model->save()){
				return $this->redirect(['create', 'insert' => 'true', 'id' => $model->tipoFormularioID]);
			}
		}

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Tformularios model.
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
			$post = Yii::$app->request->post();
			$model->regFechaUltimaModificacion = date('Y-m-d H:i:s'); 
			$model->regUsuarioUltimaModificacion = Yii::$app->user->identity->usuarioID;
			$model->versionRegistro = $version + 1;
			$model->regVersionUltimaModificacion = Yii::$app->globals->getVersion();
			$model->regFormularioUltimaModificacion = 1;
			
			if($model->save()){
				return $this->redirect(['update', 'id' => $model->tipoFormularioID, 'update'=>'true']);
			}
		}

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Tformularios model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
   
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
     * Finds the Tformularios model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tformularios the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Tformularios::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
