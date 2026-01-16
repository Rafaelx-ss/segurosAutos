<?php
ini_set('memory_limit', '-1');
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use yii\data\SqlDataProvider;
use app\assets\AppAsset;
//use yii\data\ActiveDataProvider;

use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;

$idForm = '';
if(isset($_GET['f'])){
	$idForm = $_GET['f'];
}

$frmSeguridad = '';
if(isset($_GET['r'])){$frmSeguridad = explode("/", $_GET['r']);}


$permisosBtn = Yii::$app->db->createCommand("SELECT Acciones.accionID, Acciones.nombreAccion, Formularios.nombreFormulario, Formularios.icono,  Formularios.textoID, Formularios.tipoFormularioID, Formularios.urlArchivo, Acciones.imagen FROM Acciones
inner join AccionesFormularios on Acciones.accionID=AccionesFormularios.accionID 
inner join Formularios on AccionesFormularios.formularioID=Formularios.formularioID
inner join PerfilAccionFormulario on AccionesFormularios.accionFormularioID =PerfilAccionFormulario.accionFormularioID
inner join PerfilesCompuestos on PerfilAccionFormulario.perfilID=PerfilesCompuestos.perfilID
inner join Usuarios on Usuarios.usuarioID = PerfilesCompuestos.usuarioID
where md5(Formularios.formularioID)='".$idForm."' and AccionesFormularios.estadoAccion=1 and AccionesFormularios.regEstado=1 and PerfilAccionFormulario.regEstado=1 and PerfilAccionFormulario.activoPerfilAccionFormulario=1 and Usuarios.usuarioID='".Yii::$app->user->identity->usuarioID."' group by AccionesFormularios.accionFormularioID")->queryAll();


$perCons = 0;
$perAlta = 0;
$perElim = 0;
$perEdit = false;
$perElimckh = false;
$perExcel = 0;
$perPdf = 0;
$txtID = 1;
$tituloReporte = "Reporte";
$iconForm = 'pe-7s-lock';

$iAdd = "fa fa-plus";
$iSeacrh = "fa fa-search";
$iDelete = "fa fa-trash";
$iExcel = "fa fa-file-excel";
$iPdf = "fa fa-file-pdf";

foreach($permisosBtn as $dataPbtn){
	$urlSeguridad = explode("/", $dataPbtn['urlArchivo']);	
	if(isset($urlSeguridad[0]) and isset($frmSeguridad[0])){		
		if($frmSeguridad[0] == $urlSeguridad[0]){			
			if(isset($dataPbtn['accionID'])){	
					if($dataPbtn['accionID'] == '2'){ $perCons = 1; $iSeacrh = $dataPbtn['imagen'];}	
					if($dataPbtn['accionID'] == '3'){ $perAlta = 1; $iAdd = $dataPbtn['imagen'];}	
					if($dataPbtn['accionID'] == '4'){ $perElim = 1; $perElimckh = true; $iDelete = $dataPbtn['imagen'];}
					if($dataPbtn['accionID'] == '5'){ $perEdit = true; }
					if($dataPbtn['accionID'] == '6'){ $perExcel = 1; $iExcel = $dataPbtn['imagen'];}
					if($dataPbtn['accionID'] == '7'){ $perPdf = 1; $iPdf = $dataPbtn['imagen'];}
					$txtID = $dataPbtn['textoID'];
					$iconForm = $dataPbtn['icono'];
					$tituloReporte = $dataPbtn['nombreFormulario'];
				}
			}
	}						
}

$qryReporte = "";
$qryCount = "";
$arrayCampos = array();
$arrayfCampos = array();
$arrayPost = array();
$cadQry = "";

$errorPermisos = "false";


$asset = AppAsset::register($this);
$baseUrl = $asset->baseUrl;

$this->registerCssFile('https://cdn3.devexpress.com/jslib/21.2.5/css/dx.light.css', ['position' => \yii\web\View::POS_HEAD]);

$this->registerJsFile($baseUrl.'/require/js/jquery/jquery-2.1.4.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile($baseUrl.'/require/dvtablas/js/cldr.min.js', ['position' => \yii\web\View::POS_END]);
$this->registerJsFile($baseUrl.'/require/dvtablas/js/event.min.js', ['position' => \yii\web\View::POS_END]);
$this->registerJsFile($baseUrl.'/require/dvtablas/js/supplemental.min.js', ['position' => \yii\web\View::POS_END]);
$this->registerJsFile($baseUrl.'/require/dvtablas/js/unresolved.min.js', ['position' => \yii\web\View::POS_END]);
$this->registerJsFile($baseUrl.'/require/dvtablas/js/dx.all.js', ['position' => \yii\web\View::POS_END]);


$this->registerJsFile($baseUrl.'/require/dvtablas/js/jspdf.umd.min.js', ['position' => \yii\web\View::POS_END]);
$this->registerJsFile($baseUrl.'/require/dvtablas/js/jspdf.plugin.autotable.min.js', ['position' => \yii\web\View::POS_END]);

//$this->registerJsFile($baseUrl.'/require/dvtablas/js/polyfill.min.js', ['position' => \yii\web\View::POS_END]);
$this->registerJsFile($baseUrl.'/require/dvtablas/js/exceljs.min.js', ['position' => \yii\web\View::POS_END]);
$this->registerJsFile($baseUrl.'/require/dvtablas/js/FileSaver.min.js', ['position' => \yii\web\View::POS_END]);




if($perExcel==1 and $perPdf==1){
	$reporte = Yii::$app->db->createCommand("SELECT * FROM ReportesConfiguraciones where md5(reporteConfiguracionID)='".$_GET['id']."'")->queryOne();
	
	if(isset($reporte['reporteConfiguracionID'])){		
		$qryRep = str_replace("FROM", "from", $reporte['queryReporte']);
		
		$qcount = explode("from", $qryRep);
		if(isset($qcount[1])){
			
			//echo "SELECT * FROM ReportesCampos where reporteConfiguracionID='".$reporte['reporteConfiguracionID']."'  order by orden";
			
			$rCampos = Yii::$app->db->createCommand("SELECT * FROM ReportesCampos where reporteConfiguracionID='".$reporte['reporteConfiguracionID']."'  order by orden")->queryAll();
			
			$adnCont = 1;
			foreach($rCampos as $rowCampos){
				
				if($rowCampos['searchVisible'] == 1){
					$arrayfCampos[] = array('tipoControl'=>$rowCampos['tipoControl'], 'nombreCampo'=>$rowCampos['nombreCampo'], 'traduccion'=>Yii::$app->globals->getTraductor($rowCampos['textoID'], Yii::$app->session['idiomaId'], $rowCampos['nombreCampo']), 'controlQuery'=>$rowCampos['controlQuery'], 'queryValor'=>$rowCampos['queryValor']);
					
					if($rowCampos['tipoControl'] == 'date' or $rowCampos['tipoControl'] == 'datetime'){
						//if(isset($_GET[$rowCampos['nombreCampo']."_ini"]) and isset($_GET[$rowCampos['nombreCampo']."_fin"])){
							
						if(isset($_GET[$rowCampos['nombreCampo']."_ini"]) and !empty($_GET[$rowCampos['nombreCampo']."_ini"]) and !is_null($_GET[$rowCampos['nombreCampo']."_ini"]) and isset($_GET[$rowCampos['nombreCampo']."_fin"]) and !empty($_GET[$rowCampos['nombreCampo']."_fin"]) and !is_null($_GET[$rowCampos['nombreCampo']."_fin"])){
							
							if($_GET[$rowCampos['nombreCampo']."_ini"] != ''){
								$arrayPost[$rowCampos['nombreCampo']."_ini"] = $_GET[$rowCampos['nombreCampo']."_ini"];
							}
							
							if($_GET[$rowCampos['nombreCampo']."_fin"] != ''){
								$arrayPost[$rowCampos['nombreCampo']."_fin"] = $_GET[$rowCampos['nombreCampo']."_fin"];
							}
							
							if($rowCampos['aliasTabla'] != ''){
								$aliast = $rowCampos['aliasTabla'].".";
							}else{
								$aliast = "";
							}
							
							if($adnCont == 1){								
								$cadQry .= $aliast.$rowCampos['nombreCampo']." >= '".$_GET[$rowCampos['nombreCampo']."_ini"]."' and ".$aliast.$rowCampos['nombreCampo']." <= '".$_GET[$rowCampos['nombreCampo']."_fin"]."'";
								$adnCont++;
							}else{
								//echo $adnCont."<br>";
								$cadQry .= " and  ".$aliast.$rowCampos['nombreCampo']." >= '".$_GET[$rowCampos['nombreCampo']."_ini"]."' and ".$aliast.$rowCampos['nombreCampo']." <= '".$_GET[$rowCampos['nombreCampo']."_fin"]."'";
								$adnCont++;
							}
							
						}
						
						
					}else if($rowCampos['tipoControl'] == 'select' or $rowCampos['tipoControl'] == 'array'){
						if(isset($_GET[$rowCampos['nombreCampo']]) and !empty($_GET[$rowCampos['nombreCampo']]) and !is_null($_GET[$rowCampos['nombreCampo']])){
							
								$arrayPost[$rowCampos['nombreCampo']] = $_GET[$rowCampos['nombreCampo']];
								
								if($rowCampos['aliasTabla'] != ''){
									$aliast = $rowCampos['aliasTabla'].".";
								}else{
									$aliast = "";
								}
								
								if($adnCont == 1){
									$cadQry .= $aliast.$rowCampos['nombreCampo']." = '".$_GET[$rowCampos['nombreCampo']]."' ";
									$adnCont++;
								}else{
									$cadQry .= " and ".$aliast.$rowCampos['nombreCampo']." = '".$_GET[$rowCampos['nombreCampo']]."' ";
									$adnCont++;
								}
								
																				
						}
						
					}else if($rowCampos['tipoControl'] == 'checkbox'){
						if(isset($_GET[$rowCampos['nombreCampo']]) and !empty($_GET[$rowCampos['nombreCampo']]) and !is_null($_GET[$rowCampos['nombreCampo']])){
							
								$arrayPost[$rowCampos['nombreCampo']] = $_GET[$rowCampos['nombreCampo']];
								
								if($rowCampos['aliasTabla'] != ''){
									$aliast = $rowCampos['aliasTabla'].".";
								}else{
									$aliast = "";
								}
								
								if($adnCont == 1){
									$cadQry .= $aliast.$rowCampos['nombreCampo']." = '".$_GET[$rowCampos['nombreCampo']]."' ";
									$adnCont++;
								}else{
									$cadQry .= " and ".$aliast.$rowCampos['nombreCampo']." = '".$_GET[$rowCampos['nombreCampo']]."' ";
									$adnCont++;
								}
								
																				
						}else{
							if($rowCampos['searchVisible'] == 1){
								if($rowCampos['aliasTabla'] != ''){
									$aliast = $rowCampos['aliasTabla'].".";
								}else{
									$aliast = "";
								}
								
								if($adnCont == 1){
									$cadQry .= $aliast.$rowCampos['nombreCampo']." = 0 ";
									$adnCont++;
								}else{
									$cadQry .= " and ".$aliast.$rowCampos['nombreCampo']." = 0 ";
									$adnCont++;
								}
							}
						}
						
					}else{
						if(isset($_GET[$rowCampos['nombreCampo']]) and !empty($_GET[$rowCampos['nombreCampo']]) and !is_null($_GET[$rowCampos['nombreCampo']])){
							
								$arrayPost[$rowCampos['nombreCampo']] = $_GET[$rowCampos['nombreCampo']];
								
								if($rowCampos['aliasTabla'] != ''){
									$aliast = $rowCampos['aliasTabla'].".";
								}else{
									$aliast = "";
								}
								
								if($adnCont == 1){
									$cadQry .= $aliast.$rowCampos['nombreCampo']." LIKE '%".$_GET[$rowCampos['nombreCampo']]."%' ";
									$adnCont++;
								}else{
									$cadQry .= " and ".$aliast.$rowCampos['nombreCampo']." LIKE '%".$_GET[$rowCampos['nombreCampo']]."%' ";
									$adnCont++;
								}
								
						
														
						}
						
					}
					
					
				}
				
				if($rowCampos['visible'] == 1){
					$cvisi = true;
				}else{
					$cvisi = false;
				}
				
				if($rowCampos['sumarCampo'] == 1){
					$arrayCampos[] = array('label'=>Yii::$app->globals->getTraductor($rowCampos['textoID'], Yii::$app->session['idiomaId'], $rowCampos['nombreCampo']), 'attribute'=>$rowCampos['nombreCampo'], 'value'=>$rowCampos['nombreCampo'], 'visible'=>$cvisi, 'suma'=>'Si', 'tipo'=>$rowCampos['tipoControl']);
				}else{
					$arrayCampos[] = array('label'=>Yii::$app->globals->getTraductor($rowCampos['textoID'], Yii::$app->session['idiomaId'], $rowCampos['nombreCampo']), 'attribute'=>$rowCampos['nombreCampo'], 'value'=>$rowCampos['nombreCampo'], 'visible'=>$cvisi, 'suma'=>'No', 'tipo'=>$rowCampos['tipoControl']);
				}
				
				
			}
			//getTotal($model, $count)
			//$dataProvider->models, $rowCampos['nombreCampo']
			
			$qryCounTemp = 0;
			if($cadQry == ''){
				$qryReporte = str_replace("?1", "1", $reporte['queryReporte']);
				
				$qryCounTemp = "Select count(*) FROM ".$qcount[1];	
				$qryCount = str_replace("?1", "1", $qryCounTemp);
				
			}else{
				$qryReporte = str_replace("?1", $cadQry, $reporte['queryReporte']);
				
				$qryCounTemp = "Select count(*) FROM ".$qcount[1];
				$qryCount = str_replace("?1", $cadQry, $qryCounTemp);
			}
			
			
			
		}
		
	}
}else{
	$errorPermisos = "true";
}
?>


<div class="app-page-title">
	<div class="page-title-wrapper">
    	<div class="page-title-heading">
        	<div class="page-title-icon">
            	<i class="<?=  $iconForm ?> icon-gradient bg-malibu-beach"></i>
            </div>
            <div>
				<?=  Yii::$app->globals->getTraductor($txtID, Yii::$app->session['idiomaId'], $tituloReporte); ?>				
				<div class="page-title-subheading"><?= Yii::$app->globals->getTraductor(18, Yii::$app->session['idiomaId'], 'Consulta') ?></div>
            </div>
        </div>
        <div class="page-title-actions">
			
			<?php 	
			$qryClear = "";
			if(isset($_GET['cvacio'])){
				$qryClear = "&cvacio=true";
			}

			$formMenu = Yii::$app->db->createCommand("SELECT * FROM Formularios where md5(formularioID)='".$idForm."'")->queryOne();
								
			if($formMenu['tipoMenu'] == 'Submenu'){
			
			echo '<div style="margin-top: -30px; float: right;">';
			$formSubmenus = Yii::$app->db->createCommand("SELECT * FROM Formularios where formID='".$formMenu['formID']."'")->queryAll();
			foreach($formSubmenus as $rsubMenu){
				echo Html::a('<i class="'.$rsubMenu['icono'].'"></i> '.Yii::$app->globals->getTraductor($rsubMenu['textoID'], Yii::$app->session['idiomaId'], $rsubMenu['nombreFormulario']), $url = [$rsubMenu['urlArchivo'].'&f='.md5($rsubMenu['formularioID'])], $options = ['style'=>'border-top-left-radius: 0; border-top-right-radius: 0;', 'class'=>'btn-shadow btn '.Yii::$app->globals->btnMenu().' mr-3 active']);
			}	
			
			echo '</div>';
			echo '<div style="clear: both;"></div>';
			echo '<div style="margin-top: 20px; float: right;">';
					//Consulta
					
						echo Html::a('<i class="'.$iSeacrh.'"></i> '.Yii::$app->globals->getTraductor(18, Yii::$app->session['idiomaId'], 'Consulta'), $url = ['reportesconfig/reportes&id='.$_GET['id'].'&f='.$idForm], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
					
					//Alta
												
					if($perExcel == 1){
						echo Html::a('<i class="'.$iExcel.'"></i> '.Yii::$app->globals->getTraductor(22, Yii::$app->session['idiomaId'], 'Excel'), $url = ['reportesconfig/getexcel&token='.$_GET['id'].'&f='.$idForm.$qryClear."&data=".urlencode($cadQry)], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
					}
					
					if($perPdf == 1){
						echo Html::a('<i class="'.$iPdf.'"></i> '.Yii::$app->globals->getTraductor(23, Yii::$app->session['idiomaId'], 'PDF'), $url = ['reportesconfig/getpdf&token='.$_GET['id'].'&f='.$idForm.$qryClear."&data=".urlencode($cadQry)], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones(), 'target'=>'_blank']);
					}
			echo '</div>';
			
			}else{
				//Consulta
					//if($perCons == 1){
						echo Html::a('<i class="'.$iSeacrh.'"></i> '.Yii::$app->globals->getTraductor(18, Yii::$app->session['idiomaId'], 'Consulta'), $url = ['reportesconfig/reportes&id='.$_GET['id'].'&f='.$idForm], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
					//}
																
					if($perExcel == 1){
						echo Html::a('<i class="'.$iExcel.'"></i> '.Yii::$app->globals->getTraductor(22, Yii::$app->session['idiomaId'], 'Excel'), $url = ['reportesconfig/getexcel&token='.$_GET['id'].'&f='.$idForm.$qryClear."&data=".urlencode($cadQry)], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
					}
					
					if($perPdf == 1){
						echo Html::a('<i class="'.$iPdf.'"></i> '.Yii::$app->globals->getTraductor(23, Yii::$app->session['idiomaId'], 'PDF'), $url = ['reportesconfig/getpdf&token='.$_GET['id'].'&f='.$idForm.$qryClear."&data=".urlencode($cadQry)], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones(), 'target'=>'_blank']);
					}
			}
			?>			
		</div>
     </div>
</div>

<div class="main-card mb-3 card" >
      <div class="card-body">	
		  <?php
		  if($errorPermisos == 'true'){
			 echo '<div class="alert alert-warning" role="alert">
					 Usted no cuenta con los permisos para ver el reporte, solicite información a su administrador
				 </div>'; 
		  }
		  $totalFcampos = count($arrayfCampos);
		  if($totalFcampos != 0){
				$form = ActiveForm::begin([
					'id' => 'reporte-form',
					'action' => ['reportesconfig/reportes&id='.$_GET['id'].'&f='.$_GET['f']],
					'method' => 'get',
				]);
			  echo "<input type='hidden' name='cvacio' value='true' />";
			  foreach($arrayfCampos as $rFcampos){
				  if($rFcampos['tipoControl'] == 'date'){
					  $valTdini = "";
					  if(isset($arrayPost[$rFcampos['nombreCampo'].'_ini'])){
						  $valTdini = $arrayPost[$rFcampos['nombreCampo'].'_ini'];
					  }
					  echo '<div class="col-3 float-left  pleft-0" style="padding:5px; text-align:left;">
					  			<label>'.$rFcampos['traduccion'].' Inicio</label>
								<input name="'.$rFcampos['nombreCampo'].'_ini" id="'.$rFcampos['nombreCampo'].'_ini" class="form-control input-sm" placeholder="'.$rFcampos['traduccion'].'" type="date" value="'.$valTdini.'">
							</div>';
					  
					  
					  $valTdfin = "";
					  if(isset($arrayPost[$rFcampos['nombreCampo'].'_fin'])){
						  $valTdfin = $arrayPost[$rFcampos['nombreCampo'].'_fin'];
					  }
					  echo '<div class="col-3 float-left  pleft-0" style="padding:5px; text-align:left;">
					  			<label>'.$rFcampos['traduccion'].' Fin</label>
								<input name="'.$rFcampos['nombreCampo'].'_fin" id="'.$rFcampos['nombreCampo'].'_fin" class="form-control input-sm" placeholder="'.$rFcampos['traduccion'].'" type="date" value="'.$valTdfin.'">
							</div>';
					  
				  }elseif($rFcampos['tipoControl'] == 'datetime'){		
						$valTdini = "";
					    if(isset($arrayPost[$rFcampos['nombreCampo'].'_ini'])){
						  	$valTdini = $arrayPost[$rFcampos['nombreCampo'].'_ini'];
						}
					  	echo '<div class="col-3 float-left  pleft-0" style="padding:5px; text-align:left;">';
						echo '<label>'.$rFcampos['traduccion'].' Inicio</label>';
						/*
						echo DateTimePicker::widget([
								'name' => $rFcampos['nombreCampo'].'_ini',
								'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
								'value' => $valTdini,
								'pluginOptions' => [
									'autoclose'=>true,
									'format' => 'yyyy-mm-dd',
									'class' => "form-control input-sm"
								]
							]);
						*/
					   
					  	echo '<input name="'.$rFcampos['nombreCampo'].'_ini" id="'.$rFcampos['nombreCampo'].'_ini" class="form-control input-sm" placeholder="'.$rFcampos['traduccion'].'" type="date" value="'.$valTdini.'">';
					  
					  	$timeInicio = '00:00';
					  	if(isset($_GET['time_ini'])){
							$timeInicio = $_GET['time_ini'];
						}
					  	echo '</div>';
					  	echo '<div class="col-3 float-left  pleft-0" style="padding:5px; text-align:left;">';
						echo '<label>Hora inicio</label>';
					    echo  '<input type="text" class="form-control input-sm" onchange="validateHhMm(this);" value="'.$timeInicio.'" id="time_ini" name="time_ini" />';
						
					   echo '</div>';
					  
					  $valTdfin = "";
					  if(isset($arrayPost[$rFcampos['nombreCampo'].'_fin'])){
						  $valTdfin = $arrayPost[$rFcampos['nombreCampo'].'_fin'];
					  }
					  
					  	$timeFin = '23:59';
					  	if(isset($_GET['time_fin'])){
							$timeFin = $_GET['time_fin'];
						}
					  
					  	echo '<div class="col-3 float-left  pleft-0" style="padding:5px; text-align:left;">';
						echo '<label>'.$rFcampos['traduccion'].' Fin</label>';
					  
					  	/*
						echo DateTimePicker::widget([
								'name' => $rFcampos['nombreCampo'].'_fin',
								'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
								'value' => $valTdfin,
								'pluginOptions' => [
									'autoclose'=>true,
									'format' => 'yyyy-mm-dd',
									'class' => "form-control input-sm"
								]
							]);
						*/
					  	echo '<input name="'.$rFcampos['nombreCampo'].'_fin" id="'.$rFcampos['nombreCampo'].'_fin" class="form-control input-sm" placeholder="'.$rFcampos['traduccion'].'" type="date" value="'.$valTdfin.'">';
					    echo '</div>';
					  	echo '<div class="col-3 float-left  pleft-0" style="padding:5px; text-align:left;">';
						echo '<label>Hora fin</label>';
					    echo  '<input type="text" class="form-control input-sm" onchange="validateHhMm(this);" value="'.$timeFin.'" id="time_fin" name="time_fin" />';
						echo '</div>';
					  
				  }elseif($rFcampos['tipoControl'] == 'checkbox'){
					  if(isset($_GET[$rFcampos['nombreCampo']])){
						  if($_GET[$rFcampos['nombreCampo']] == 1){
							   echo '<input type="checkbox" name="'.$rFcampos['nombreCampo'].'" value="1" checked> <label for="cbox2">'.$rFcampos['traduccion'].'</label>';
						  }else{
							   echo '<input type="checkbox" name="'.$rFcampos['nombreCampo'].'" value="1"> <label for="cbox2">'.$rFcampos['traduccion'].'</label>';
						  }
					  }else{
						  if(isset($_GET['cvacio'])){
							  echo '<input type="checkbox" name="'.$rFcampos['nombreCampo'].'" value="1"> <label for="cbox2">'.$rFcampos['traduccion'].'</label>';
						  }else{
							  echo '<input type="checkbox" name="'.$rFcampos['nombreCampo'].'" value="1" checked> <label for="cbox2">'.$rFcampos['traduccion'].'</label>';
						  }
						  
					  }
					  
				  }elseif($rFcampos['tipoControl'] == 'select'){
					  $valTdata = "";
					  if(isset($arrayPost[$rFcampos['nombreCampo']])){
						  $valTdata = $arrayPost[$rFcampos['nombreCampo']];
					  }
					  //falta este
					  $campQry = Yii::$app->db->createCommand($rFcampos['controlQuery'])->queryAll();
					  echo '<div class="col-3 float-left  pleft-0" style="padding:5px; text-align:left;">';
					  echo '<label>'.$rFcampos['traduccion'].'</label>';
					  echo '<select name="'.$rFcampos['nombreCampo'].'" class="form-control input-sm" >';
					  echo '<option value=""> -- Selecciona -- </option>';
						  foreach($campQry as $rEa){
								$dataArray = explode(",", $rFcampos['queryValor']);
								if(isset($dataArray[0]) and isset($dataArray[1])){
									if($valTdata == $rEa[$dataArray[0]]){
										echo '<option value="'.$rEa[$dataArray[0]].'" selected>'.$rEa[$dataArray[1]].'</option>';
									}else{
										echo '<option value="'.$rEa[$dataArray[0]].'">'.$rEa[$dataArray[1]].'</option>';
									}
									
								}
						  }
					  echo '</select>';
					  echo '</div>';
				  }elseif($rFcampos['tipoControl'] == 'array'){
					  $valTdata = "";
					  if(isset($arrayPost[$rFcampos['nombreCampo']])){
						  $valTdata = $arrayPost[$rFcampos['nombreCampo']];
					  }
					  
					  $elementosArray = explode(",", $rFcampos['queryValor']);
					   echo '<div class="col-3 float-left  pleft-0" style="padding:5px; text-align:left;">';
					  echo '<label>'.$rFcampos['traduccion'].'</label>';
					  echo '<select name="'.$rFcampos['nombreCampo'].'" class="form-control input-sm" >';
					  echo '<option value=""> -- Selecciona -- </option>';
						  foreach($elementosArray as $rEa){
								$dataArray = explode(":", $rEa);
								if(isset($dataArray[0]) and isset($dataArray[1])){
									if($valTdata == $dataArray[0]){
										echo '<option value="'.$dataArray[0].'" selected>'.$dataArray[1].'</option>';
									}else{
										echo '<option value="'.$dataArray[0].'">'.$dataArray[1].'</option>';
									}
									
								}
						  }
					  echo '</select>';
					  echo '</div>';
				  }else{
					  $valTdata = "";
					  if(isset($arrayPost[$rFcampos['nombreCampo']])){
						  $valTdata = $arrayPost[$rFcampos['nombreCampo']];
					  }
					  
					   echo '<div class="col-3 float-left  pleft-0" style="padding:5px; text-align:left;">
					  			<label>'.$rFcampos['traduccion'].'</label>
								<input name="'.$rFcampos['nombreCampo'].'" id="'.$rFcampos['nombreCampo'].'" class="form-control input-sm" placeholder="'.$rFcampos['traduccion'].'" type="text" value="'.$valTdata.'">
							</div>';
						
				  }
			  }
			  echo "<div style='clear:both;'></div><br>";
			  echo '<div class="form-group" style="margin-top: 10px; margin-left: 5px;">
			  			<input type="hidden" value="ok" name="send" />
						<button type="submit" class="btn btn-success">Filtrar</button>
				</div>';
			  	 ActiveForm::end(); 
		  }
		  
		  ?>
	  
		  <?php
	
		//&r=reportesconfig/reportes&id=eccbc87e4b5ce2fe28308fd9f2a7baf3&f=d09bf41544a3365a46c9077ebb5e35c3&cvacio=true&usuarioID=&fechaAcceso_ini=&fechaAcceso_fin=&send=ok
		$numero = count($_GET);
		$tags = array_keys($_GET);
		$valores = array_values($_GET);
		
		$url = "";
		
		for($i=0;$i<$numero;$i++){
			$url .= "&".$tags[$i]."=".$valores[$i];
		}
		
		$url = str_replace("&r=reportesconfig/reportes", "", $url);
		//print_r($url);
	
		  
?>
		  
		  
		  
		  
			
		  
		    <div class="options">
				<!-- <div class="caption">Opciones</div> -->
				<div class="option">
				  <div id="column-lines"></div>
				</div>
				<div class="option">
				  <div id="row-lines"></div>
				</div>
				<div class="option">
				  <div id="show-borders"></div>
				</div>
				<div class="option">
				  <div id="row-alternation"></div>
				</div>
		   </div>
		  <br>
		  
		  <div id="gridContainer"></div>
				<!-- inicia el grid -->	           	
				<!-- finaliza el grid -->	  
		 
 	</div>
</div>


<?php

//print_r($arrayCampos);

function getTotal($model, $count){
	$total = 0;
	foreach($model as $m){
	   //$total += $m->dosis->precioDosis;
		$total += $m[$count];
	}
	
	return $total;
}


$imprimirLogoPdf = $reporte['imprimirLogoPdf'];
$imprimirEncabezado = $reporte['imprimirEncabezado'];
$imprimirFechaHora = $reporte['imprimirFechaHora'];
$reporteName = "reporte_".date('Ymdhis');
$reporteNamePdf = "Reporte";
if($reporte['nombreReporte'] != ""){
	$reporteName = str_replace(" ", "_", $reporte['nombreReporte'])."_".date('Ymd');
	$reporteNamePdf = $reporte['nombreReporte'];
}


$template = Yii::$app->db->createCommand("SELECT * FROM TemplatesReportes where templateReporteID='".$reporte['templateReporteID']."'")->queryOne();

$logoReporte = "";
$fl1 = "";
$fl2 = "";
$fl3 = "";
$encabezadoReporte = "";

if(isset($template['templateReporteID'])){
	if($imprimirLogoPdf == 1){
		$logoReporte = $template['logoTemplateReporte'];
	}
	
	$fl1 = $template['pieTemplateReporteL1'];
	$fl2 = $template['pieTemplateReporteL2'];
	$fl3 = $template['pieTemplateReporteL3'];
	$encabezadoReporte = $template['encabezadoTemplateReporte'];
}

$orientacionPagina = 'l';
if($reporte['orientacionPagina'] == 'Vertical'){
	$orientacionPagina = 'p';
}
//echo $reporte['orientacionPagina'];

$imprimirNombreUsuario = 'p';
if($reporte['imprimirNombreUsuario'] == 1){
	$imprimirNombreUsuario = Yii::$app->user->identity->nombreUsuario;
}

$imprimirPie = $reporte['imprimirPie'];
?>

<script>
<?php
	if(isset($_GET['send'])){
		$tags = array_keys($_GET);
		$valores = array_values($_GET);
	}
?>
//console.log("aqui hay <?php echo Url::to(['reportesconfig/getreport'.$url]); ?>");
$(() => {
  const dataGrid = $('#gridContainer').dxDataGrid({
    dataSource: "<?php echo Url::to(['reportesconfig/getreport'.$url]); ?>",
	showColumnLines: true,
    showRowLines: true,
    rowAlternationEnabled: true,
    showBorders: true,
	sorting: {
      mode: 'multiple',
    },
	filterRow: {
      visible: true,
      applyFilter: 'auto',
    },
	groupPanel: {
      visible: true,
    },
	searchPanel: {
      visible: true,
      width: 240,
      placeholder: 'Search...',
    },
    headerFilter: {
      visible: true,
    },
	paging: {
      pageSize: 10,
    },
	allowColumnReordering: true,
	toolbar: {
      items: [
       'groupPanel',
       'exportButton',
        {
          widget: 'dxButton',
          location: 'after',
          options: {
            icon: 'exportpdf',
            text: 'Exportar PDF',
            onClick() {
              const doc = new jsPDF('<?php echo $orientacionPagina; ?>', 'px', 'letter');
			  	
				<?php
				if($logoReporte != ""){
				?>
					var imgData = '<?php echo getbase64('web/tlogos/'.$logoReporte); ?>';
				<?php
				}
				?>
				
				
				const addHeaders = doc => {
					  const pageCount = doc.internal.getNumberOfPages()
					  
					  doc.setFont('helvetica', 'italic')
					 
					  for (var i = 1; i <= pageCount; i++) {
						doc.setPage(i)
						<?php
						if($logoReporte != ""){
						?>
						doc.addImage(imgData, 'JPEG', 15, 5, 90, 35);
						<?php
						}
						?>
						  //60 izquierda (primer numero es mover a la izquierda)
						  //15 alto (sgundo numero mover )
						  
						<?php
						  if($imprimirFechaHora == 1){
						?>
						   doc.setFontSize(10)
						  doc.text('Fecha: <?php echo date('Y-m-d'); ?>', doc.internal.pageSize.width - 25, 15, {
							  align: 'right',
							  baseline: 'top'
						  })
						 <?php
							}
						  
						  if($imprimirEncabezado == 1){
						?>
						  doc.setFontSize(12);
						  //$reporteNamePdf 
						   doc.text('<?php echo $encabezadoReporte; ?>', doc.internal.pageSize.width / 2, 28, {
							  align: 'center',
							  baseline: 'top'
						  })
						  
						  doc.text('<?php echo $reporteNamePdf; ?>', doc.internal.pageSize.width / 2, 38, {
							  align: 'center',
							  baseline: 'top'
						  })
						<?php
						}
						?>
					  }
				}
				
				const addFooters = doc => {
					  const pageCount = doc.internal.getNumberOfPages()

					  doc.setFont('helvetica', 'italic')
					  
					  for (var i = 1; i <= pageCount; i++) {
						doc.setPage(i)
						
						doc.setFontSize(8)
						doc.text('<?php echo $imprimirNombreUsuario; ?> | Pag. ' + String(i) + '/' + String(pageCount), doc.internal.pageSize.width - 25, 560, {
						   align: 'right',
						   baseline: 'bottom'
						})
						  
						<?php
						if($imprimirPie == 1){
						?>
						doc.text('<?php echo $fl1; ?>', 25, 560, {
						   align: 'left',
						   baseline: 'bottom'
						})
						  
						doc.text('<?php echo $fl2; ?>', 25, 567, {
						   align: 'left',
						   baseline: 'bottom'
						})
						
						doc.text('<?php echo $fl3; ?>', 25, 574, {
						   align: 'left',
						   baseline: 'bottom'
						})
						<?php
						}
						?>  
						
					  }
				}
				
				
					
//var imgData = 'data:image/jpeg;base64,'+ base64;
				
              DevExpress.pdfExporter.exportDataGrid({
                jsPDFDocument: doc,
                component: dataGrid,
				autoTableOptions: {
					margin: { top: 60, left: 25, bottom:50, right:25 },
				}
              }).then(() => {
				
				addHeaders(doc);
				addFooters(doc);
                doc.save('<?php echo $reporteName; ?>.pdf');
              });
            },
          },
        },
        'searchPanel',
      ],
    },
	export: {
      enabled: true,
    },
	onExporting(e) {
      const workbook = new ExcelJS.Workbook();
      const worksheet = workbook.addWorksheet('reporte');

      DevExpress.excelExporter.exportDataGrid({
        component: e.component,
        worksheet,
        autoFilterEnabled: true,
      }).then(() => {
        workbook.xlsx.writeBuffer().then((buffer) => {
          saveAs(new Blob([buffer], { type: 'application/octet-stream' }), '<?php echo $reporteName; ?>.xlsx');
        });
      });
      e.cancel = true;
    },
    columns: [<?php
				// dataType: 'number',
					$dcoma = 1;
					foreach($arrayCampos as $rcampos){
						if($rcampos['visible'] == 1){
							if($dcoma == 1){
								if($rcampos['tipo']=='number' or $rcampos['tipo']=='float'){
									echo "{caption: '".$rcampos['label']."', dataField:'".$rcampos['label']."', dataType: 'number'}";
								}else{
									echo "'".$rcampos['label']."'";
									//echo "{caption: '".$rcampos['label']."', dataField:'".$rcampos['attribute']."', dataType: 'number'}";
								}								
							}else{
								if($rcampos['tipo']=='number' or $rcampos['tipo']=='float'){
									echo ", {caption: '".$rcampos['label']."', dataField:'".$rcampos['label']."', dataType: 'number'}";
								}else{
									echo ", '".$rcampos['label']."'";
								}								
							}
							$dcoma++;
						}
					}
			  ?>],
	
    showBorders: true,
  }).dxDataGrid('instance');
	
  $('#column-lines').dxCheckBox({
    text: 'Ver lineas columnas',
    value: true,
    onValueChanged(data) {
      dataGrid.option('showColumnLines', data.value);
    },
  });

  $('#row-lines').dxCheckBox({
    text: 'Ver lineas fila',
    value: true,
    onValueChanged(data) {
      dataGrid.option('showRowLines', data.value);
    },
  });

  $('#show-borders').dxCheckBox({
    text: 'Ver bordes',
    value: true,
    onValueChanged(data) {
      dataGrid.option('showBorders', data.value);
    },
  });

  $('#row-alternation').dxCheckBox({
    text: 'Alternar color',
    value: true,
    onValueChanged(data) {
      dataGrid.option('rowAlternationEnabled', data.value);
    },
  });	
	
	
});

	
function validateHhMm(inputField) {
    var isValid = /^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$/.test(inputField.value);

    if (isValid) {
      inputField.style.borderColor = '#bfa';
	  
    } else {
      inputField.style.borderColor = '#fba';
	  inputField.setCustomValidity("El formato para la hora es incorrecto 00:00");
    }

    return isValid;
}

	

</script>

<?php
function getbase64($path){
	
	$path =  realpath(__DIR__ . '/../..')."/".$path;
	if(!file_exists($path)) {
		$path = realpath(__DIR__ . '/../..')."/".'web/tlogos/logo_blanco.jpg';
	}
	//if (file_exists($path)) {
		$type = pathinfo($path, PATHINFO_EXTENSION);
		$data = file_get_contents($path);
		$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
	
		return $base64;
	//}else{
	//	return "";
	//}
}

?>