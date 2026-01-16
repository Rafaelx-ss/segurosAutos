<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use yii\bootstrap4\ActiveForm;

use app\assets\AppAsset;
$asset = AppAsset::register($this);
$baseUrl = $asset->baseUrl;
$this->registerJsFile($baseUrl.'/require/js/jquery/jquery-2.1.4.js', ['position' => \yii\web\View::POS_HEAD]);
?>

<div class="app-page-title">
	<div class="page-title-wrapper">
    	<div class="page-title-heading">
        	<div class="page-title-icon">
            	<i class="pe-7s-server icon-gradient bg-malibu-beach"></i>
            </div>
            <div>
				Catalogos				<div class="page-title-subheading">Generar modelos</div>
            </div>
        </div>
        <div class="page-title-actions">
			<?php 	
			$iconAcciones = Yii::$app->db->createCommand("SELECT * FROM Acciones where regEstado='1'")->queryAll();
			$iAdd = "fa fa-plus";
			$iSeacrh = "fa fa-search";
			$iDelete = "fa fa-trash";
			$iExcel = "fa fa-file-excel";
			$iPdf = "fa fa-file-pdf";
			
			foreach($iconAcciones as $rAccion){
				if($rAccion['accionID'] == '2'){
					$iSeacrh = $rAccion['imagen'];
				}
			
				if($rAccion['accionID'] == '3'){
					$iAdd = $rAccion['imagen'];
				}
			
				if($rAccion['accionID'] == '4'){
					$iDelete = $rAccion['imagen'];
				}
			
				if($rAccion['accionID'] == '6'){
					$iExcel = $rAccion['imagen'];
				}
			
				if($rAccion['accionID'] == '7'){
					$iPdf = $rAccion['imagen'];
				}
			}
			
			
					echo Html::a('<i class="'.$iSeacrh.'"></i> Consulta', $url = ['catalogos/index'], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
			
			
					echo Html::a('<i class="'.$iAdd.'" aria-hidden="true"></i> Alta', $url = ['catalogos/create'], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);			
					
			
					echo  Html::button('<i class="'.$iDelete.'" aria-hidden="true"></i> Eliminar', ['class' => 'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones(), 'onclick' => 'getRows()']);
			
			
					echo Html::a('<i class="fa fa-cogs" aria-hidden="true"></i> Modelos', $url = ['catalogos/modelo'], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);	
			
					echo Html::a('<i class="fa fa-folder-open" aria-hidden="true"></i> CRUD', $url = ['catalogos/crud'], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);	
			?>
		</div>
     </div>
</div>

<div class="main-card mb-3 card">
      <div class="card-body">	
		  <?php
			echo '<div id="cargando" style="display:none;  color: green; font-size:12px; text-align: center;">
								<img src="'.$baseUrl.'/require/img/loader.gif" alt="cargando"  style="width: 190px;" /> 
								<h4>No cierre ni refresque la pagina hasta finalizar el proceso</h4><br>
							</div>';
						if(isset($_GET['update'])){
							if($_GET['update'] == 'true'){
								echo '<div class="alert alert-success" role="alert">
										  Registro actualizado con exito!
									  </div>';
							}else{
								echo '<div class="alert alert-danger" role="alert">
									  	No pudimos actualizar el registro, intenta de nuevo!
									 </div>';
							}
						}	
		  
		  $modelQry = Yii::$app->db->createCommand('SELECT * FROM Catalogos where regEstado="1" and activoCatalogo="1" order by nombreModelo ASC')->queryAll();
		  
		  if(count($modelQry) == 0){
			  echo '<div style="padding-top:30px; padding-bottom:30px; text-align:center;">
									El proyecto no cuenta con modelos, verifique la configuración.
							</div>';
		  }else{
			  				if(isset($_POST['generar'])){
								
								$numMod = 1;
								foreach($modelQry as $row){
									if(isset($_POST['model_'.$row['nombreModelo']])){
										echo $numMod.".- ";
										Yii::$app->globals->createModels($row['nombreModelo'], $row['nombreCatalogo']);
										$numMod = 1;
									}
									//break;
								}
							}else{
								echo "<br><strong style='font-size:18px;'>Catálogos ( <input type='checkbox' checked id='select_all' /> seleccionar todos)</strong><br>";
								echo '<div class="form-group" style="margin-top: 1px; ">';
								$form = ActiveForm::begin([
											'id' => 'form',
											'action' => ['catalogos/modelo'],
											'options' => ['method' => 'POST', 'onsubmit' => 'cargando()'],				
										]);
								$numMod = 1;
								$arrayDeselect = array('Catalogos', 'Textos', 'Usuariosapi');
								foreach($modelQry as $row){
									if (!in_array($row['nombreModelo'], $arrayDeselect)) {
										echo '<input type="checkbox" id="model_'.$row['nombreModelo'].'" name="model_'.$row['nombreModelo'].'" checked> '.$numMod.'.- '.$row['nombreModelo'];
										echo "<br>";
										$numMod++;
									}
								}
								echo '<input type="hidden" name="generar" value="ok" />';

								echo '</div>
											  <div class="form-group" style="margin-top: 1px; ">
													<button type="submit" class="btn btn-info " id="btn_export">Iniciar creación de modelos</button>
											  </div>';
								ActiveForm::end();
							}
		  }
		 ?>
 	</div>
</div>
<script type="text/javascript">
	function cargando(){
		document.getElementById('cargando').style.display = 'block';
		document.getElementById('btn_export').disabled = true;
		
		 startday=new Date();
		clockStart=startday.getTime();
		 
		window.setTimeout('getSecs()',1);
	 }
	
	$('#select_all').click(function() {
	  var c = this.checked;
	  $(':checkbox').prop('checked', c);
	});
</script>

