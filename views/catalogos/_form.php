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
	<div class="form-group field-mformularios-menuid">
		<label class="control-label" for="mformularios-menuid">Menu</label>
		<select name="sel_menu" id="sel_menu" class="form-control" required>
			<option value=""> --- Selecciona --- </option>
			<?php
			$menus = Menus::find()->andWhere(['=', 'regEstado', '1'])->orderBy('nombreMenu')->all();
			foreach($menus as $rmenu){
				echo '<option value="'.$rmenu['menuID'].'"> '.$rmenu['nombreMenu'].' </option>';
			}
			?>
		</select>
	</div>
</div>

<div class="col-4 float-left  pleft-0">
	<div class="form-group field-catalogos-nombremodelo">
		<label class="control-label" for="catalogos-nombremodelo">Nombre Formulario</label>
		<input type="text" id="nombreFormulario" class="form-control" name="nombreFormulario" required>
	</div>
</div>

<div class="col-4 float-left  pleft-0">
<div class="form-group field-campos-textoid">
	<label class="control-label" for="campos-textoid">Texto menu Formulario</label>
	<div class="input-group-append">							
		<select name="sel_texto" id="campos-textoid" class="form-control" required>
			<option value=""> --- Selecciona --- </option>
			<?php
			$menus = Textos::find()->andWhere(['=', 'regEstado', '1'])->andWhere(['=', 'tipoTexto', 'Menus'])->orderBy('nombreTexto')->all();
			foreach($menus as $rmenu){
				echo '<option value="'.$rmenu['textoID'].'"> '.$rmenu['nombreTexto'].' </option>';
			}
			?>
		</select>					
		<div class="input-group-append">
			<button class="btn btn-success btnAddform" type="button" onclick="openModal('2', 'Select * from Textos where ', 'textoID', 'nombreTexto', 'campos-textoid')"><i class="fas fa-plus-square"></i></button>
		</div>
	</div>
</div>
</div>

<div class="col-4 float-left  pleft-0">
	<div class="form-group field-catalogos-nombremodelo">
		<label class="control-label" for="catalogos-nombremodelo">Orden Menu-Formulario</label>
		<input type="number" id="ordenFormulario" value="1" class="form-control" name="ordenFormulario" required>
	</div>
</div>

<div class="col-4 float-left " style="padding-left: 40px;">
	<div class="form-group field-catalogos-nombremodelo">
		<input type="checkbox" class="form-check-input" id="pcon" name="pcon" checked value="ok">
    	<label class="form-check-label" for="exampleCheck1">Consulta</label>
		<br>
		
		<input type="checkbox" class="form-check-input" id="palta" name="palta" checked value="ok">
    	<label class="form-check-label" for="exampleCheck1">Alta</label>
		<br>
		
		<input type="checkbox" class="form-check-input" id="pdel" name="pdel" checked value="ok">
    	<label class="form-check-label" for="exampleCheck1">Eliminar</label>
		<br>
		
		<input type="checkbox" class="form-check-input" id="pedit" name="pedit" checked value="ok">
    	<label class="form-check-label" for="exampleCheck1">Editar</label>
		<br>
		
		<input type="checkbox" class="form-check-input" id="pexcel" name="pexcel" checked value="ok">
    	<label class="form-check-label" for="exampleCheck1">Excel</label>
		<br>
		
		<input type="checkbox" class="form-check-input" id="ppdf" name="ppdf" checked value="ok">
    	<label class="form-check-label" for="exampleCheck1">PDF</label>
	</div>
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

<script>
$(document).ready(function(){
	$("#catalogos-nombrecatalogo").change(function(){
		var data = document.getElementById('catalogos-nombrecatalogo').value;
		var valueData = MaysPrimera(data.toLowerCase());
		
		document.getElementById('catalogos-nombremodelo').value = valueData;
		document.getElementById('nombreFormulario').value = valueData;
	}); 

}); 
	
	
function MaysPrimera(string){
  return string.charAt(0).toUpperCase() + string.slice(1);
}
	
	

function openModal(idCatalogo, stringQry, valueField, textField, nombreCampo){
	console.log("idCatalogo="+idCatalogo+"&qry="+stringQry+"&valueField="+valueField+"&textField="+textField+"&nombreCampo="+nombreCampo);
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
	var querySql = "Select * from Textos where tipoTexto='Menus' and regEstado= '1' order by textoID DESC";
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
				data:'qry='+querySql+"&valueField="+valueField+"&textField="+textField,
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