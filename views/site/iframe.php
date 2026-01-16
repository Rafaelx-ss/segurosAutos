<?php
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\assets\AppAsset;

$asset = AppAsset::register($this);
$baseUrl = $asset->baseUrl;
/* @var $this yii\web\View */
//Yii::$app->response->redirect(['siniestros/index']);
//$this->title = 'My Yii Application';
$error = 'true';
$tituloPagina = 'Sitio';
$iconPagina = 'pe-7s-attention';
$urlPagina='';

if(isset($_GET['pagina'])){
	
	$iframe = Yii::$app->db->createCommand("SELECT * FROM Formularios where md5(formularioID)='".$_GET['pagina']."'")->queryOne();
	if(isset($iframe['formularioID'])){
		$tituloPagina  = Yii::$app->globals->getTraductor($iframe['textoID'], Yii::$app->session['idiomaId'], $iframe['nombreFormulario']);
		$iconPagina = $iframe['icono'];
		$urlPagina = $iframe['urlArchivo'];
		$error = 'false';
	}
}
?>


<div class="app-page-title">
	<div class="page-title-wrapper">
    	<div class="page-title-heading">
        	<div class="page-title-icon">
            	<i class="<?= $iconPagina ?> icon-gradient bg-malibu-beach"></i>
            </div>
            <div>
				<?= $tituloPagina ?>			<div class="page-title-subheading">Consulta</div>
            </div>
        </div>
       
     </div>
</div>

<div class="main-card mb-3 card">
      <div class="card-body">	
	  <?php
		  if($error == 'false'){
			  echo '<iframe src="'.$urlPagina.'" marginwidth="0" marginheight="0" name="ventana_iframe" scrolling="Yes" border="0" 
frameborder="0" width="100%" height="500"></iframe>';
		  }else{
			  echo '<div class="site-error">
						<div class="alert alert-danger text-center">
							Página no encontrada.    </div>

						<div style="text-align: center; margin-top: 40px;">
						<img src="/kairos/web/images/page_error.png" alt="error" style="width: 20%;">

						</div>

					</div>';
		  }
	  ?>
 	</div>
</div>