<?php

use yii\helpers\Html;
use yii\helpers\Url;

use yii\widgets\ActiveForm;
use app\assets\AppAsset;

$asset = AppAsset::register($this);
$baseUrl = $asset->baseUrl;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */
/* @var $form yii\widgets\ActiveForm */

$this->registerJsFile($baseUrl.'/require/js/jquery/jquery-2.1.4.js', ['position' => \yii\web\View::POS_HEAD]);

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */
?>

<div class="app-page-title">
	<div class="page-title-wrapper">
    	<div class="page-title-heading">
        	<div class="page-title-icon">
            	<i class="pe-7s-add-user icon-gradient bg-malibu-beach"></i>
            </div>
            <div>
				Usuarios 
				<div class="page-title-subheading">Editar registro - <?= $model->nombreUsuario ?></div>
            </div>
        </div>
        <div class="page-title-actions">
			<?php 				
					echo Html::a(' Perfiles', $url = ['perfiles/index'], $options = ['class'=>'btn-shadow btn '.Yii::$app->globals->btnMenu().' mr-3']);	
					echo Html::a(' Usuarios', $url = ['usuarios/index'], $options = ['class'=>'btn-shadow btn '.Yii::$app->globals->btnMenu().' mr-3 active']);
					echo Html::a(' Perfil Compuesto', $url = ['pcompuestos/index'], $options = ['class'=>'btn-shadow btn '.Yii::$app->globals->btnMenu().' mr-3']);
					echo Html::a(' Permisos', $url = ['paccion/index'], $options = ['class'=>'btn-shadow btn '.Yii::$app->globals->btnMenu().' mr-3']);
					
					echo Html::a(' Menus', $url = ['pmenus/index'], $options = ['class'=>'btn-shadow btn '.Yii::$app->globals->btnMenu().' mr-3']);			
					echo Html::a(' Formularios', $url = ['formulariosperfiles/index'], $options = ['class'=>'btn-shadow btn '.Yii::$app->globals->btnMenu().' mr-3']);	
			?>	
		</div>
     </div>
</div>
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
?>
<ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
	<li class="nav-item">
		<?php
		echo Html::a('<span><i class="'.$iSeacrh.'"></i> Consulta</span>', $url = ['usuarios/index'], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
		?>
    </li>
	<li class="nav-item">
		<?php
		echo Html::a('<span><i class="'.$iAdd.'" aria-hidden="true"></i> Alta</span>', $url = ['usuarios/create'], $options = ['class'=>'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
		?>
    </li>
    <li class="nav-item">
		<?php
		echo  Html::a('<span><i class="fa fa-lock" aria-hidden="true"></i> &nbsp;Contraseña</span>', $url = ['usuarios/passwd&id='.$_GET['id']], $options = ['class' => 'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
		?>
    </li>
	 <li class="nav-item">
		<?php
		echo  Html::a('<span><i class="fas fa-file-alt" aria-hidden="true"></i> &nbsp;Carta responsiva</span>', $url = ['usuarios/carta&id='.$_GET['id']], $options = ['target'=>'_blank', 'class' => 'mb-2 mr-2 btn-pill btn-transition btn '.Yii::$app->globals->btnAcciones()]);
		?>
    </li>
</ul>
<div class="main-card mb-3 card">
      <div class="card-body">
		  <div class="col-12  pleft-0">
			<span style="font-size: 16px;">Selecciona los establecimientos para integrar a la carta responsiva<br><br></span>	
		  </div>
			<?php $form = ActiveForm::begin([
								'method' => 'get',
								'action' => Url::to(['usuarios/carta']),
								'options' => [
									'target' => '_blank',
								]
							]); ?>
		  
		  	<div style="clear: both;"></div>  
			<div class="col-12  pleft-0">
				<div class="form-group">
					<?php
					$qryEstablecimiento = "SELECT * FROM Establecimientos where activoEstablecimiento=1 and regEstado=1";
					$establecimiento = Yii::$app->db->createCommand($qryEstablecimiento)->queryAll();
					
					foreach($establecimiento as $row){
						echo '<div>
							  <input type="checkbox" name="estab[]" value="'.$row['establecimientoID'].'">
							  <label for="establecimiento">'.$row['establecimientoID'].'-'.$row['razonSocialEstablecimiento'].'</label>
							</div>';
					}
					?>
				</div>
			</div>
		    <div style="clear: both;"></div>  
			<div class="col-12  pleft-0">
				<div class="form-group">
					<br>
					<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
					<?= Html::submitButton('<i class="pe-7s-cloud-download"></i> &nbsp;Descargar carta responsiva', ['id'=>'btnSend', 'class' => 'btn '.Yii::$app->globals->btnSave()]) ?>
				</div>
			</div>
		    <?php ActiveForm::end(); ?>
    </div>
</div>

