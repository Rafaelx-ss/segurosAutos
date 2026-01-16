<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Camposgrid */
/* @var $form yii\widgets\ActiveForm */
use app\assets\AppAsset;
use yii\helpers\Url;

use yii\helpers\ArrayHelper;
use kartik\select2\Select2;


use app\models\Textos;
use app\models\Catalogos;

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
					 <i class="fa fa-check-square-o" aria-hidden="true"></i>  Registro guardado con exito! ('.Html::a("Ver camposgrid", $url = ['camposgrid/update&id='.$_GET['id']], $options = ['class'=>'']).')
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
	<label class="control-label" for="campos-nombrecampo">Nombre Campo</label>
	<input class="form-control" type="text" id="name" name="name" value="<?php echo $model->nombreCampo; ?>" readonly>
</div>

<div class="col-sm-4 float-left pleft-0">  
	<?= $form->field($model, 'tipoControl')->dropDownList(['Autoincremental'=>'Autoincremental', 'text'=>'Texto', 'number'=>'Numero', 'date'=>'Fecha', 'datetime'=>'Fecha y hora', 'email'=>'Correo', 'password'=>'Contraseña', 'checkbox'=>'Check',  'color'=>'color', 'select'=>'Select', 'consulta'=>'select consulta', 'textArea'=>'Area Texto']) ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?php
		echo $form->field($model, 'textoID',[
				'template' => 
				'{label}
				 <div class="input-group-append">							
					{input}
					<div class="input-group-append">
					<button class="btn btn-success btnAddform" type="button" onClick="openModal(\'2\', \'Select * from Textos\', \'textoID\', \'nombreTexto\', \'camposgrid-textoid\')"><i class="fas fa-plus-square"></i></button>
					</div>
					</div>'
				])->widget(Select2::classname(), [                         
				'data' => ArrayHelper::map(Textos::find()->andWhere(['=', 'regEstado', '1'])->andWhere(['or', ['tipoTexto'=>'Catalogos'], ['tipoTexto'=>'default']])->orderBy(['textoID'=>SORT_DESC])->all(), 'textoID', 'nombreTexto'),
					'language' => 'es',
					'options' => ['placeholder' => ' --- Selecciona --- '],
					'pluginOptions' => ['allowClear' => true]]); 
	?>
</div>


<?php
$styleBlock1 = 'style="display:none;"';
$styleBlock2 = 'style="display:none;"';
$styleBlock3 = 'style="display:none;"';
$styleBlock4 = 'style="display:none;"';
$styleBlock5 = 'style="display:none;"';
$styleBlock6 = 'style="display:none;"';
	
if($model->tipoControl == 'select' or $model->tipoControl == 'consulta'){
	$styleBlock = 'style="display:none;"';
	if($model->tipoControl == 'select'){
		$styleBlock1 = 'style="display:block;"';
		$styleBlock2 = 'style="display:block;"';
		$styleBlock3 = 'style="display:block;"';
		
		//consulta
		$styleBlock4 = 'style="display:none;"';
		$styleBlock5 = 'style="display:none;"';
		$styleBlock6 = 'style="display:none;"';
	
	}elseif($model->tipoControl == 'consulta'){
		//select
		$styleBlock1 = 'style="display:block;"';
		$styleBlock2 = 'style="display:block;"';
		$styleBlock3 = 'style="display:block;"';
		
		//consulta
		$styleBlock4 = 'style="display:block;"';
		$styleBlock5 = 'style="display:block;"';
		$styleBlock6 = 'style="display:block;"';
	}else{
		$styleBlock1 = 'style="display:none;"';
		$styleBlock2 = 'style="display:none;"';
		$styleBlock3 = 'style="display:none;"';
		$styleBlock4 = 'style="display:none;"';
		$styleBlock5 = 'style="display:none;"';
		$styleBlock6 = 'style="display:none;"';
	}
}


?>
<div class="col-sm-4 float-left pleft-0" id="select_div1" <?= $styleBlock1 ?>>   
	<?php 
		echo $form->field($model, 'catalogoReferenciaID')->dropDownList(ArrayHelper::map(Catalogos::find()->andWhere(['=', 'regEstado', '1'])->orderBy('nombreCatalogo')->all(), 'catalogoID', 'nombreCatalogo'));			
	?>
</div>


<?php
	$arrayDat = array();
	if($model->regEstado != '' and $model->catalogoReferenciaID != 1){
		$catalogo = Yii::$app->db->createCommand("SELECT nombreCatalogo FROM Catalogos where catalogoID='".$model->catalogoReferenciaID."'")->queryOne();
		if(isset($catalogo['nombreCatalogo'])){
			$table = Yii::$app->db->getTableSchema($catalogo['nombreCatalogo']);	
			
			//echo $model->catalogoReferenciaID;
			
			foreach($table->columns as $row){
				if($row->name == 'regEstado' or $row->name == 'regFechaUltimaModificacion' or $row->name == 'regUsuarioUltimaModificacion' or $row->name == 'versionRegistro' or $row->name == 'regVersionUltimaModificacion' or $row->name == 'regFormularioUltimaModificacion'){	}else{
					$arrayDat[$row->name] = $row->name;
				}			
			}
			
		}		
	}							
	?>
	<div class="col-4 float-left  pleft-0" id="select_div2" <?= $styleBlock2 ?>>		
		<?= $form->field($model, 'textField')->dropDownList($arrayDat) ?>
	</div>

	<div class="col-4 float-left  pleft-0" id="select_div3" <?= $styleBlock3 ?>>
		<?= $form->field($model, 'valueField')->dropDownList($arrayDat) ?>
	</div>



