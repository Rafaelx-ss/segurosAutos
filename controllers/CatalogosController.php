<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\models\Catalogos;
use app\models\CatalogosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;



/**
 * CatalogosController implements the CRUD actions for Catalogos model.
 */
class CatalogosController extends Controller
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
                'only' => ['create', 'update', 'delete', 'index', 'modelo', 'crud'],
                'rules' => [
                    [
                        'actions' => [$perAlta, $perEdit, $perdel, $perCons, 'campos', 'getcampos', 'updatecampos', 'modelo', 'crud'],
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
	
	
	public function actionCrud()
    {
        $searchModel = new CatalogosSearch();
      

		if(isset($_GET['clear'])){
			Yii::$app->session['CatalogosSearch'] = '';
			return $this->redirect(['crud']);			
		}else{
			$params = Yii::$app->request->queryParams;

			if(count($params) <= 1){		  
				if(isset(Yii::$app->session['CatalogosSearch'])) {
					$params = Yii::$app->session['CatalogosSearch'];
				}else{
					Yii::$app->session['CatalogosSearch'] = $params;
				}
			}else{	
				if(isset(Yii::$app->request->queryParams['CatalogosSearch'])){
					Yii::$app->session['CatalogosSearch'] = $params;
				}else{
					$params = Yii::$app->session['CatalogosSearch'];
				}		
			}
		}
		
		$dataProvider = $searchModel->search($params);
		$dataProvider->pagination->pageSize = Yii::$app->params['npag'];

        return $this->render('crud', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
	
	
	public function actionModelo()
    {
        $searchModel = new CatalogosSearch();
      

		if(isset($_GET['clear'])){
			Yii::$app->session['CatalogosSearch'] = '';
			return $this->redirect(['modelo']);			
		}else{
			$params = Yii::$app->request->queryParams;

			if(count($params) <= 1){		  
				if(isset(Yii::$app->session['CatalogosSearch'])) {
					$params = Yii::$app->session['CatalogosSearch'];
				}else{
					Yii::$app->session['CatalogosSearch'] = $params;
				}
			}else{	
				if(isset(Yii::$app->request->queryParams['CatalogosSearch'])){
					Yii::$app->session['CatalogosSearch'] = $params;
				}else{
					$params = Yii::$app->session['CatalogosSearch'];
				}		
			}
		}
		
		$dataProvider = $searchModel->search($params);
		$dataProvider->pagination->pageSize = Yii::$app->params['npag'];

        return $this->render('modelo', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Catalogos models.
     * @return mixed
     */
	//actualizar los capos de las tablas
	public function actionUpdatecampos($id){
		$model = $this->findModel($id);
		
		$campos = Yii::$app->db->createCommand("SELECT * FROM Campos WHERE catalogoID ='".$model->catalogoID."'")->queryAll();
		$camposGrdi = Yii::$app->db->createCommand("SELECT * FROM CamposGrid WHERE catalogoID ='".$model->catalogoID."'")->queryAll();
				
		$aCampos = array();
		$arrayNid = array();
		if(count($campos) != 0){
			foreach($campos as $rcampo){
				$aCampos[] = $rcampo['nombreCampo'];
				$arrayNid[$rcampo['nombreCampo']] = $rcampo['campoID'];
			}
		}
				
		$aCamposGrid = array();
		$aCGridNid = array();
		if(count($camposGrdi) != 0){
			foreach($camposGrdi as $rcampoG){
				$aCamposGrid[] = $rcampoG['nombreCampo'];
				$aCGridNid[$rcampoG['nombreCampo']] = $rcampoG['campoGridID'];
			}					
		}
		
		//print_r($aCGridNid);				
		$number = 1;
		$table = Yii::$app->db->getTableSchema($model->nombreCatalogo);	
		$aCamposTable = array();
		$aIdCampos = array();
		$aIdCamposGrid = array();
		foreach($table->columns as $row){
			//echo $row->name;
			if($row->name == 'regEstado' or $row->name == 'regFechaUltimaModificacion' or $row->name == 'regUsuarioUltimaModificacion' or $row->name == 'versionRegistro' or $row->name == 'regVersionUltimaModificacion' or $row->name == 'regFormularioUltimaModificacion'){			//no hacemos nada con los registros		
			}else{
				
				$isPrimaryKey = 0;
				
				if($row->defaultValue != ''){
					$default = $row->defaultValue;
				}

				if($row->isPrimaryKey == '1'){
					$isPrimaryKey = 1;
				}
				$aCamposTable[] = $row->name;	
				
				$default = "";
				$tipoData  = "text";
				if($row->type == "integer"){ $default = '0'; 	$tipoData  = 'number'; }
				if($row->type == "date"){ $default = '0000-00-00'; 	$tipoData  = 'date'; }
				if($row->type == "datetime"){ $default = '0000-00-00 00:00'; 	$tipoData  = 'datetime'; }
				if($row->type == "tinyint" or $row->type == "boolean"){ $default = 1; 	$tipoData  = 'checkbox'; }
				
				if(in_array($row->name, $aCampos)) {
					$insertCampos = "UPDATE Campos SET longitud = '".$row->size."', campoPK = ".$isPrimaryKey.", orden = '".$number."', tipoCampo = '".$row->type."' WHERE campoID = '".$arrayNid[$row->name]."' ";
					$aIdCampos[] = $arrayNid[$row->name];

					Yii::$app->db->createCommand($insertCampos)->query();
				}else{
					$insertCampos = "INSERT INTO Campos(nombreCampo, tipoControl, longitud, campoPK, campoFK, controlQuery, visible, orden, tipoCampo, campoRequerido, textField, valueField, valorDefault, CSS, catalogoID, textoID, catalogoReferenciaID, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) VALUES ('".$row->name."', '".$tipoData."', '".$row->size."', ".$isPrimaryKey.", 0, 'Select', 1,  '".$number."', '".$row->type."', 1, '".$row->name."', '0', '".$default."', 'na', '".$model->catalogoID."', 1, '0', '1', 1, NOW(), '".Yii::$app->user->identity->usuarioID."',  '1', '1')";
					//echo $insertCampos;
					Yii::$app->db->createCommand($insertCampos)->query();
				}
						
				if(in_array($row->name, $aCamposGrid)) {
					$insertCamposGrid = "UPDATE CamposGrid SET orden = '".$number."' WHERE campoGridID = '".$aCGridNid[$row->name]."' ";
					Yii::$app->db->createCommand($insertCamposGrid)->query();
					$aIdCamposGrid[] = $aCGridNid[$row->name];
				}else{
					$insertCamposGrid = "INSERT INTO CamposGrid(nombreCampo, visible, orden, textoID, tipoControl, catalogoID, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion, searchVisible, catalogoReferenciaID) VALUES ('".$row->name."', 1,  '".$number."',  1, '".$tipoData."', '".$model->catalogoID."', '1', 1, NOW(), '".Yii::$app->user->identity->usuarioID."',  '1', '1', 0, 1)";
					//echo $insertCamposGrid;
					Yii::$app->db->createCommand($insertCamposGrid)->query();
				}				
				$number ++;
			}
		}
		
		$difCampos = array_diff($arrayNid, $aIdCampos);
		$difCamposGrid = array_diff($aCGridNid, $aIdCamposGrid);
		
		foreach($difCampos as $del1){
			$delCampos = "DELETE FROM Campos WHERE campoID = '".$del1."'";
			Yii::$app->db->createCommand($delCampos)->query();
		}
		
		foreach($difCamposGrid as $del2){
			$delCamposGrid = "DELETE FROM CamposGrid WHERE campoGridID = '".$del2."'";
			Yii::$app->db->createCommand($delCamposGrid)->query();
		}
		
		return $this->redirect(['update', 'id' => $id, 'update'=>'true']);
	}
	
	
	public function actionGetcampos()
    {
		$echo = ''; 
		if(isset($_POST['idRel'])){
			$catalogo = Yii::$app->db->createCommand("SELECT nombreCatalogo FROM Catalogos where catalogoID='".$_POST['idRel']."'")->queryOne();
			if(isset($catalogo['nombreCatalogo'])){
				$table = Yii::$app->db->getTableSchema($catalogo['nombreCatalogo']);			
				foreach($table->columns as $row){
					if($row->name == 'regEstado' or $row->name == 'regFechaUltimaModificacion' or $row->name == 'regUsuarioUltimaModificacion' or $row->name == 'versionRegistro' or $row->name == 'regVersionUltimaModificacion' or $row->name == 'regFormularioUltimaModificacion'){	}else{
						$echo .= '<option value="'.$row->name.'"> '.$row->name.' </option>';
					}			
				}	
			}else{
				$echo .= '<option value=""> -- Selecciona -- </option>';
			}	
		}else{
			$echo .= '<option value=""> -- Selecciona -- </option>';
		}
		
		echo $echo;
	}
	
	
    public function actionIndex()
    {
        $searchModel = new CatalogosSearch();
      

		if(isset($_GET['clear'])){
			Yii::$app->session['CatalogosSearch'] = '';
			return $this->redirect(['index']);			
		}else{
			$params = Yii::$app->request->queryParams;

			if(count($params) <= 1){		  
				if(isset(Yii::$app->session['CatalogosSearch'])) {
					$params = Yii::$app->session['CatalogosSearch'];
				}else{
					Yii::$app->session['CatalogosSearch'] = $params;
				}
			}else{	
				if(isset(Yii::$app->request->queryParams['CatalogosSearch'])){
					Yii::$app->session['CatalogosSearch'] = $params;
				}else{
					$params = Yii::$app->session['CatalogosSearch'];
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
     * Displays a single Catalogos model.
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
     * Creates a new Catalogos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
	public function actionCampos(){
		if(isset($_GET['id'])){
			$catalogo = Yii::$app->db->createCommand("SELECT * FROM Catalogos WHERE catalogoID ='".$_GET['id']."'")->queryOne();
			if(isset($catalogo['catalogoID'])){
				$campos = Yii::$app->db->createCommand("SELECT * FROM Campos WHERE catalogoID ='".$catalogo['catalogoID']."'")->queryAll();
				$camposGrdi = Yii::$app->db->createCommand("SELECT * FROM CamposGrid WHERE catalogoID ='".$catalogo['catalogoID']."'")->queryAll();
				
				$aCampos = array();
				if(count($campos) != 0){
					foreach($campos as $rcampo){
						$aCampos[] = $rcampo['nombreCampo'];
					}
				}
				
				$aCamposGrid = array();
				if(count($camposGrdi) != 0){
					foreach($campos as $rcampoG){
						$aCamposGrid[] = $rcampoG['nombreCampo'];
					}					
				}
				
				
				$campos = Yii::$app->db->createCommand("UPDATE Campos set regEstado=0 WHERE catalogoID ='".$catalogo['catalogoID']."'")->query();
				$camposGrdi = Yii::$app->db->createCommand("UPDATE CamposGrid set regEstado=0 WHERE catalogoID ='".$catalogo['catalogoID']."'")->query();
								
				$table = Yii::$app->db->getTableSchema($model->nombreCatalogo);			
				foreach($table->columns as $row){
					$aTable[] = $row->name;
				}
				
				
				
				
			}else{
				echo 'false';
			}
		}else{
			echo 'false';
		}
	}
	
	
	
	public function actionCreate()
    {
        $model = new Catalogos();

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
				$number = 1;
				$table = Yii::$app->db->getTableSchema($model->nombreCatalogo);			
				foreach($table->columns as $row){
					//echo $row->name;
					if($row->name == 'regEstado' or $row->name == 'regFechaUltimaModificacion' or $row->name == 'regUsuarioUltimaModificacion' or $row->name == 'versionRegistro' or $row->name == 'regVersionUltimaModificacion' or $row->name == 'regFormularioUltimaModificacion'){
						
					}else{
						
						$isPrimaryKey = 0;
						if($row->defaultValue != ''){
							$default = $row->defaultValue;
						}

						if($row->isPrimaryKey == '1'){
							$isPrimaryKey = 1;
						}
						
						$default = "";
						$tipoData  = 'text';
						if($row->type == "integer"){ $default = '0'; 	$tipoData  = 'number'; }
						if($row->type == "date"){ $default = '0000-00-00'; 	$tipoData  = 'date'; }
						if($row->type == "datetime"){ $default = '0000-00-00 00:00'; 	$tipoData  = 'datetime'; }
						if($row->type == "tinyint" or $row->type == "boolean"){ $default = 1; 	$tipoData  = 'checkbox'; }
						
						
						$insertCampos = "INSERT INTO Campos(nombreCampo, tipoControl, longitud, campoPK, campoFK, controlQuery, visible, orden, tipoCampo, campoRequerido, textField, valueField, valorDefault, CSS, catalogoID, textoID, catalogoReferenciaID, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) VALUES ('".$row->name."', '".$tipoData."', '".$row->size."', ".$isPrimaryKey.", 0, 'Select', 1,  '".$number."', '".$row->type."', 1, '".$row->name."', '0', '".$default."', 'na', '".$model->catalogoID."', 1, '0', '1', 1, NOW(), '".Yii::$app->user->identity->usuarioID."',  '1', '1')";

						Yii::$app->db->createCommand($insertCampos)->query();

						$insertCamposGrid = "INSERT INTO CamposGrid(nombreCampo, visible, orden, textoID, tipoControl, catalogoID, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion, searchVisible, catalogoReferenciaID) VALUES ('".$row->name."', 1,  '".$number."',  1, '".$tipoData."', '".$model->catalogoID."', '1', 1, NOW(), '".Yii::$app->user->identity->usuarioID."',  '1', '1', 0, 1)";

						Yii::$app->db->createCommand($insertCamposGrid)->query();
						$number ++;
					}
				}
			
				
				$formInser = "INSERT INTO Formularios (tipoMenu, formID, nombreFormulario, urlArchivo, estadoFormulario, orden, icono, menuID, aplicacionID, catalogoID, textoID, tipoFormularioID, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion)VALUES('Directo', '0', '".$_POST['nombreFormulario']."', '".strtolower($model->nombreModelo)."/index', 1, 0, 'pe-7s-help1', '".$_POST['sel_menu']."', 1, '".$model->catalogoID."', '".$_POST['sel_texto']."', '1', 1, 1, NOW(), '".Yii::$app->user->identity->usuarioID."', 1, '".Yii::$app->globals->getVersion()."')";
				Yii::$app->db->createCommand($formInser)->query();
				$idFormulario = Yii::$app->db->getLastInsertID();
				
				$InsFormMen = "INSERT INTO MenusFormularios (formularioID, menuID, ordenMenuFormulario, activoMenusForumlarios, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion) VALUES ('".$idFormulario."', '".$_POST['sel_menu']."', '".$_POST['ordenFormulario']."', 1, 1, 1, NOW(), '".Yii::$app->user->identity->usuarioID."', 1, '".Yii::$app->globals->getVersion()."')";
				Yii::$app->db->createCommand($InsFormMen)->query();
				
				if(isset($_POST['pcon'])){
					if($_POST['pcon'] == 'ok'){
						$insActCon = "INSERT INTO AccionesFormularios(claveAccion, estadoAccion, accionID, formularioID, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion)VALUES('".$model->nombreCatalogo."-Consulta', 1, 2, '".$idFormulario."', 1, 1, NOW(), '".Yii::$app->user->identity->usuarioID."', 1, '".Yii::$app->globals->getVersion()."')";
						Yii::$app->db->createCommand($insActCon)->query();
					}
				}
				
				if(isset($_POST['palta'])){
					if($_POST['palta'] == 'ok'){
						$insActa = "INSERT INTO AccionesFormularios(claveAccion, estadoAccion, accionID, formularioID, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion)VALUES('".$model->nombreCatalogo."-Alta', 1, 3, '".$idFormulario."', 1, 1, NOW(), '".Yii::$app->user->identity->usuarioID."', 1, '".Yii::$app->globals->getVersion()."')";
						Yii::$app->db->createCommand($insActa)->query();
					}
				}
				
				if(isset($_POST['pdel'])){
					if($_POST['pdel'] == 'ok'){
						$insActdel= "INSERT INTO AccionesFormularios(claveAccion, estadoAccion, accionID, formularioID, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion)VALUES('".$model->nombreCatalogo."-Eliminar', 1, 4, '".$idFormulario."', 1, 1, NOW(), '".Yii::$app->user->identity->usuarioID."', 1, '".Yii::$app->globals->getVersion()."')";
						Yii::$app->db->createCommand($insActdel)->query();
					}
				}
				
				if(isset($_POST['pedit'])){
					if($_POST['pedit'] == 'ok'){
						$insActedit = "INSERT INTO AccionesFormularios(claveAccion, estadoAccion, accionID, formularioID, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion)VALUES('".$model->nombreCatalogo."-Editar', 1, 5, '".$idFormulario."', 1, 1, NOW(), '".Yii::$app->user->identity->usuarioID."', 1, '".Yii::$app->globals->getVersion()."')";
						Yii::$app->db->createCommand($insActedit)->query();
					}
				}
				
				if(isset($_POST['pexcel'])){
					if($_POST['pexcel'] == 'ok'){
						$insActex = "INSERT INTO AccionesFormularios(claveAccion, estadoAccion, accionID, formularioID, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion)VALUES('".$model->nombreCatalogo."-Excel', 1, 6, '".$idFormulario."', 1, 1, NOW(), '".Yii::$app->user->identity->usuarioID."', 1, '".Yii::$app->globals->getVersion()."')";
						Yii::$app->db->createCommand($insActex)->query();
					}
				}
				
				if(isset($_POST['ppdf'])){
					if($_POST['ppdf'] == 'ok'){
						$insActPdf = "INSERT INTO AccionesFormularios(claveAccion, estadoAccion, accionID, formularioID, versionRegistro, regEstado, regFechaUltimaModificacion, regUsuarioUltimaModificacion, regFormularioUltimaModificacion, regVersionUltimaModificacion)VALUES('".$model->nombreCatalogo."-PDF', 1, 7, '".$idFormulario."', 1, 1, NOW(), '".Yii::$app->user->identity->usuarioID."', 1, '".Yii::$app->globals->getVersion()."')";
						Yii::$app->db->createCommand($insActPdf)->query();
					}
				}
				
				
				
				$rauditoria = Yii::$app->globals->setRegistro(3, 'Alta', 'Catalogos', $fID);

				Yii::$app->globals->setRmodifica($rauditoria, 'catalogoID', $model->catalogoID);
				Yii::$app->globals->setRmodifica($rauditoria, 'nombreCatalogo', $model->nombreCatalogo);
				Yii::$app->globals->setRmodifica($rauditoria, 'activoCatalogo', $model->activoCatalogo);
				
				
				return $this->redirect(['create', 'insert' => 'true', 'id' => $model->catalogoID]);
			}	
		}
		

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Catalogos model.
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
				
				$rauditoria = Yii::$app->globals->setRegistro(5, 'Editar', 'Catalogos', $fID);
				
				Yii::$app->globals->setRmodifica($rauditoria, 'catalogoID', $model->catalogoID);
				Yii::$app->globals->setRmodifica($rauditoria, 'nombreCatalogo', $model->nombreCatalogo);
				Yii::$app->globals->setRmodifica($rauditoria, 'activoCatalogo', $model->activoCatalogo);
				
				return $this->redirect(['update', 'id' => $model->catalogoID, 'update'=>'true']);
			}
		}

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Catalogos model.
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
				$model->regEstado = '0';	
				
				 if($model->save()){
					 
					 $rauditoria = Yii::$app->globals->setRegistro(4, 'Eliminar', 'Catalogos', $fID);
					 Yii::$app->globals->setRmodifica($rauditoria, 'catalogoID', $model->catalogoID);
					 
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
     * Finds the Catalogos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Catalogos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Catalogos::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
