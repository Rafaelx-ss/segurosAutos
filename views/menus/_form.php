<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Menus */
/* @var $form yii\widgets\ActiveForm */
use app\assets\AppAsset;
use yii\helpers\Url;

use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

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
					 <i class="fa fa-check-square-o" aria-hidden="true"></i>  Registro guardado con exito! ('.Html::a("Ver menus", $url = ['menus/update&id='.$_GET['id']], $options = ['class'=>'']).')
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
	<?= $form->field($model, 'menuID')->textInput() ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'nombreMenu')->textInput(['maxlength' => true]) ?>
</div>


<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'orden')->textInput(['type'=>'number']) ?>
</div>


<div class="col-4 float-left  pleft-0">
	<?php
		echo $form->field($model, 'textoID',[
				'template' => 
				'{label}
				 <div class="input-group-append">							
					{input}
					<div class="input-group-append">
					<button class="btn btn-success btnAddform" type="button" onClick="openModal(\'2\', \'Select * from Textos\', \'textoID\', \'nombreTexto\', \'menus-textoid\')"><i class="fas fa-plus-square"></i></button>
					</div>
					</div>'
				])->widget(Select2::classname(), [                         
				'data' => ArrayHelper::map(Textos::find()->andWhere(['=', 'regEstado', '1'])->andWhere(['or', ['tipoTexto'=>'Menus'], ['tipoTexto'=>'default']])->orderBy(['textoID'=>SORT_DESC])->all(), 'textoID', 'nombreTexto'),
					'language' => 'es',
					'options' => ['placeholder' => ' --- Selecciona --- '],
					'pluginOptions' => ['allowClear' => true]]); 
	?>
</div>

<div style="clear: both;"></div>    
<div class="col-12  pleft-0">
    <div class="form-group">
		<br>		
        <?= Html::submitButton('<i class="pe-7s-diskette"></i> &nbsp;Guardar', ['class' => 'btn '.Yii::$app->globals->btnSave()]) ?>
		<?= $form->field($model, 'urlPagina')->textInput(['type'=>'hidden', 'value'=>'na'])->label(false) ?>
		<?= $form->field($model, 'imagen')->textInput(['type'=>'hidden', 'value'=>'na'])->label(false) ?>
		<?= $form->field($model, 'menuPadre')->textInput(['type'=>'hidden', 'value'=>0])->label(false) ?>
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

