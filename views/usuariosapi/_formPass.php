<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\Usuariosapi */
/* @var $form yii\widgets\ActiveForm */

use app\assets\AppAsset;
use yii\helpers\Url;


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
					 <i class="fa fa-check-square-o" aria-hidden="true"></i>  '.Yii::$app->globals->getTraductor(8, Yii::$app->session['idiomaId'], 'Registro actualizado con exito').'! ('.Html::a(Yii::$app->globals->getTraductor(15, Yii::$app->session['idiomaId'], 'Ver')." usuariosapi", $url = ['usuariosapi/update&f='.$_GET['f'].'&id='.$_GET['id']], $options = ['class'=>'']).')
				 </div>';
		}
	}
	
	if(isset($_GET['update'])){
		if($_GET['update'] == 'true'){
			echo '<div class="alert alert-success" role="alert">
					 <i class="fa fa-check-square-o" aria-hidden="true"></i>  '.Yii::$app->globals->getTraductor(8, Yii::$app->session['idiomaId'], 'Registro actualizado con exito').'!
				 </div>';
		}
	}
}
?>
</div>
<div style="clear: both;"></div>

<div class="col-4 float-left  pleft-0">
	<div class="form-group field-usuariosapi-usuarioapiid">
		<label class="control-label" for="usuariosapi-usuarioapiid">Contraseña</label>
		<input type="password" id="usuariosapi-passw" class="form-control" name="Usuariosapi[passw]"  aria-invalid="false" required>
	</div>
</div>
<div class="col-4 float-left  pleft-0">
	<div class="form-group field-usuariosapi-usuarioapiid">
		<label class="control-label" for="usuariosapi-usuarioapiid">Repite la contraseña</label>
		<input type="password" id="confirm_password" class="form-control" name="confirm_password"  aria-invalid="false" required>
	</div>
</div>

<div style="clear: both;"></div>    
<div class="col-12  pleft-0">
    <div class="form-group">
		<br>
        <?= Html::submitButton('<i class="pe-7s-diskette"></i> &nbsp;'.Yii::$app->globals->getTraductor(14, Yii::$app->session['idiomaId'], 'Guardar'), ['class' => 'btn btn-success']) ?>
    </div>
</div>

<?php ActiveForm::end();
		
function getNameInput($id){
	if($id == 1){
		return 0;
	}else{
		return $id;
	}
}
?>


<script>
var password = document.getElementById("usuariosapi-passw")
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
	
function openModal(idCatalogo, stringQry, valueField, textField, nombreCampo){
	$.ajax({
            url: '<?php  echo Url::to(['campos/getform']); ?>',
			type: "POST",
			data:"idCatalogo="+idCatalogo+"&qry="+stringQry+"&valueField="+valueField+"&textField="+textField+"&nombreCampo="+nombreCampo,
            success:function(response){
				$("#contenidoFormAdd").html(response);
				$('#modalFormAdd').modal('show');
            },
			error: function( jqXHR, textStatus, errorThrown ) {
				$("#contenidoFormAdd").html('<div class="text-center" style="padding-bottom:40px; padding-top:20px;"> Catálogo no encontrado, solicite a su administrador  agregar las configuraciones.</div>');
				$('#modalFormAdd').modal('show');
			}
    }).fail( function( jqXHR, textStatus, errorThrown ) {
			$("#contenidoFormAdd").html('<div class="text-center" style="padding-bottom:40px; padding-top:20px;"> Catálogo no encontrado, solicite a su administrador agregar las configuraciones.</div>');
			$('#modalFormAdd').modal('show');
	});
	
	
}
	
	
function send(form, urlPost, urlSelect, varQry, valueField, textField, nombreCampo){
	var data=$("#"+form).serialize();
	document.getElementById('formalerttrue').style.display = 'none';
	document.getElementById('formalertfalse').style.display = 'none';
	$.ajax({
   		type: 'POST',
    	url: urlPost,
   		data:data,
		success:function(data){
			document.getElementById('formalerttrue').style.display = 'block';
			document.getElementById(form).reset();
			$.ajax({
				type: 'POST',
				url: urlSelect,
				data:'qry='+varQry+"&valueField="+valueField+"&textField="+textField,
				success:function(result){
					  //aqui va la info
					$("#"+nombreCampo).html(result);
					//document.getElementById(nombreCampo).innerHTML = result;
				}
		  	})
        },
   		error: function(data) { // if error occured
			document.getElementById('formalertfalse').style.display = 'block';
    	},

  		dataType:'html'
  }).fail( function( jqXHR, textStatus, errorThrown ) {
		document.getElementById('formalertfalse').style.display = 'block';
  });

}
</script>

