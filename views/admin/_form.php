<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Admin */
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
					 <i class="fa fa-check-square-o" aria-hidden="true"></i>  Registro guardado con exito! ('.Html::a("Ver admin", $url = ['admin/update&id='.$_GET['id']], $options = ['class'=>'']).')
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
	<?= $form->field($model, 'Tipo_user')->dropDownList(['Administrador'=>'Administrador', 'Usuario'=>'Usuario', 'Invitado'=>'Invitado', 'RH'=>'RH', 'Vehiculos'=>'Vehiculos'], ['required'=> 'required']) ?>
</div>


<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'Nombre_admin')->textInput(['maxlength' => true]) ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'User_admin')->textInput(['maxlength' => true]) ?>
</div>

<div class="col-4 float-left pleft-0">
	<?= $form->field($model, 'Pass_radmin')->passwordInput(['maxlength' => true, 'autocomplete'=>'new-password', 'onclick'=>'mod_text();', 'onblur'=>'mod_pass();', 'required'=> 'required']) ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'Status_admin')->dropDownList(['Activo'=>'Activo', 'Inactivo'=>'Inactivo'], ['required'=> 'required']) ?>
</div>

<div style="clear: both;"></div>    
<div class="col-12  pleft-0">
    <div class="form-group ">
		<br>
        <?= Html::submitButton('<i class="ti-check-box"></i> &nbsp;Guardar', ['class' => 'btn btn-success']) ?>
    </div>
</div>

 <?php ActiveForm::end(); ?>


<script>
	function mod_text(){
		document.getElementById("admin-pass_radmin").type="text";
	}
	function mod_pass(){
		document.getElementById("admin-pass_radmin").type="password";
	}
</script>


