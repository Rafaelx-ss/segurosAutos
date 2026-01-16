<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\assets\AppAsset;

$asset = AppAsset::register($this);
$baseUrl = $asset->baseUrl;

$this->title = 'Portal :: actualizacion de datos';


//print_r($userData);
?>
<style>
/* Add a green text color and a checkmark when the requirements are right */
.valid {
  color: green;
}

.valid:before {
  position: relative;
  left: -5px;
  font-family: 'Font Awesome 5 Free';
  content: "\f00c";
}

/* Add a red text color and an "x" icon when the requirements are wrong */
.invalid {
  color: red;
}

.invalid:before {
  position: relative;
  left: -5px;
	font-family: 'Font Awesome 5 Free';
  content: "\f00d";
}
</style>
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'layout' => 'horizontal',
		'options' => [
			//agregar opciones 
         ],
    ]); ?>


	<div class="form-row">
		<div class="col-md-6">
        	<div class="position-relative form-group">
				<label for="exampleEmail" class="">Usuario</label>
				<input type="text" class="form-control" placeholder="Usuario" id="loginform-user" name="user" autofocus="" required="required" >
			</div>
			
        </div>	
    	<div class="col-md-6">
        	<div class="position-relative form-group">
				<label for="exampleEmail" class="">Correo</label>
				<input type="text" class="form-control" placeholder="Usuario" id="loginform-username" name="correo" autofocus="" required="required" >
			</div>
			
        </div>		
		
    </div>

	                               
	<div class="divider row"></div>

	<?php
	if(isset($_GET['datos'])){
		if($_GET['datos'] == 'false'){
			echo '<div class="alert alert-danger" role="alert">
					  Los datos ingresados son incorrectos.
				 </div>';
		}else{
			echo '<div class="alert alert-success" role="alert">
					  Enviamos un correo a la dirección ingresada.
				 </div>';
		}		
	}

	if(isset($_GET['update'])){
		if($_GET['update'] == 'false'){
			echo '<div class="alert alert-danger" role="alert">
					  Ocurrio un error al actualizar, intenta de nuevo o contacta con tu administrador.
				 </div>';
		}		
	}

	
	?>


	<div class="d-flex align-items-center">
    	<div class="ml-auto">
			 <?php echo Html::a('Iniciar sesión', $url = ['site/login'],  ['class' => 'btn btn-secondary btn-lg']); ?> 
			<button type="submit" class="btn btn-primary btn-lg" id="btnSend" name="send"><i class="fa fa-sign-in"></i> &nbsp; Solicitar cambio de contraseña</button>
        </div>
    </div>							
	
	 <?php ActiveForm::end(); ?>


