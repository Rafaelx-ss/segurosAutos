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

$qryReglas = "Select * from ReglasPassw where regEstado=1 limit 1";
$permisosPass = Yii::$app->db->createCommand($qryReglas)->queryOne();


$this->registerJsFile($baseUrl.'/require/js/jquery/jquery-2.1.4.js', ['position' => \yii\web\View::POS_HEAD]);
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
        	
			<div class="form-group">
				<label for="examplePassword" class="">Contraseña</label>
				<div class="input-group" id="show_hide_password">
					<input type="password" class="form-control" placeholder="Usuario" id="loginform-username" name="passwd" autofocus="" aria-required="true" required="required" aria-invalid="false">
					<div class="input-group-append" >
						<a href="#" class="input-group-text" style="cursor: pointer; text-decoration: none;"><i class="fa fa-eye-slash"></i></a>
					</div>
				</div>
			</div>
			
			<div style="clear: both;"></div>
			<!--
			<input type="checkbox" onclick="myFunction()"> &nbsp;Ver contraseñas
			
			<div style="clear: both;"></div>
id="message"
			-->
			<br>
			<div >
			  <h5>La contraseña debe de contener: </h5>
			  <?php
				if($permisosPass['contieneMinusculas'] == 1){
					echo '<span id="letter" class="invalid">Una letra <b>Minuscula</b></span><br>';
				}

				if($permisosPass['contieneMayuscula']== 1){
					echo '<span id="capital" class="invalid">Una letra <b>Mayuscula</b></span><br>';
				}

				if($permisosPass['contieneCaracteresEspeciales']== 1){
					echo '<span id="especial" class="invalid">Algún caracter especial <b>@ $ _ -</b></span><br>';
				}
				
				if($permisosPass['contieneRepetidos']== 1){
					echo '<span id="numberepetidos" class="valid">Sin  <b>caracteres repetidos</b></span><br>';
				}

				if($permisosPass['contieneConsecutivos']== 1){
					echo '<span id="consecutivos" class="valid">Sin <b>caracteres consecutivos</b></span><br>';
				}

				if($permisosPass['contieneNumeros']== 1){
					echo '<span id="number" class="invalid">Un  <b>número</b></span><br>';
				}

				if($permisosPass['minimioLongitudPassw'] > 0){
					echo '<span id="length" class="invalid">Minimo <b>'.$permisosPass['minimioLongitudPassw'].' caracteres</b></span><br>';
				}
			  ?>
			</div>
        </div>
                                       
		<div class="col-md-6">
			<div class="form-group">
				<label for="examplePassword" class="">Repite la Contraseña</label>
				<div class="input-group" id="show_hide_password_r">
					<input  type="password" id="confirm_password"  required="required" class="form-control"  name="confirm_password"  aria-required="true" aria-invalid="false" placeholder="Contraseña">
					<div class="input-group-append" >
						<a href="#" class="input-group-text" style="cursor: pointer; text-decoration: none;"><i class="fa fa-eye-slash"></i></a>
					</div>
				</div>
			</div>
        </div>
		
		
    </div>

	                               
	<div class="divider row"></div>

	<?php
	if(isset($_GET['pass'])){
		if($_GET['pass'] == 'false'){
			echo '<div class="alert alert-danger" role="alert">
					  La contraseña ya ha sido utilizada anteriormente, intenta con una nueva.
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
			<button type="submit" class="btn btn-primary btn-lg" id="btnSend" name="send"><i class="fa fa-sign-in"></i> &nbsp; Actualizar Contraseña</button>
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
	
$(document).ready(function() {
    $("#show_hide_password_r a").on('click', function(event) {
        event.preventDefault();
        if($('#show_hide_password_r input').attr("type") == "text"){
            $('#show_hide_password_r input').attr('type', 'password');
            $('#show_hide_password_r i').addClass( "fa-eye-slash" );
            $('#show_hide_password_r i').removeClass( "fa-eye" );
        }else if($('#show_hide_password_r input').attr("type") == "password"){
            $('#show_hide_password_r input').attr('type', 'text');
            $('#show_hide_password_r i').removeClass( "fa-eye-slash" );
            $('#show_hide_password_r i').addClass( "fa-eye" );
        }
    });
});
	
var password = document.getElementById("loginform-username")
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


document.getElementById("btnSend").disabled = true;
	
var myInput = document.getElementById("loginform-username");
	
<?php
	$desabilitar = 0;
	$habilitar = 0;
		if($permisosPass['contieneMinusculas'] == 1){
			echo 'var letter = document.getElementById("letter");';
			$desabilitar = $desabilitar + 1;
		}
		
		if($permisosPass['contieneMayuscula']== 1){
			echo 'var capital = document.getElementById("capital");';
			$desabilitar = $desabilitar + 1;
		}
		
		if($permisosPass['contieneCaracteresEspeciales']== 1){
			echo 'var especial = document.getElementById("especial");';
			$desabilitar = $desabilitar + 1;
		}
		
		if($permisosPass['contieneNumeros']== 1){
			echo 'var number = document.getElementById("number");';
			$desabilitar = $desabilitar + 1;
		}
	
		if($permisosPass['contieneRepetidos']== 1){
			echo 'var repetidos = document.getElementById("numberepetidos");';
			$habilitar = $habilitar + 1;
		}
	
		if($permisosPass['contieneConsecutivos']== 1){
			echo 'var consecutivos = document.getElementById("consecutivos");';
			$habilitar = $habilitar + 1;
		}
		
		if($permisosPass['minimioLongitudPassw'] > 0){
			echo 'var length = document.getElementById("length");';
			$desabilitar = $desabilitar + 1;
		}
?>
	





// When the user clicks on the password field, show the message box
myInput.onfocus = function() {
  //document.getElementById("message").style.display = "block";
}

// When the user clicks outside of the password field, hide the message box
myInput.onblur = function() {
  //document.getElementById("message").style.display = "none";
}

// When the user starts to type something inside the password field
myInput.onkeyup = function() {
	var disabled = 0;
<?php
		if($permisosPass['contieneMinusculas'] == 1){
			echo '// Validate lowercase letters
				  var lowerCaseLetters = /[a-z]/g;
				  if(myInput.value.match(lowerCaseLetters)) {
					letter.classList.remove("invalid");
					letter.classList.add("valid");
					disabled = disabled + 1;
				  } else {
					letter.classList.remove("valid");
					letter.classList.add("invalid");
				}';
		}
		
		if($permisosPass['contieneMayuscula']== 1){
			echo '// Validate capital letters
				  var upperCaseLetters = /[A-Z]/g;
				  if(myInput.value.match(upperCaseLetters)) {
					capital.classList.remove("invalid");
					capital.classList.add("valid");
					disabled = disabled + 1;
				  } else {
					capital.classList.remove("valid");
					capital.classList.add("invalid");
				  }';
		}
		
		if($permisosPass['contieneCaracteresEspeciales']== 1){
			echo '// Validate especial
				  var caracterEspec = /[\*@\*$\*-\*_]/g;
				  if(myInput.value.match(caracterEspec)) {
					especial.classList.remove("invalid");
					especial.classList.add("valid");
					disabled = disabled + 1;
				  } else {
					especial.classList.remove("valid");
					especial.classList.add("invalid");
				  }';
			
		}
		
		if($permisosPass['contieneNumeros']== 1){
			echo '// Validate numbers
				  var numbers = /[0-9]/g;
				  if(myInput.value.match(numbers)) {
					number.classList.remove("invalid");
					number.classList.add("valid");
					disabled = disabled + 1;
				  } else {
					number.classList.remove("valid");
					number.classList.add("invalid");
				  }';
		}
	
		if($permisosPass['contieneRepetidos']== 1){
			echo '// Validate numbers
				  var repet = /([a-zA-Z0-9])\1{1}/g;
				  if(myInput.value.match(repet)) {
				  	//console.log("entro a repetidos");
					repetidos.classList.remove("valid");
					repetidos.classList.add("invalid");
					disabled = disabled + 1;
				  } else {
				  	//console.log("entro a no repetidos");
					repetidos.classList.remove("invalid");
					repetidos.classList.add("valid");
				  }';
		}
	
		if($permisosPass['contieneConsecutivos']== 1){
			echo '// Validate numbers
				 var contv =  validateConsecutivo(myInput.value, 3);
				
				  if(!contv) {
				   console.log("falso");
					consecutivos.classList.remove("valid");
					consecutivos.classList.add("invalid");
					disabled = disabled + 1;
				  } else {
				  	console.log("verdadero");
					consecutivos.classList.remove("invalid");
					consecutivos.classList.add("valid");
				  }';
		}
		
		if($permisosPass['minimioLongitudPassw'] > 0){
			echo '// Validate length
			  if(myInput.value.length >= 8) {
				length.classList.remove("invalid");
				length.classList.add("valid");
				disabled = disabled + 1;
			  } else {
				length.classList.remove("valid");
				length.classList.add("invalid");
			  }';
		}
?>
	
	if(disabled == <?php echo $desabilitar; ?>){
		document.getElementById("btnSend").disabled = false;
	}else{
		document.getElementById("btnSend").disabled = true;
	}
  
}


function myFunction() {
  var x = document.getElementById("loginform-username");
  var x2 = document.getElementById("confirm_password");
  if (x.type === "password") {
    x.type = "text";
	x2.type = "text";
  } else {
    x.type = "password";
	x2.type = "password";
  }
}
	
function validateConsecutivo(string, consec=3, reverse=true) {
    let cpos = cneg = cequ = 1;
    let last_code = cur_code = 0;
    for(let i=0; i<string.length; i++) {
        cur_code = string[i].charCodeAt(0);
        if(cur_code-last_code == 1) { 
            cpos++; // consecutivos
        } else if (reverse && (cur_code-last_code == -1)) { 
            cneg++; // consecutivos en reversa
        } else if (cur_code == last_code) {
            cequ++; // iguales
        } else { 
            cpos = cneg = cequ = 1;
        }
        //
        if([cpos, cneg, cequ].includes(consec)) {
            return false;
        }
        last_code = cur_code;
    }
    return true;
}
</script>