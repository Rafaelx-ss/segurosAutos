<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Campos */
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
					 <i class="fa fa-check-square-o" aria-hidden="true"></i>  Registro guardado con exito! ('.Html::a("Ver campos", $url = ['campos/update&id='.$_GET['id']], $options = ['class'=>'']).')
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
	<?= $form->field($model, 'tipoControl')->dropDownList(['Autoincremental'=>'Autoincremental', 'text'=>'Texto', 'float'=>'Float', 'number'=>'Numero', 'date'=>'Fecha', 'datetime'=>'Fecha y hora', 'email'=>'Correo', 'password'=>'Contraseña', 'checkbox'=>'Check', 'color'=>'color', 'select'=>'Select', 'dependiente'=>'Select dependiente', 'consulta'=>'select consulta', 'busqueda'=>'select busqueda', 'array'=>'Select array', 'textArea'=>'Area Texto']) ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'longitud')->textInput(['maxlength' => true]) ?>
</div>

<?php
$styleBlock1 = 'style="display:none;"';
$styleBlock2 = 'style="display:none;"';
$styleBlock3 = 'style="display:none;"';
$styleBlock4 = 'style="display:none;"';
	
if($model->tipoControl == 'select' or $model->tipoControl == 'consulta' or $model->tipoControl == 'busqueda'){
	$styleBlock = 'style="display:none;"';
	if($model->tipoControl == 'select'){
		$styleBlock1 = 'style="display:block;"';
		$styleBlock2 = 'style="display:block;"';
		$styleBlock3 = 'style="display:block;"';
		$styleBlock4 = 'style="display:block;"';
	}elseif($model->tipoControl == 'consulta' or $model->tipoControl == 'busqueda'){
		$styleBlock1 = 'style="display:block;"';
		$styleBlock2 = 'style="display:none;"';
		$styleBlock3 = 'style="display:none;"';
		$styleBlock4 = 'style="display:none;"';
	}else{
		$styleBlock1 = 'style="display:none;"';
		$styleBlock2 = 'style="display:none;"';
		$styleBlock3 = 'style="display:none;"';
		$styleBlock4 = 'style="display:none;"';
	}
}
?>
<div class="col-4 float-left  pleft-0" id="divBlock1" <?= $styleBlock1 ?>>
	<?= $form->field($model, 'controlQuery')->textarea(['rows' => 4])->label('ControlQuery <i style="color:#b98f2a; font-size:20px;" class="pe-7s-info" data-toggle="tooltip"  data-placement="top" data-original-title="Select busqueda ejemplo SELECT claveSubProductoEnvioSATID as id, claveSubProductoEnvioSAT as text FROM clavessubproductosenviossat WHERE claveSubProductoEnvioSAT LIKE ?1 LIMIT 20"></i>') ?>
</div>

	
	<div class="col-sm-4 float-left pleft-0" id="divBlock2" <?= $styleBlock2 ?>>   
		<?php 
			echo $form->field($model, 'catalogoReferenciaID')->dropDownList(ArrayHelper::map(Catalogos::find()->andWhere(['=', 'regEstado', '1'])->orderBy('nombreCatalogo')->all(), 'catalogoID', 'nombreCatalogo'));			
		?>
	</div>

	<?php
	$arrayDat = array();
	if($model->regEstado != '' and $model->catalogoReferenciaID != 1){
		$catalogo = Yii::$app->db->createCommand("SELECT nombreCatalogo FROM Catalogos where catalogoID='".$model->catalogoReferenciaID."'")->queryOne();
		if(isset($catalogo['nombreCatalogo'])){
			$table = Yii::$app->db->getTableSchema(trim($catalogo['nombreCatalogo']));			
			foreach($table->columns as $row){
				if($row->name == 'regEstado' or $row->name == 'regFechaUltimaModificacion' or $row->name == 'regUsuarioUltimaModificacion' or $row->name == 'versionRegistro' or $row->name == 'regVersionUltimaModificacion' or $row->name == 'regFormularioUltimaModificacion'){	}else{
					$arrayDat[$row->name] = $row->name;
				}			
			}	
		}		
	}							
	?>
	<div class="col-4 float-left  pleft-0" id="divBlock3" <?= $styleBlock3 ?>>
		<?= $form->field($model, 'textField')->dropDownList($arrayDat) ?>
	</div>
	
	<div class="col-4 float-left  pleft-0" id="divBlock4" <?= $styleBlock4 ?>>
		<?= $form->field($model, 'valueField')->dropDownList($arrayDat) ?>
	</div>