<div class="col-4 float-left  pleft-0" <?= $styleBlock4 ?> id="select_div5">
	<?= $form->field($model, 'controlQuery')->textarea(['rows' => 2])->label('ControlQuery <i style="color:#b98f2a; font-size:20px;" class="pe-7s-info" data-toggle="tooltip"  data-placement="top" data-original-title="Consulta SQL en el index para mostrar el texto, funciona en conjunto con queryValor ejemplo: select nombreCampoReturn from tabla where nombreCampo=?1 and nombreCampo=?2. donde los signos y los numeros incrementales son los valores de queryvalor separados por coma."></i>') ?>
</div>

<div class="col-4 float-left  pleft-0" <?= $styleBlock4 ?> id="select_div51">
	<?= $form->field($model, 'searchQuery')->textarea(['rows' => 2])->label('SearchQuery <i style="color:#b98f2a; font-size:20px;" class="pe-7s-info" data-toggle="tooltip"  data-placement="top" data-original-title="Consulta SQL funciona cuando se realiza la busqueda en el modelo, ejemplo: Select IDCampo from tabla where concat(descripcion,\' \',usuario,\' \',aliasEstablecimiento) like \'%?1%\' group by IDCampo"></i>') ?>
</div>

<div class="col-4 float-left  pleft-0" <?= $styleBlock5 ?> id="select_div6">
	<?= $form->field($model, 'queryValor')->textInput()->label('Parametros Query <i style="color:#b98f2a; font-size:20px;" class="pe-7s-info" data-toggle="tooltip"  data-placement="top" data-original-title="Nombre de los campos que van a tomar el valor en controlQuery ejemplo: nombreCampo=?1 and nombreCampo=?2. nombreCampo1,nombreCampo2"></i>') ?>
</div>

<div class="col-4 float-left  pleft-0" <?= $styleBlock6 ?> id="select_div7">
	<?= $form->field($model, 'valorDefault')->textInput()->label('Valores Defaul <i style="color:#b98f2a; font-size:20px;" class="pe-7s-info" data-toggle="tooltip"  data-placement="top" data-original-title="Campo a retornar en el ejemplo de controlQuery nombreCampoReturn"></i>') ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'orden')->textInput() ?>
</div>


<div class="col-2 float-left  pleft-0">
	<?= $form->field($model, 'visible')->checkbox() ?>
</div>

<div class="col-2 float-left  pleft-0">
	<?= $form->field($model, 'searchVisible')->checkbox() ?>
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

	$("#camposgrid-tipocontrol").change(function(){
		var data = document.getElementById('camposgrid-tipocontrol').value;
		console.log("debe de llegar "+data);
		if(data == 'select'){
			document.getElementById('select_div1').style.display = 'block';
			document.getElementById('select_div2').style.display = 'block';
			document.getElementById('select_div3').style.display = 'block';
			
			//consulta
			document.getElementById('select_div5').style.display = 'none';
			document.getElementById('select_div51').style.display = 'none';
			document.getElementById('select_div6').style.display = 'none';
			document.getElementById('select_div7').style.display = 'none';
		}else if(data == 'consulta'){
			//select 
			document.getElementById('select_div1').style.display = 'block';
			document.getElementById('select_div2').style.display = 'block';
			document.getElementById('select_div3').style.display = 'block';
			
			//consulta
			document.getElementById('select_div5').style.display = 'block';
			document.getElementById('select_div51').style.display = 'block';
			document.getElementById('select_div6').style.display = 'block';
			document.getElementById('select_div7').style.display = 'block';
		}else{
			//select
			document.getElementById('select_div1').style.display = 'none';
			document.getElementById('select_div2').style.display = 'none';
			document.getElementById('select_div3').style.display = 'none';
			
			//consulta
			document.getElementById('select_div5').style.display = 'none';
			document.getElementById('select_div51').style.display = 'none';
			document.getElementById('select_div6').style.display = 'none';
			document.getElementById('select_div7').style.display = 'none';
		}
	}); 
	
	$("#camposgrid-catalogoreferenciaid").change(function(){
		var data = document.getElementById('camposgrid-catalogoreferenciaid').value;
		
		$.ajax({
            url: '<?php echo Url::to(['catalogos/getcampos']); ?>',
			type: "POST",
			data:"idRel="+data,
            success:function(response){
				document.getElementById('camposgrid-textfield').innerHTML = response;
				document.getElementById('camposgrid-valuefield').innerHTML = response;
            }
       });
	}); 

}); 
</script>
