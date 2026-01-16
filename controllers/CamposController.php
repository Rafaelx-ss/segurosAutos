<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\models\Campos;
use app\models\CamposSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

use app\assets\AppAsset;
use yii\helpers\Url;

use kartik\datetime\DateTimePicker;

/**
 * CamposController implements the CRUD actions for Campos model.
 */
class CamposController extends Controller
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
                        'actions' => [$perEdit, 'delete', $perCons, 'getform'],
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

	
	 public function actionGetform(){
		 
		 if(isset($_POST['idCatalogo'])){
			 $catalogo = Yii::$app->db->createCommand("SELECT * FROM Catalogos where catalogoID='".$_POST['idCatalogo']."'")->queryOne();
			 $varQry = '';
			 $valueField = '';
			 $textField = '';
			 $nombreCampo = '';
			 $urlSelect = '';
			 
			 if(isset($_POST['qry'])){ $varQry = $_POST['qry'];}
			 if(isset($_POST['valueField'])){ $valueField = $_POST['valueField'];}
			 if(isset($_POST['textField'])){ $textField = $_POST['textField'];}
			 if(isset($_POST['nombreCampo'])){ $nombreCampo = $_POST['nombreCampo'];}
			 
			 			 
			 if(isset($catalogo['catalogoID'])){	
				 $queryPermiso = "SELECT Acciones.accionID, Acciones.nombreAccion, Formularios.nombreFormulario FROM Acciones inner join AccionesFormularios on Acciones.accionID=AccionesFormularios.accionID inner join Formularios on AccionesFormularios.formularioID=Formularios.formularioID inner join PerfilAccionFormulario on AccionesFormularios.accionFormularioID =PerfilAccionFormulario.accionFormularioID inner join PerfilesCompuestos on PerfilAccionFormulario.perfilID=PerfilesCompuestos.perfilID inner join Usuarios on Usuarios.usuarioID = PerfilesCompuestos.usuarioID where Formularios.catalogoID='".$catalogo['catalogoID']."' and AccionesFormularios.estadoAccion=1 and AccionesFormularios.regEstado=1  and PerfilAccionFormulario.regEstado=1 and PerfilAccionFormulario.activoPerfilAccionFormulario=1  and Usuarios.usuarioID='".Yii::$app->user->identity->usuarioID."' and Acciones.accionID='3' group by AccionesFormularios.accionFormularioID";
				 
				 $permiso = Yii::$app->db->createCommand($queryPermiso)->queryOne();
				 if(isset($permiso['accionID'])){
				 
				 
					$campos = Yii::$app->db->createCommand("SELECT * FROM Campos where regEstado='1' and catalogoID='".$catalogo['catalogoID']."' order by orden ASC")->queryAll();
				 	
				 	$modelName = '\app\\models\\'.$catalogo['nombreModelo'];
				 	$model =  new $modelName;
				 
				 	$urlRedirect = Url::to([strtolower($catalogo['nombreModelo']).'/createform']);
				 	$urlSelect = Url::to([strtolower($catalogo['nombreModelo']).'/getselect']);
				 
				 	$idForm = 'form-'.strtolower($catalogo['nombreModelo']);
				 	echo '<div class="alert alert-success" role="alert" style="display:none;" id="formalerttrue">
						 <i class="fa fa-check-square-o" aria-hidden="true"></i>  '.Yii::$app->globals->getTraductor(8, Yii::$app->session['idiomaId'], 'Registro actualizado con exito').'!
					 </div>';
				 
				 	echo '<div class="alert alert-danger" role="alert" style="display:none;" id="formalertfalse">
						 <i class="fa fa-check-square-o" aria-hidden="true"></i>   '.Yii::$app->globals->getTraductor(13, Yii::$app->session['idiomaId'], 'Ocurrio un error, intenta de nuevo').'!
					 </div>';
				 
					$form = ActiveForm::begin([
						'id' => $idForm,
						'enableClientValidation' => true, 
						'enableAjaxValidation' => false,
						'options'=>[
                               'onsubmit'=>"return false;",/* Disable normal form submit */
                               'onkeypress'=>' if(event.keyCode == 13){ send(\''.$idForm.'\', \''.$urlRedirect.'\', \''.$urlSelect.'\', \''.$varQry.'\', \''.$valueField.'\', \''.$textField.'\', \''.$nombreCampo.'\'); } ' /* Do ajax call when user presses enter key */
                       ]
					]); 
				 
					foreach($campos as $rCampos){
						if($rCampos['visible'] == 1){		
							if($rCampos['tipoControl'] == 'text' or $rCampos['tipoControl'] == 'number' or $rCampos['tipoControl'] == 'date' or $rCampos['tipoControl'] == 'email' or $rCampos['tipoControl'] == 'password' or $rCampos['tipoControl'] == 'color'){
								echo '<div class="col-12 pleft-0">';
								if($rCampos['campoRequerido'] == 1){
									echo $form->field($model, $rCampos['nombreCampo'])->textInput(['value'=>$rCampos['valorDefault'], 'type'=>$rCampos['tipoControl'], 'required'=>'required'])->label(Yii::$app->globals->getTraductor($this->getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo']));
								}else{
									echo $form->field($model, $rCampos['nombreCampo'])->textInput(['value'=>$rCampos['valorDefault'], 'type'=>$rCampos['tipoControl']])->label(Yii::$app->globals->getTraductor($this->getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo']));
								}

								echo '</div>';
							}elseif($rCampos['tipoControl'] == 'float'){
								echo '<div class="col-12 pleft-0">';
								if($rCampos['campoRequerido'] == 1){
									echo $form->field($model, $rCampos['nombreCampo'])->textInput(['value'=>$rCampos['valorDefault'], 'type'=>'number', 'required'=>'required', 'step'=>'any'])->label(Yii::$app->globals->getTraductor($this->getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo']));
								}else{
									echo $form->field($model, $rCampos['nombreCampo'])->textInput(['value'=>$rCampos['valorDefault'], 'type'=>'number', 'step'=>'any'])->label(Yii::$app->globals->getTraductor($this->getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo']));
								}

								echo '</div>';
							}elseif($rCampos['tipoControl'] == 'datetime'){
								echo '<div class="col-12  pleft-0">';
								if($rCampos['campoRequerido'] == 1){
									 echo $form->field($model, $rCampos['nombreCampo'])->widget(DateTimePicker::classname(), [
											'options' => ["autocomplete"=>"off", 'required'=>'required', 'value'=>$rCampos['valorDefault']],
											'pluginOptions' => [
												'autoclose'=>true,
												'format' => 'yyyy-mm-dd H:i',
												'todayHighlight' => true,
											]
									])->label(Yii::$app->globals->getTraductor($this->getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo']));
								}else{
									 echo $form->field($model, $rCampos['nombreCampo'])->widget(DateTimePicker::classname(), [
											'options' => ["autocomplete"=>"off", 'value'=>$rCampos['valorDefault']],
											'pluginOptions' => [
												'autoclose'=>true,
												'format' => 'yyyy-mm-dd H:i',
												'todayHighlight' => true,
											]
									])->label(Yii::$app->globals->getTraductor($this->getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo']));
								}
								echo '</div>';
							}elseif($rCampos['tipoControl'] == 'checkbox'){
								echo '<div class="col-12  pleft-0">';
									if($rCampos['valorDefault'] == '1'){
										echo $form->field($model, $rCampos['nombreCampo'])->checkbox(['checked'=>'checked', 'label' => Yii::$app->globals->getTraductor($this->getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo'])]);
									}else{
										echo $form->field($model, $rCampos['nombreCampo'])->checkbox(['label' => Yii::$app->globals->getTraductor($this->getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo'])]);
									}
								echo '</div>';
							}elseif($rCampos['tipoControl'] == 'textArea'){
								echo '<div class="col-12  pleft-0">';
									echo $form->field($model, $rCampos['nombreCampo'])->textarea(['value'=>$rCampos['valorDefault'], 'rows' => 4])->label(Yii::$app->globals->getTraductor($this->getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo']));
								echo '</div>';
							}elseif($rCampos['tipoControl'] == 'select'){
								echo '<div class="col-12  pleft-0">';	
								
								echo $form->field($model, $rCampos['nombreCampo'])->dropDownList(ArrayHelper::map(Yii::$app->db->createCommand($rCampos['controlQuery'])->queryAll(), $rCampos['valueField'], $rCampos['textField']))->label(Yii::$app->globals->getTraductor($this->getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo']));	
								echo '</div>';
							}elseif($rCampos['tipoControl'] == 'consulta'){
								echo '<div class="col-12  pleft-0">';
								$inputVal = explode(",", $rCampos['valorDefault']);
								$valSel = 0;
								$textSel = 'No definido';
								if(isset($inputVal[0])){$valSel = $inputVal[0];}
								if(isset($inputVal[1])){$textSel = $inputVal[1];}
								
								echo $form->field($model, $rCampos['nombreCampo'])->dropDownList(ArrayHelper::map(Yii::$app->db->createCommand($rCampos['controlQuery'])->queryAll(), $valSel, $textSel))->label(Yii::$app->globals->getTraductor($this->getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo']));
								echo '</div>';
							}elseif($rCampos['tipoControl'] == 'array'){
								$arrayDataCampo = array();
								
								$elementosArray = explode(",", $rCampos['valorDefault']);
								foreach($elementosArray as $rEa){
									$dataArray = explode(":", $rEa);
									if(isset($dataArray[0]) and isset($dataArray[1])){
										$arrayDataCampo[$dataArray[0]] = $dataArray[1];
									}
								}
								
								echo '<div class="col-12  pleft-0">';
								echo $form->field($model, $rCampos['nombreCampo'])->dropDownList($arrayDataCampo)->label(Yii::$app->globals->getTraductor($this->getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo']));
								echo '</div>';
								
							}elseif($rCampos['tipoControl'] == 'Autoincremental'){
								//no imprime el input
							}else{
								echo '<div class="col-12  pleft-0">';
								echo $form->field($model, $rCampos['nombreCampo'])->textInput(['value'=>$rCampos['valorDefault']])->label(Yii::$app->globals->getTraductor($this->getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo']));
								echo '</div>';
							}
						}else{
							echo $form->field($model, $rCampos['nombreCampo'])->textInput(['value'=>$rCampos['valorDefault'], 'type'=>'hidden'])->label(false);
						}
					}
				 
				 echo '<div style="clear: both;"></div>    
					<div class="col-12  pleft-0">
						<div class="form-group">
							<br>
							<button type="button" class="btn btn-success" onclick="send(\''.$idForm.'\', \''.$urlRedirect.'\', \''.$urlSelect.'\', \''.$varQry.'\', \''.$valueField.'\', \''.$textField.'\', \''.$nombreCampo.'\')"><i class="pe-7s-diskette"></i> &nbsp;'.Yii::$app->globals->getTraductor(14, Yii::$app->session['idiomaId'], 'Guardar').'</button>
						</div>
					</div>';
				 
				 ActiveForm::end();
				 exit;
				}else{
					echo '<div class="text-center" style="padding-bottom:40px; padding-top:20px;"> Catálogo no encontrado, solicite a su administrador los permisos para ver el formulario.</div>';
				}
			 }else{
				  echo '<div class="text-center" style="padding-bottom:40px; padding-top:20px;"> Catálogo no encontrado, solicite a su administrador  agregar las configuraciones.</div>';
			 }
		 }else{
			 echo '<div class="text-center" style="padding-bottom:40px; padding-top:20px;"> Catálogo no encontrado, solicite a su administrador  agregar las configuraciones.</div>';
		 }
	 }
    /**
     * Lists all Campos models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CamposSearch();
      

		if(isset($_GET['clear'])){
			Yii::$app->session['CamposSearch'] = '';
			return $this->redirect(['index&token='.$_GET['token']]);				
		}else{
			$params = Yii::$app->request->queryParams;

			if(count($params) <= 1){		  
				if(isset(Yii::$app->session['CamposSearch'])) {
					$params = Yii::$app->session['CamposSearch'];
				}else{
					Yii::$app->session['CamposSearch'] = $params;
				}
			}else{	
				if(isset(Yii::$app->request->queryParams['CamposSearch'])){
					Yii::$app->session['CamposSearch'] = $params;
				}else{
					$params = Yii::$app->session['CamposSearch'];
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
     * Displays a single Campos model.
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
     * Creates a new Campos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Campos();
		
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
				
				$rauditoria = Yii::$app->globals->setRegistro(3, 'Alta', 'Campos', $fID);

				Yii::$app->globals->setRmodifica($rauditoria, 'campoID', $model->campoID);
				Yii::$app->globals->setRmodifica($rauditoria, 'nombreCampo', $model->nombreCampo);
				Yii::$app->globals->setRmodifica($rauditoria, 'tipoControl', $model->tipoControl);
				Yii::$app->globals->setRmodifica($rauditoria, 'visible', $model->visible);
				Yii::$app->globals->setRmodifica($rauditoria, 'catalogoID', $model->catalogoID);
				Yii::$app->globals->setRmodifica($rauditoria, 'textoID', $model->textoID);
				
				return $this->redirect(['create', 'insert' => 'true', 'id' => $model->campoID]);
			}
		}
		

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Campos model.
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
				Yii::$app->db->createCommand('UPDATE CamposGrid SET textoID="'.$model->textoID.'" WHERE nombreCampo="'.$model->nombreCampo.'" and catalogoID="'.$model->catalogoID.'"')->query();
				
				$rauditoria = Yii::$app->globals->setRegistro(5, 'Editar', 'Campos', $fID);

				Yii::$app->globals->setRmodifica($rauditoria, 'campoID', $model->campoID);
				Yii::$app->globals->setRmodifica($rauditoria, 'nombreCampo', $model->nombreCampo);
				Yii::$app->globals->setRmodifica($rauditoria, 'tipoControl', $model->tipoControl);
				Yii::$app->globals->setRmodifica($rauditoria, 'visible', $model->visible);
				Yii::$app->globals->setRmodifica($rauditoria, 'catalogoID', $model->catalogoID);
				Yii::$app->globals->setRmodifica($rauditoria, 'textoID', $model->textoID);
				
				return $this->redirect(['update', 'id' => $model->campoID, 'token'=>$_GET['token'], 'update'=>'true']);
			}
		}


        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Campos model.
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
					 $rauditoria = Yii::$app->globals->setRegistro(4, 'Eliminar', 'Campos', $fID);
					 Yii::$app->globals->setRmodifica($rauditoria, 'campoID', $model->campoID);
					 
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
     * Finds the Campos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Campos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Campos::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
	
	function getNameInput($id){
		if($id == 1){
			return 0;
		}else{
			return $id;
		}
	}
}
