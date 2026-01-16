<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\assets\AppAsset;

$asset = AppAsset::register($this);
$baseUrl = $asset->baseUrl;

$this->title = 'Portal :: Login';


$this->registerJsFile($baseUrl.'/require/js/jquery/jquery-2.1.4.js', ['position' => \yii\web\View::POS_HEAD]);
?>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'layout' => 'horizontal',
		'options' => [
			//agregar opciones 
         ],		
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>


	<div class="form-row">
    	<div class="col-md-6">
        	<div class="position-relative form-group">
				<label for="exampleEmail" class="">Usuario</label>
				<input type="text" class="form-control" placeholder="Usuario" id="loginform-username" name="LoginForm[username]" autofocus="" aria-required="true" required="required" aria-invalid="false" style="outline: none !important;">
			</div>
        </div>
                                       
		<div class="col-md-6">
			 			
			<div class="form-group">
				<label for="examplePassword" class="">Contraseña</label>
				<div class="input-group" id="show_hide_password">
					<input  type="password" id="loginform-password"  required="required" class="form-control" name="LoginForm[password]"  aria-required="true" aria-invalid="false" placeholder="Contraseña" style="outline: none !important;">
					<div class="input-group-append" >
						<a href="#" class="input-group-text" style="cursor: pointer; text-decoration: none;"><i class="fa fa-eye-slash"></i></a>
					</div>
				</div>
			</div>
        </div>
		
		
    </div>

	 <div class="position-relative form-check">
		 <input class="form-check-input" type="checkbox" id="loginform-rememberme" name="LoginForm[rememberMe]" value="1" checked="" aria-invalid="false">
		 <label for="exampleCheck" class="form-check-label">Recordarme</label>
		 | <?php echo Html::a('¿Olvidaste tu Contraseña?', $url = ['site/recupera']); ?>
	</div>                                    
	<div class="divider row"></div>

	<?php
	if(Yii::$app->session->getFlash('failure')){
		if(Yii::$app->session->getFlash('failure') != ''){
			echo '<div class="alert alert-danger" role="alert">
					  '.Yii::$app->session->getFlash('failure').'
				 </div>';	
		}										
	}

	if(isset($_GET['hash'])){
		if($_GET['hash'] == 'false'){
			echo '<div class="alert alert-danger" role="alert">
					  El hash o folio sufrio alguna alteración, contacte con su administrador.
				 </div>';
		}		
	}

	if(isset($_GET['pass'])){
		if($_GET['pass'] == 'true'){
			echo '<div class="alert  alert-success" role="alert">
					  Datos actualizados correctamente.
				 </div>';
		}		
	}
	?>

	<div class="d-flex align-items-center">
    	<div class="ml-auto">
			<button class="btn btn-primary btn-lg"><i class="fa fa-sign-in"></i> &nbsp; Iniciar sesi&oacute;n</button>
        </div>
    </div>							
	
	 <?php ActiveForm::end(); ?>

<script>

$(document).ready(function() {
    $("#show_hide_password a").on('click', function(event) {
        event.preventDefault();
        if($('#show_hide_password input').attr("type") == "text"){
            $('#show_hide_password input').attr('type', 'password');
            $('#show_hide_password i').addClass( "fa-eye-slash" );
            $('#show_hide_password i').removeClass( "fa-eye" );
        }else if($('#show_hide_password input').attr("type") == "password"){
            $('#show_hide_password input').attr('type', 'text');
            $('#show_hide_password i').removeClass( "fa-eye-slash" );
            $('#show_hide_password i').addClass( "fa-eye" );
        }
    });
});
</script>