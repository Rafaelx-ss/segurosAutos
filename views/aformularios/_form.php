<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

use app\assets\AppAsset;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Aformularios */
/* @var $form yii\widgets\ActiveForm */


use app\models\Acciones;
use app\models\Formularios;
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


<div class="col-4 float-left  pleft-0">
	<?php 
		echo $form->field($model, 'formularioID')->widget(Select2::classname(), [                         
			'data' => ArrayHelper::map(Formularios::find()->andWhere(['=', 'regEstado', '1'])->orderBy('nombreFormulario')->all(), 'formularioID', 'nombreFormulario'),
			'language' => 'es',
			'options' => ['placeholder' => ' --- Selecciona --- '],
			'pluginOptions' => ['allowClear' => true]]); 	
	?>
</div>

<div class="col-4 float-left " style="padding-left: 40px;">
	<div class="form-group field-catalogos-nombremodelo">
		<h5>Acciones</h5>
		<?php
		$formAcciones = Yii::$app->db->createCommand("SELECT * FROM Acciones where accionID !=1 and estadoAccion=1 and regEstado=1")->queryAll();
		
		foreach($formAcciones as $axn){
			echo '<input type="checkbox" class="form-check-input" id="chk_'.$axn['accionID'].'" name="chk_'.$axn['accionID'].'" checked value="ok">
				  <label class="form-check-label" for="exampleCheck1">'.$axn['nombreAccion'].'</label>
				  <br>';
		}
		?>		
	</div>
</div>


<div class="col-4 float-left  pleft-0">
	<?php
	if($model->isNewRecord){
		echo $form->field($model, 'estadoAccion')->checkbox(['checked'=>'checked']);
	}else{
		echo $form->field($model, 'estadoAccion')->checkbox();
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

