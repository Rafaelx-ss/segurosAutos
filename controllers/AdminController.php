<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\models\Admin;
use app\models\AdminSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


use app\components\AccessRule;
/**
 * AdminController implements the CRUD actions for Admin model.
 */
class AdminController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
			 'access' => [
                'class' => AccessControl::className(),
				'ruleConfig' => [
                	'class' => AccessRule::className(),
                ],
                'only' => ['create', 'update', 'delete', 'index'],
                'rules' => [
                    [
                        'actions' => ['create', 'update', 'delete', 'index'],
                        'allow' => true,
                        'roles' => [
                               Admin::ROLE_ADMIN 
                        ],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className()
            ],
        ];
    }

    /**
     * Lists all Admin models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AdminSearch();
      

		if(isset($_GET['clear'])){
			Yii::$app->session['AdminSearch'] = '';
			return $this->redirect(['index']);			
		}else{
			$params = Yii::$app->request->queryParams;

			if(count($params) <= 1){		  
				if(isset(Yii::$app->session['AdminSearch'])) {
					$params = Yii::$app->session['AdminSearch'];
				}else{
					Yii::$app->session['AdminSearch'] = $params;
				}
			}else{	
				if(isset(Yii::$app->request->queryParams['AdminSearch'])){
					Yii::$app->session['AdminSearch'] = $params;
				}else{
					$params = Yii::$app->session['AdminSearch'];
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
     * Displays a single Admin model.
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
     * Creates a new Admin model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Admin();
		
		if($model->load(Yii::$app->request->post())) {
			$post = Yii::$app->request->post();
			$model->Pass_hadmin = 's'.md5($post['Admin']['Pass_radmin']).'-07';
			$model->AuthKey = Yii::$app->globals->genKey();
			if($model->save()){
				 return $this->redirect(['create', 'insert' => 'true', 'id' => $model->Id_admin]);
			}				
		}
			

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Admin model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

       if($model->load(Yii::$app->request->post())) {
			$post = Yii::$app->request->post();
			$model->Pass_hadmin = 's'.md5($post['Admin']['Pass_radmin']).'-07';
			if($model->save()){
				 return $this->redirect(['update', 'id' => $model->Id_admin, 'update'=>'true']);
			}				
		}

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Admin model.
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
				$model->Status_admin = 'Eliminado';	
				
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
     * Finds the Admin model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Admin the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Admin::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
