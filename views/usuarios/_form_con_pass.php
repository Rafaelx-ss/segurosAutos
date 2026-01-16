<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */
/* @var $form yii\widgets\ActiveForm */
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
					 <i class="fa fa-check-square-o" aria-hidden="true"></i>  Registro guardado con exito! ('.Html::a("Ver usuarios", $url = ['usuarios/update&id='.$_GET['id']], $options = ['class'=>'']).')
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
	
	echo Url::toRoute('site/changepass&token='.md5($model->codigoRecuperacionPassw).'&folio='.md5($model->usuarioID), true);
?>
</div>
<div style="clear: both;"></div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'nombreUsuario')->textInput(['maxlength' => true, 'required'=>'required']) ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'passw')->textInput(['required'=>'required', 'type'=>'password']) ?>
</div>

<div class="col-4 float-left  pleft-0">
	<div class="form-group field-usuariosapi-usuarioapiid">
		<label class="control-label" for="usuariosapi-usuarioapiid">Repite la contraseña</label>
		<input type="password" id="confirm_password" class="form-control" name="confirm_password"  aria-invalid="false" required>
	</div>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'usuario')->textInput(['maxlength' => true, 'required'=>'required']) ?>
</div>


<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'correoUsuario')->textInput(['maxlength' => true, 'required'=>'required']) ?>
</div>


<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'intentosValidos')->textInput() ?>
</div>


<div class="col-4 float-left  pleft-0">
	<?php
	if($model->isNewRecord){
		echo $form->field($model, 'activoUsuario')->checkbox(['checked'=>'checked']);
	}else{
		echo $form->field($model, 'activoUsuario')->checkbox();
	}
	?>
</div>

<div style="clear: both;"></div>    
<div class="col-12  pleft-0">
    <div class="form-group">
		<br>
        <?= Html::submitButton('<i class="pe-7s-diskette"></i> &nbsp;Guardar', ['class' => 'btn '.Yii::$app->globals->btnSave()]) ?>
    </div>
</div>

 <?php ActiveForm::end(); ?>

<script>
var password = document.getElementById("usuarios-passw")
  , confirm_password = document.getElementById("confirm_password");

function validatePassword(){
  if(password.value != confirm_password.value) {
    confirm_password.setCustomValidity("Las contraseñas no coinciden");
  } else {
    confirm_password.setCustomValidity('');
  }
}

password.onchange = validatePassword;
confirm_password.onkeyup = validatePassword;
</script>