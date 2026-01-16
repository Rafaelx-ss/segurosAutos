<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Tformularios */
/* @var $form yii\widgets\ActiveForm */
use app\assets\AppAsset;
use yii\helpers\Url;

use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

use app\models\Textos;
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
					 <i class="fa fa-check-square-o" aria-hidden="true"></i>  Registro guardado con exito! ('.Html::a("Ver tformularios", $url = ['tformularios/update&id='.$_GET['id']], $options = ['class'=>'']).')
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

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'tipoFormularioNombre')->textInput(['maxlength' => true]) ?>
</div>


<div class="col-sm-4 float-left pleft-0">    
	<?php 
			echo $form->field($model, 'textoID')->widget(Select2::classname(), [                         
			'data' => ArrayHelper::map(Textos::find()->andWhere(['=', 'regEstado', '1'])->orderBy('nombreTexto')->all(), 'textoID', 'nombreTexto'),
			'language' => 'es',
			'options' => ['placeholder' => ' --- Selecciona --- '],
			'pluginOptions' => ['allowClear' => true]]); 	
	?>
</div>


<div style="clear: both;"></div>    
<div class="col-12  pleft-0">
    <div class="form-group">
		<br>
        <?= Html::submitButton('<i class="ti-check-box"></i> &nbsp;Guardar', ['class' => 'btn btn-success']) ?>
    </div>
</div>

 <?php ActiveForm::end(); ?>

