<?php
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\assets\AppAsset;

$asset = AppAsset::register($this);
$baseUrl = $asset->baseUrl;
$this->registerJsFile($baseUrl.'/require/js/jquery/jquery-2.1.4.js', ['position' => \yii\web\View::POS_HEAD]);

$url_padre = realpath(dirname(__FILE__).'/../../../');
$url_proyecto = realpath(Yii::$app->basePath).'/views';
//echo $url_proyecto;

if(isset($_POST['urlinicial']) and isset($_POST['urldestino'])){
	if($_POST['urlinicial'] != '' and $_POST['urldestino'] != ''){
		$url_padre = $_POST['urldestino'];
		$url_proyecto = $_POST['urlinicial'];
	}		
}
?>

<div class="app-page-title">
	<div class="page-title-wrapper">
    	<div class="page-title-heading">
        	<div class="page-title-icon">
            	<i class="pe-7s-albums icon-gradient bg-malibu-beach"></i>
            </div>
            <div>
				Exportar vistas, controladores y modelos.			
				<div class="page-title-subheading"></div>
            </div>
        </div>
        <div class="page-title-actions">			
		</div>
     </div>
</div>

<div class="main-card mb-3 card">
	<div class="card-body">	
		<div style="clear: both;"></div>
			<?php
			$form = ActiveForm::begin([
				'id' => 'login-form',
				'action' => ['site/exportfiles'],
				'options' => ['method' => 'POST', 'onsubmit' => 'cargando()'],				
			]);
			?>
			<div class="form-group" style="margin-right: 20px;">
				<label>Url Inicial:</label> &nbsp;&nbsp;&nbsp;
				<input name="urlinicial" required class="form-control input-sm" type="text" value="<?php echo $url_proyecto; ?>" />				
			</div>			
			
			<div class="form-group" style="margin-right: 20px;">
				<label>Url Destino:</label> &nbsp;&nbsp;&nbsp;
				<input name="urldestino" required class="form-control input-sm" type="text"  value="<?php echo $url_padre; ?>" />				
			</div>
			<div style="clear: both;"></div>
			
			<div class="form-group" style="margin-top: 1px; margin-left: 5px;">
					<button type="submit" class="btn btn-success " id="btn_export">Enviar datos</button>
			</div>
			
			<?php ActiveForm::end() ?>
		
		<div style="clear: both;"></div>
		<hr>
		
		<?php	
		echo '<div id="cargando" style="display:none;  color: green; font-size:12px; text-align: center;">
				<img src="'.$baseUrl.'/require/images/cloud-upload.gif" alt="cargando"  style="width: 380px;" /> 
				<input class="timepage" size="5" id="timespent" name="timespent" readonly style="text-align:center;width:200px;font-size:40px;border:1px solid #56aaf3;padding:6px;margin:12px 0 12px 0;">
				<h4>No cierre ni refresque la pagina hasta finalizar el proceso</h4><br>
			</div>';
		
		
		if(isset($_POST['urlinicial']) and isset($_POST['urldestino'])){
			$rutaInicial = $_POST['urlinicial'];
			$rutaFinal = $_POST['urldestino'];
			if($rutaInicial != '' and $rutaFinal != ''){
				
				$datosFile = array();
 				if(file_exists($rutaInicial)) {
					if(file_exists($rutaFinal)) {
						if(is_dir($rutaInicial)){	
							if($dh = opendir($rutaInicial)){
								$num = 1;
								while(($file = readdir($dh)) !== false) {
									if($file!="." && $file!=".."){
										$datosFile[] = $file;		
									}
								}
							}
						}
						
						if(count($datosFile) != 0){
							
							
							if(isset($_POST['copiar'])){
								echo '<h5>Copiaando archivos de la ruta: </h5>';
								echo $rutaInicial .' <strong>a la ruta</strong> '.$rutaFinal;
								echo "<br><strong style='font-size:18px;' id='text-fin'>Espere mientras termina el proceso ... </strong><br>";
								
								foreach($datosFile as $row){									
									if(isset($_POST['file_'.$row])){
										echo "Iniciando ".$row."<br>";
										$controllerData = ucfirst($row)."Controller.php";
										$modelData = ucfirst($row).".php";
										$modelDatas = ucfirst($row)."Search.php";
										$viewsData = $row;	
										
										echo "controllers/".$controllerData." | models/".$modelData." | models/".$modelDatas." | views/".$row;
										echo "<br>";
										
										$rConF = $rutaFinal."/controllers/".$controllerData;
										$rCont = realpath($rutaInicial.'/../')."/controllers/".$controllerData;
										if(file_exists($rConF)){
											echo exec("rm -r ".$rConF);
											if(file_exists($rCont)){
												exec("cp ".$rCont." ".$rConF, $output, $retrun_var);
											}else{
												echo "No encontramos el controlador de la vista ".$viewsData;
											}	
										}else{											
											if(file_exists($rCont)){
												exec("cp ".$rCont." ".$rConF, $output, $retrun_var);
											}else{
												echo "No encontramos el controlador de la vista ".$viewsData;
											}											
											//
										}
										//echo "<br>";
										
										$rModF = $rutaFinal."/models/".$modelData;
										$rMod = realpath($rutaInicial.'/../')."/models/".$modelData;
										if(file_exists($rModF)){
											echo exec("rm -r ".$rModF);
											
											if(file_exists($rMod)){
												exec("cp ".$rMod." ".$rModF, $output, $retrun_var);
											}else{
												echo "No encontramos el modelo de la vista ".$viewsData;
											}												
										}else{
											if(file_exists($rMod)){
												exec("cp ".$rMod." ".$rModF, $output, $retrun_var);
											}else{
												echo "No encontramos el modelo de la vista ".$viewsData;
											}	
										}
										//echo "<br>";
										
										$rModsF = $rutaFinal."/models/".$modelDatas;
										$rMods = realpath($rutaInicial.'/../')."/models/".$modelDatas;
										if(file_exists($rModsF)){
											echo exec("rm -r ".$rModsF);
											
											if(file_exists($rMods)){
												exec("cp ".$rMods." ".$rModsF, $output, $retrun_var);
												//print_r($retrun_var);
												//print_r($output);
											}else{
												echo "No encontramos el modelo de la vista ".$viewsData;
											}												
										}else{
											if(file_exists($rMods)){
												exec("cp ".$rMods." ".$rModsF, $output, $retrun_var);
											}else{
												echo "No encontramos el modelo de la vista ".$viewsData;
											}	
										}
										//echo "<br>";
										
										$rViewF = $rutaFinal."/views/".$viewsData;
										$rView = realpath($rutaInicial)."/".$viewsData;										
										if(file_exists($rViewF)) {
											echo exec("rm -r ".$rViewF);
											if(file_exists($rView)){
												echo "cp ".$rView." ".$rViewF;
												exec("mkdir ".$rViewF);
												$rViewF1 = $rutaFinal."/views/";
												exec("cp -r ".$rView." ".$rViewF1, $output, $retrun_var);
												//print_r($retrun_var);
												//print_r($output);
											}else{
												echo "No encontramos el modelo de la vista ".$viewsData;
											}
											echo "<br>";
											//exec("rm -r ".$rutaFinal.'/'.$viewsData);
										}else{
											if(file_exists($rView)){
												echo "cp ".$rView." ".$rViewF;
												exec("mkdir ".$rViewF);
												$rViewF1 = $rutaFinal."/views/";
												exec("cp -r ".$rView." ".$rViewF1, $output, $retrun_var);
												//print_r($retrun_var);
												//print_r($output);
											}else{
												echo "No encontramos el modelo de la vista ".$viewsData;
											}
											echo "<br>";
										}
										echo "<br>";
									}	
									
								}
								echo "<script>";
								echo "document.getElementById('text-fin').innerHTML = 'Proceso Finalizado';";
								echo "</script>";
							}else{
								echo '<h5>Copiar carpetas seleccionadas de la ruta: </h5>';
								echo $rutaInicial .' <strong>a la ruta</strong> '.$rutaFinal;
								echo "<br><strong style='font-size:18px;'>Carpetas ( <input type='checkbox' checked id='select_all' /> seleccionar todos)</strong><br>";


								$form = ActiveForm::begin([
									'id' => 'form',
									'action' => ['site/exportfiles'],
									'options' => ['method' => 'POST', 'onsubmit' => 'cargando()'],				
								]);
								
								$datosOrdenados = array();
								foreach ($datosFile as $key => $row) {
									$datosOrdenados[$key] = $row;
								}

								array_multisort($datosOrdenados, SORT_ASC, $datosFile);

								foreach($datosOrdenados as $row){
									echo '<input type="checkbox" id="file_'.$row.'" name="file_'.$row.'" checked> '.$row;
									echo "<br>";
								}

								echo '<input type="hidden" name="urlinicial" value="'.$rutaInicial.'" />';
								echo '<input type="hidden" name="urldestino" value="'.$rutaFinal.'" />';
								echo '<input type="hidden" name="copiar" value="ok" />';

								echo '<br><br><div class="form-group" style="margin-top: 1px; margin-left: 5px;">
											<button type="submit" class="btn btn-success " id="btn_export">Iniciar copiar archivos</button>
									  </div>';
								ActiveForm::end();
							}							
						}else{
							echo '<br><br><div class="alert alert-success" role="alert">
									No hay subcarpetas en el directorio ingresado.
								</div>';
						}
					
					}else{
						echo '<br><br><div class="alert alert-danger" role="alert">
								El <strong>directorio destino</strong> no existe, verifica y recarga los datos de nuevo.
							</div>';
					}
				}else{
					echo '<br><br><div class="alert alert-danger" role="alert">
						 	El <strong>directorio inicial</strong> no existe, verifica y recarga los datos de nuevo.
						</div>';
				}
			}else{
				echo '<br><br><div class="alert alert-danger" role="alert">
				 Las rutas no deben de estar vacias, intenta de nuevo.
				</div>';
			}
			
		}else{
			echo '<br><br><div class="alert alert-success" role="alert">
				 Debes de ingresar la url inicial, la url destino y presionar el boton de enviar datos  para iniciar el proceso.
				</div>';
		}
		?>
	</div>
</div>
<script>
	
	
	 function cargando(){
		document.getElementById('cargando').style.display = 'block';
		document.getElementById('btn_export').disabled = true;
		
		 startday=new Date();
		clockStart=startday.getTime();
		 
		window.setTimeout('getSecs()',1);
	 }  
	
		 
	function initStopwatch(){
		var myTime=new Date();
		return((myTime.getTime()-clockStart)/1000);
	}
		 
	function getSecs(){
		var tSecs=Math.round(initStopwatch());
		var iSecs=tSecs%60;
		var iMins=Math.round((tSecs-30)/60);
		var sSecs=""+((iSecs>9)?iSecs:"0"+iSecs);
		var sMins=""+((iMins>9)?iMins:"0"+iMins);
		document.getElementById('timespent').value=sMins+":"+sSecs;
		window.setTimeout('getSecs()',1000);
	}
		 
		
	$('#select_all').click(function() {
	  var c = this.checked;
	  $(':checkbox').prop('checked', c);
	});
</script>
