<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

use app\assets\AppAsset;
use yii\helpers\Url;

$asset = AppAsset::register($this);
$baseUrl = $asset->baseUrl; 

/* @var $this yii\web\View */
/* @var $model app\models\Slider */
/* @var $form yii\widgets\ActiveForm */
?>



 <?php $form = ActiveForm::begin([
        'options' => [
			'enctype' => 'multipart/form-data',
            'onsubmit' => 'cargando()'
        ]
    ]); ?>

<div class="col-sm-12 pleft-0">
<?php $tterror = count($model->getErrors());
if($tterror != 0){
	echo '<div class="alert alert-danger" role="alert">
			<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
			'.$form->errorSummary($model).'
	</div>';
}else{
	if(isset($_GET['insert'])){
		if($_GET['insert'] == 'true'){
			echo '<div class="alert alert-success" role="alert">
					 <i class="fa fa-check-square-o" aria-hidden="true"></i>  Registro guardado con exito! ('.Html::a("Ver slider", $url = ['slider/update&id='.$_GET['id']], $options = ['class'=>'']).')
				 </div>';
		}
	}
	
	if(isset($_GET['update'])){
		if($_GET['update'] == 'true'){
			echo '<div class="alert alert-success" role="alert">
					 <i class="fa fa-check-square-o" aria-hidden="true"></i>  Registro actualizado con exito!
				 </div>';
		}
	}
}
?>
</div>
<div style="clear: both;"></div>
<div id="cargando" style="display:none;  color: green; font-size:12px; text-align: center;">
	<img src="<?php echo $baseUrl;?>/require/images/loader.gif" alt="cargando" /> (No cierre ni refresque la pagina hasta finalizar el proceso)
	<br>
</div>
<div style="clear: both;"></div>
	<div class="col-6 float-left">
		
		<?= $form->field($model, 'tituloSlider')->textInput(['maxlength' => true, 'required'=>'required']) ?>
		<?= $form->field($model, 'contenidoSlider')->textInput(['maxlength' => true, 'required'=>'required']) ?>
		<?= $form->field($model, 'ordenSlider')->textInput(['type'=>'number', 'required'=>'required']) ?>
		
		
	<?php
	if($model->isNewRecord){
		echo $form->field($model, 'activoConfiguracionesSlider')->checkbox(['checked'=>'checked']);
	}else{
		echo $form->field($model, 'activoConfiguracionesSlider')->checkbox();
	}
	?>
		
	</div>
	<div class="col-6 float-left">		
	    <div class="form-group">
			 <div class="form-group">
				<label class="control-label">Selecciona un archivo (recomendado 320px X 673px)</label>
				<input type="file" class="form-control" id="img_data" name="img_data" />
			</div>
		</div>
		
		<?php
		if($model->isNewRecord){
			echo $form->field($model, 'imagenSlider')->textInput(['type'=>'hidden', 'value' => 'na'])->label(false);
		}else{
			if($model->imagenSlider != ''){
				echo '<img src="../slider/'.$model->imagenSlider.'" alt="Slider" height="140px;" />';
			}
			echo $form->field($model, 'imagenSlider')->textInput(['type'=>'hidden'])->label(false);
		}
	 ?>
		
	</div>

<div style="clear: both;"></div>    
<div class="col-12  pleft-0">
    <div class="form-group">
		<br>
        <?= Html::submitButton('<i class="pe-7s-diskette"></i> &nbsp;'.Yii::$app->globals->getTraductor(14, Yii::$app->session['idiomaId'], 'Guardar'), ['class' => 'btn '.Yii::$app->globals->btnSave()]) ?>
    </div>
</div>

 <?php ActiveForm::end(); ?>

<script>
	 function cargando(){
		document.getElementById('cargando').style.display = 'block';
	 }  
</script>

