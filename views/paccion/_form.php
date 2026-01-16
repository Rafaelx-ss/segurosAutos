<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

use app\assets\AppAsset;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Paccion */
/* @var $form yii\widgets\ActiveForm */
use app\models\Perfiles;
use app\models\Aformularios;


$asset = AppAsset::register($this);
$baseUrl = $asset->baseUrl;

$this->registerJsFile($baseUrl.'/require/js/jquery/jquery-2.1.4.js', ['position' => \yii\web\View::POS_HEAD]);
?>



 <?php $form = ActiveForm::begin(); ?>

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
					 <i class="fa fa-check-square-o" aria-hidden="true"></i>  Registro guardado con exito!
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
<div class="col-sm-6 float-left pleft-0"> 
	<div class="col-sm-12 float-left pleft-0">  
		<?php 
			echo $form->field($model, 'perfilID')->widget(Select2::classname(), [                         
				'data' => ArrayHelper::map(Perfiles::find()->andWhere(['=', 'regEstado', '1'])->orderBy('nombrePerfil')->all(), 'perfilID', 'nombrePerfil'),
				'language' => 'es',
				'options' => ['placeholder' => ' --- Selecciona --- ', 'required'=>'required'],
				'pluginOptions' => ['allowClear' => true,  'initialize' => true]]); 	
		?>
	</div>

	<div class="col-sm-12 float-left pleft-0">  
		<?php 		
			echo '<label class="control-label">Formulario</label>';
			echo Select2::widget([
				'name' => 'idformchange',
				'id'=>'idformchange',
				'data' =>  ArrayHelper::map(Yii::$app->db->createCommand("SELECT * FROM Formularios where estadoFormulario='1' order by nombreFormulario ASC")->queryAll(), 'formularioID', 'nombreFormulario'),
				'language' => 'es',
				'options' => ['placeholder' => ' --- Selecciona --- ', 'required'=>'required'],
				'pluginOptions' => ['allowClear' => true,  'initialize' => true],
			]);
		?>
	</div>
	
	<div style="clear: both;"></div>  <br>
	<div class="col-4 float-left  pleft-0">
		<?php
		if($model->isNewRecord){
			echo $form->field($model, 'activoPerfilAccionFormulario')->checkbox(['checked'=>'checked']);
		}else{
			echo $form->field($model, 'activoPerfilAccionFormulario')->checkbox();
		}
		?>
	</div>
	
	<div class="col-6 float-left  pleft-0">
		<div class="form-group">
			<label><input type="checkbox" id="formperfil" name="formperfil" value="1" checked="checked"> Agregar formulario-perfil</label>
		</div>	
	</div>
	
</div>	



<div class="col-sm-6 float-left pleft-0" id="itemAcciones"> 
	<!-- aqui cargan las acciones al seleccionar el formulario-->
</div>

<div style="clear: both;"></div>    
<div class="col-12  pleft-0">
    <div class="form-group">
		<br>
        <?= Html::submitButton('<i class="pe-7s-diskette"></i> &nbsp;'.Yii::$app->globals->getTraductor(14, Yii::$app->session['idiomaId'], 'Guardar'), ['class' => 'btn '.Yii::$app->globals->btnSave()]) ?>
		<?= $form->field($model, 'accionFormularioID')->textInput(['value'=>'0', 'type'=>'hidden'])->label(false) ?>
    </div>
</div>

 <?php ActiveForm::end(); ?>


<script>
$(document).ready(function(){
	$("#idformchange").change(function(){
		var data = document.getElementById('idformchange').value;
		
		$.ajax({
            url: '<?php echo Url::to(['paccion/getinput']); ?>',
			type: "POST",
			data:"idform="+data,
            success:function(response){
				document.getElementById('itemAcciones').innerHTML = response;
            }
       });
	}); 

}); 
</script>