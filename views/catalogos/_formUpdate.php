<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Catalogos */
/* @var $form yii\widgets\ActiveForm */
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

use app\models\Menus;
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
					 <i class="fa fa-check-square-o" aria-hidden="true"></i>  Registro guardado con exito! ('.Html::a("Ver catalogos", $url = ['catalogos/update&id='.$_GET['id']], $options = ['class'=>'']).')
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
<div class="col-sm-4 float-left pleft-0">    
	<?php 
	$db_connection = Yii::$app->db;
	$dbSchema = $db_connection->schema;
	$tables = $dbSchema->getTableNames();
	$data = array();
	foreach($tables as $tbl){
		$data[$tbl] = $tbl;
		//echo $tbl."<br>";
	}
	
	echo $form->field($model, 'nombreCatalogo')->widget(Select2::classname(), [                         
		'data' => $data,
		'language' => 'es',
		'options' => ['placeholder' => ' --- Selecciona --- '],
		'pluginOptions' => ['allowClear' => true]]); 	
	?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'nombreModelo')->textInput() ?>
</div>


<div class="col-4 float-left  pleft-0">
	<?php
	if($model->isNewRecord){
		echo $form->field($model, 'activoCatalogo')->checkbox(['checked'=>'checked']);
	}else{
		echo $form->field($model, 'activoCatalogo')->checkbox();
	}
	?>
</div>
<div style="clear: both;"></div>



<div style="clear: both;"></div>    
<div class="col-12  pleft-0">
    <div class="form-group">
		<br>			
        <?= Html::submitButton('<i class="pe-7s-diskette"></i> &nbsp;Guardar', ['class' => 'btn '.Yii::$app->globals->btnSave()]) ?>
		<?= $form->field($model, 'sqlQuery')->textInput(['type'=>'hidden', 'value'=>'na'])->label(false) ?>
    </div>
</div>

 <?php ActiveForm::end(); ?>