<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'valorDefault')->textInput(['maxlength' => true])->label('Valor Default <i style="color:#b98f2a; font-size:20px;" class="pe-7s-info" data-toggle="tooltip" data-html="true"  data-placement="top" data-original-title="Caso 1 check: para activo el valor es 1<br> caso 2 select array: A:Activo,N:No activo<br> caso 3 select consulta: campoValor,campoTexto <br> en los otros casos ingresar el valor que se desee"></i>') ?>
</div>



<div class="col-4 float-left  pleft-0">
	<?php
		echo $form->field($model, 'textoID',[
				'template' => 
				'{label}
				 <div class="input-group-append">							
					{input}
					<div class="input-group-append">
					<button class="btn btn-success btnAddform" type="button" onClick="openModal(\'2\', \'Select * from Textos\', \'textoID\', \'nombreTexto\', \'campos-textoid\')"><i class="fas fa-plus-square"></i></button>
					</div>
					</div>'
				])->widget(Select2::classname(), [                         
				'data' => ArrayHelper::map(Textos::find()->andWhere(['=', 'regEstado', '1'])->andWhere(['or', ['tipoTexto'=>'Catalogos'], ['tipoTexto'=>'default']])->orderBy(['textoID'=>SORT_DESC])->all(), 'textoID', 'nombreTexto'),
					'language' => 'es',
					'options' => ['placeholder' => ' --- Selecciona --- '],
					'pluginOptions' => ['allowClear' => true]]); 
	?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'orden')->textInput(['maxlength' => true]) ?>
</div>


<div class="col-3 float-left  pleft-0">
	<?= $form->field($model, 'campoRequerido')->checkbox() ?>
</div>

<div class="col-3 float-left  pleft-0">
	<?= $form->field($model, 'visible')->checkbox() ?>
</div>


<div style="clear: both;"></div>    
<div class="col-12  pleft-0">
    <div class="form-group">
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

	$("#campos-tipocontrol").change(function(){
		var data = document.getElementById('campos-tipocontrol').value;
		if(data == 'select' ||  data == 'consulta' || data == 'busqueda'){
			
			if(data == 'select'){
				document.getElementById('divBlock1').style.display = 'block';
				document.getElementById('divBlock2').style.display = 'block';
				document.getElementById('divBlock3').style.display = 'block';
				document.getElementById('divBlock4').style.display = 'block';
			}else if(data == 'consulta' || data == 'busqueda'){
				document.getElementById('divBlock1').style.display = 'block';
				document.getElementById('divBlock2').style.display = 'none';
				document.getElementById('divBlock3').style.display = 'none';
				document.getElementById('divBlock4').style.display = 'none';
			}else{
				document.getElementById('divBlock1').style.display = 'none';
				document.getElementById('divBlock2').style.display = 'none';
				document.getElementById('divBlock3').style.display = 'none';
				document.getElementById('divBlock4').style.display = 'none';
			}
		}else{
			document.getElementById('divBlock1').style.display = 'none';
			document.getElementById('divBlock2').style.display = 'none';
			document.getElementById('divBlock3').style.display = 'none';
			document.getElementById('divBlock4').style.display = 'none';
		}
	}); 
	
	$("#campos-catalogoreferenciaid").change(function(){
		var data = document.getElementById('campos-catalogoreferenciaid').value;
		var nameTabla = $('#campos-catalogoreferenciaid option:selected').text();
		
		document.getElementById('campos-controlquery').innerHTML = "SELECT * FROM "+nameTabla;
		
		$.ajax({
            url: '<?php echo Url::to(['catalogos/getcampos']); ?>',
			type: "POST",
			data:"idRel="+data,
            success:function(response){
				document.getElementById('campos-textfield').innerHTML = response;
				document.getElementById('campos-valuefield').innerHTML = response;
            }
       });
	}); 

}); 
</script>

