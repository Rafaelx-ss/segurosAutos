<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

use app\assets\AppAsset;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Reportescampos */
/* @var $form yii\widgets\ActiveForm */


use app\models\Textos;
$asset = AppAsset::register($this);
$baseUrl = $asset->baseUrl;

$this->registerJsFile($baseUrl.'/require/js/jquery/jquery-2.1.4.js', ['position' => \yii\web\View::POS_HEAD]);

$styleBlock1 = 'style="display:none;"';
$styleBlock2 = 'style="display:none;"';
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
		
	if(isset($_GET['update'])){
		if($_GET['update'] == 'true'){
			echo '<div class="alert alert-success" role="alert">
					 <i class="fa fa-check-square-o" aria-hidden="true"></i>  Registro actualizado con exito!
				 </div>';
		}
	}
}
	
	
if($model->tipoControl == 'select' or $model->tipoControl == 'array'){
	$styleBlock = 'style="display:none;"';
	if($model->tipoControl == 'select'){
		$styleBlock1 = 'style="display:block;"';
		$styleBlock2 = 'style="display:block;"';
	}elseif($model->tipoControl == 'array'){
		$styleBlock1 = 'style="display:none;"';
		$styleBlock2 = 'style="display:block;"';
	}else{
		$styleBlock1 = 'style="display:none;"';
		$styleBlock2 = 'style="display:none;"';
	}
}
?>
</div>
<div style="clear: both;"></div>


<div class="col-sm-4 float-left pleft-0">  
	<?= $form->field($model, 'tipoControl')->dropDownList(['text'=>'Texto', 'float'=>'Float', 'number'=>'Numero', 'date'=>'Fecha', 'datetime'=>'Fecha y hora', 'checkbox'=>'Check', 'select'=>'Select', 'array'=>'Select array']) ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'aliasTabla')->textInput() ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'orden')->textInput() ?>
</div>


<div class="col-4 float-left  pleft-0">
	<?php
		echo $form->field($model, 'textoID',[
				'template' => 
				'{label}
				 <div class="input-group-append">							
					{input}
					<div class="input-group-append">
					<button class="btn btn-success btnAddform" type="button" onClick="openModal(\'2\', \'Select * from Textos\', \'textoID\', \'nombreTexto\', \'reportescampos-textoid\')"><i class="fas fa-plus-square"></i></button>
					</div>
					</div>'
				])->widget(Select2::classname(), [                         
				'data' => ArrayHelper::map(Textos::find()->andWhere(['=', 'regEstado', '1'])->andWhere(['or', ['tipoTexto'=>'Catalogos'], ['tipoTexto'=>'default']])->orderBy(['textoID'=>SORT_DESC])->all(), 'textoID', 'nombreTexto'),
					'language' => 'es',
					'options' => ['placeholder' => ' --- Selecciona --- '],
					'pluginOptions' => ['allowClear' => true]]); 
	?>
</div>


<div class="col-4 float-left  pleft-0" id="divBlock1" <?= $styleBlock1 ?>>
	<?= $form->field($model, 'controlQuery')->textarea(['rows' => 6]) ?>
</div>

<div class="col-4 float-left  pleft-0" id="divBlock2" <?= $styleBlock2 ?>>
	<?= $form->field($model, 'queryValor')->textInput(['maxlength' => true])->label('Valor Default <i style="color:#b98f2a; font-size:20px;" class="pe-7s-info" data-toggle="tooltip" data-html="true"  data-placement="top" data-original-title="select array: S:Si,N:No<br> select: campoValor,campoTexto"></i>') ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'visible')->checkbox() ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'searchVisible')->checkbox() ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'sumarCampo')->checkbox() ?>
</div>

<div style="clear: both;"></div>    
<div class="col-12  pleft-0">
    <div class="form-group">
		<br>
        <?= Html::submitButton('<i class="pe-7s-diskette"></i> &nbsp;'.Yii::$app->globals->getTraductor(14, Yii::$app->session['idiomaId'], 'Guardar'), ['class' => 'btn '.Yii::$app->globals->btnSave()]) ?>
    </div>
</div>

 <?php ActiveForm::end(); ?>

<script>
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
	var querySql = "Select * from Textos where tipoTexto='Catalogos' and regEstado= '1' order by textoID DESC";
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

	
	
$(document).ready(function(){

	$("#reportescampos-tipocontrol").change(function(){
		var data = document.getElementById('reportescampos-tipocontrol').value;
		//console.log(data);
		if(data == 'select' ||  data == 'array'){
			
			if(data == 'select'){
				document.getElementById('divBlock1').style.display = 'block';
				document.getElementById('divBlock2').style.display = 'block';
			}else if(data == 'array'){
				document.getElementById('divBlock1').style.display = 'none';
				document.getElementById('divBlock2').style.display = 'block';
			}else{
				document.getElementById('divBlock1').style.display = 'none';
				document.getElementById('divBlock2').style.display = 'none';
			}
		}else{
			document.getElementById('divBlock1').style.display = 'none';
			document.getElementById('divBlock2').style.display = 'none';
		}
	}); 
	
	

}); 
</script>
