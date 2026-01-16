<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;

use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\Reportespdf */
/* @var $form yii\widgets\ActiveForm */

use app\assets\AppAsset;
use yii\helpers\Url;

use kartik\datetime\DateTimePicker;

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
	
	$cadenaDiv = explode('La combinaci',$form->errorSummary($model));
	if(isset($cadenaDiv[1])){
		$msjDuplicado = "La combinacion de registros ya existe en la base de datos";
		$btID = Yii::$app->globals->setBitacora("Registro duplicado", $msjDuplicado, 0, "", Yii::$app->user->identity->usuarioID, 1);
		Yii::$app->globals->setEvento(Yii::$app->user->identity->usuarioID, 10, 0, 1, $btID, $msjDuplicado);
	}
}else{
	if(isset($_GET['insert'])){
		if($_GET['insert'] == 'true'){
			echo '<div class="alert alert-success" role="alert">
					 <i class="fa fa-check-square-o" aria-hidden="true"></i>  '.Yii::$app->globals->getTraductor(8, Yii::$app->session['idiomaId'], 'Registro actualizado con exito').'! ('.Html::a(Yii::$app->globals->getTraductor(15, Yii::$app->session['idiomaId'], 'Ver')." reportespdf", $url = ['reportespdf/update&f='.$_GET['f'].'&id='.$_GET['id']], $options = ['class'=>'']).')
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
<?php 

$catalogo = Yii::$app->globals->getCatalogo('ReportesPdf'); 

if(isset($catalogo['catalogoID'])){	
	$campos = Yii::$app->globals->getCamposForm($catalogo['catalogoID']);

	foreach($campos as $rCampos){
		if($rCampos['visible'] == 1){		
			if($rCampos['tipoControl'] == 'text' or $rCampos['tipoControl'] == 'number' or $rCampos['tipoControl'] == 'date' or $rCampos['tipoControl'] == 'email' or $rCampos['tipoControl'] == 'password' or $rCampos['tipoControl'] == 'color'){
				echo '<div class="col-4 float-left  pleft-0">';
				if($rCampos['campoRequerido'] == 1){
					echo $form->field($model, $rCampos['nombreCampo'])->textInput(['type'=>$rCampos['tipoControl'], 'required'=>'required', 'oninvalid'=>"InvalidMsg(this, 'Usuarios', '".$rCampos['tipoControl']."', '".$rCampos['nombreCampo']."');"])->label(Yii::$app->globals->getTraductor(getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo']));
				}else{
					echo $form->field($model, $rCampos['nombreCampo'])->textInput(['type'=>$rCampos['tipoControl'], 'oninvalid'=>"InvalidMsg(this, 'Usuarios', '".$rCampos['tipoControl']."', '".$rCampos['nombreCampo']."');"])->label(Yii::$app->globals->getTraductor(getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo']));
				}

				echo '</div>';
			}elseif($rCampos['tipoControl'] == 'float'){
				echo '<div class="col-4 float-left  pleft-0">';
				if($rCampos['campoRequerido'] == 1){
					echo $form->field($model, $rCampos['nombreCampo'])->textInput(['type'=>'number', 'required'=>'required', 'step'=>'any', 'oninvalid'=>"InvalidMsg(this, 'Usuarios', '".$rCampos['tipoControl']."', '".$rCampos['nombreCampo']."');"])->label(Yii::$app->globals->getTraductor(getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo']));
				}else{
					echo $form->field($model, $rCampos['nombreCampo'])->textInput(['type'=>'number', 'step'=>'any', 'oninvalid'=>"InvalidMsg(this, 'Usuarios', '".$rCampos['tipoControl']."', '".$rCampos['nombreCampo']."');"])->label(Yii::$app->globals->getTraductor(getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo']));
				}

				echo '</div>';
			}elseif($rCampos['tipoControl'] == 'datetime'){
				echo '<div class="col-4 float-left  pleft-0">';
				if($rCampos['campoRequerido'] == 1){
					 echo $form->field($model, $rCampos['nombreCampo'])->widget(DateTimePicker::classname(), [
							'options' => ["autocomplete"=>"off", 'required'=>'required'],
							'pluginOptions' => [
								'autoclose'=>true,
								'format' => 'yyyy/mm/dd H:i',
								'todayHighlight' => true,
							]
					])->label(Yii::$app->globals->getTraductor(getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo']));
				}else{
					 echo $form->field($model, $rCampos['nombreCampo'])->widget(DateTimePicker::classname(), [
							'options' => ["autocomplete"=>"off"],
							'pluginOptions' => [
								'autoclose'=>true,
								'format' => 'yyyy/mm/dd H:i',
								'todayHighlight' => true,
							]
					])->label(Yii::$app->globals->getTraductor(getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo']));
				}
				echo '</div>';
			}elseif($rCampos['tipoControl'] == 'checkbox'){
				echo '<div class="col-4 float-left  pleft-0">';
					echo $form->field($model, $rCampos['nombreCampo'])->checkbox(['label' => Yii::$app->globals->getTraductor(getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo'])]);
				echo '</div>';
			}elseif($rCampos['tipoControl'] == 'textArea'){
				echo '<div style="clear: both;"></div><div class="col-12 float-left  pleft-0">';
					echo $form->field($model, $rCampos['nombreCampo'])->textarea(['value'=>Html::decode($model->arrayCampos),  'rows' => 50])->label();
				echo '</div><div style="clear: both;"></div>';
				
			}elseif($rCampos['tipoControl'] == 'select'){
				echo '<div class="col-4 float-left  pleft-0">';	
				echo $form->field($model, $rCampos['nombreCampo'],[
						'template' => 
						'{label}
						 <div class="input-group-append">							
							{input}
							<div class="input-group-append">
							<button class="btn btn-success btnAddform" type="button" onClick="openModal(\''.$rCampos['catalogoReferenciaID'].'\', \''.$rCampos['controlQuery'].'\', \''.$rCampos['valueField'].'\', \''.$rCampos['textField'].'\', \''.strtolower($catalogo['nombreModelo']).'-'.strtolower($rCampos['nombreCampo']).'\')"><i class="fas fa-plus-square"></i></button>
							</div>
						</div>'
					])->widget(Select2::classname(), [                         
						'data' => ArrayHelper::map(Yii::$app->db->createCommand($rCampos['controlQuery'])->queryAll(), $rCampos['valueField'], $rCampos['textField']),
						'language' => 'es',
						'options' => ['placeholder' => ' --- Selecciona --- '],
						'pluginOptions' => ['allowClear' => true]])->label(Yii::$app->globals->getTraductor(getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo'])); 
				echo '</div>';
			}elseif($rCampos['tipoControl'] == 'busqueda'){
				echo '<div class="col-4 float-left  pleft-0">';	
				$query_search = str_replace("?1", "'' or ".$rCampos['nombreCampo']."=".$model[$rCampos['nombreCampo']], $rCampos['controlQuery']);
				echo $form->field($model, $rCampos['nombreCampo'],[
						'template' => 
						'{label}
						 <div class="input-group-append">							
							{input}
							<div class="input-group-append">
							<button class="btn btn-success btnAddform" type="button" onClick="openModal(\''.$rCampos['catalogoReferenciaID'].'\', \''.$rCampos['controlQuery'].'\', \''.$rCampos['valueField'].'\', \''.$rCampos['textField'].'\', \''.strtolower($catalogo['nombreModelo']).'-'.strtolower($rCampos['nombreCampo']).'\')"><i class="fas fa-plus-square"></i></button>
							</div>
						</div>'
					])->widget(Select2::classname(), [ 
						'initValueText' => ArrayHelper::map(Yii::$app->db->createCommand($query_search)->queryAll(), 'id', 'text'),
						'language' => 'es',
						'options' => ['placeholder' => ' --- Selecciona --- '],
						'pluginOptions' => [
							'allowClear' => true,
							'minimumInputLength' => 2,
							'language' => [
								'errorLoading' => new JsExpression("function () { return 'Espera por un momento...'; }"),
							],
							'ajax' => [
								'url' => Url::to(['/reportespdf/getdatacombo']),
								'dataType' => 'json',
								'data' => new JsExpression('function(params) {return {q:params.term, campo:"'.$rCampos['nombreCampo'].'", consulta:"'.$rCampos['controlQuery'].'"}; }')
							],
							'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
							'templateResult' => new JsExpression('function(data) {return data.text; }'),
							'templateSelection' => new JsExpression('function (data) {  return data.text; }'),
						]
					
					])->label(Yii::$app->globals->getTraductor(getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo'])); 
				echo '</div>';
			}elseif($rCampos['tipoControl'] == 'dependiente'){
				
				$combo =  Yii::$app->globals->getFormDependientes($rCampos['catalogoID'], $rCampos['campoID']);
				
			 	$query_search = str_replace("'?'", "'".$model[$combo['nombreCampo']]."'", $combo['controlQuery']);

				if(!empty($combo['parametrosQuery']) and !is_null($combo['parametrosQuery'])){
					 $parametros = explode(",", $combo['parametrosQuery']);

					 $np = 1;
					 foreach($parametros as $rparam){
						 $query_search = str_replace("'?".$np."'", "'".$model[$rparam]."'", $query_search);
					 }
				 }


				$campos = Yii::$app->db->createCommand($query_search)->queryAll();
				
				echo '<div class="col-sm-4 float-left pleft-0">
						<div class="form-group field-siniestros-d_detalle required has-success">
							<label class="control-label" for="select_dependiente">'.Yii::$app->globals->getTraductor(getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo']).'</label>
							<select id="reportespdf-'.strtolower($rCampos['nombreCampo']).'" class="form-control" name="'.$catalogo['nombreModelo'].'['.$rCampos['nombreCampo'].']" required>';
								 echo '<option value=""> -- Selecciona -- </option>';
								 foreach($campos as $rowCampo){
									 if($model[$rCampos['nombreCampo']] == $rowCampo[$combo['queryValue']]){
										 echo '<option value="'.$rowCampo[$combo['queryValue']].'" selected>'.$rowCampo[$combo['queryText']].'</option>';
									 }else{
										 echo '<option value="'.$rowCampo[$combo['queryValue']].'">'.$rowCampo[$combo['queryText']].'</option>';
									 }				 
								 }
					echo '</select>	
						</div>				
					</div>';
			}elseif($rCampos['tipoControl'] == 'consulta'){
				echo '<div class="col-4 float-left  pleft-0">';	
				$inputVal = explode(",", $rCampos['valorDefault']);
				$valSel = 0;
				$textSel = 'No definido';
				if(isset($inputVal[0])){$valSel = $inputVal[0];}
				if(isset($inputVal[1])){$textSel = $inputVal[1];}
				echo $form->field($model, $rCampos['nombreCampo'])->widget(Select2::classname(), [                         
						'data' => ArrayHelper::map(Yii::$app->db->createCommand($rCampos['controlQuery'])->queryAll(), $valSel, $textSel),
						'language' => 'es',
						'options' => ['placeholder' => ' --- Selecciona --- '],
						'pluginOptions' => ['allowClear' => true]])->label(Yii::$app->globals->getTraductor(getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo'])); 
				echo '</div>';
			}elseif($rCampos['tipoControl'] == 'array'){
				$arrayDataCampo = array();
								
				$elementosArray = explode(",", $rCampos['valorDefault']);
				foreach($elementosArray as $rEa){
					$dataArray = explode(":", $rEa);
					if(isset($dataArray[0]) and isset($dataArray[1])){
						$arrayDataCampo[$dataArray[0]] = $dataArray[1];
					}
				}
								
				echo '<div class="col-4 float-left  pleft-0">';
				echo $form->field($model, $rCampos['nombreCampo'])->dropDownList($arrayDataCampo)->label(Yii::$app->globals->getTraductor(getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo']));
				echo '</div>';
								
			}elseif($rCampos['tipoControl'] == 'Autoincremental'){
				//no imprime el input
				echo '<div class="col-4 float-left  pleft-0">';
				echo $form->field($model, $rCampos['nombreCampo'])->textInput(['readonly'=>'readonly'])->label(Yii::$app->globals->getTraductor(getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo']));
				echo '</div>';
			}else{
				echo '<div class="col-4 float-left  pleft-0">';
				echo $form->field($model, $rCampos['nombreCampo'])->textInput(['oninvalid'=>"InvalidMsg(this, 'Usuarios', '".$rCampos['tipoControl']."', '".$rCampos['nombreCampo']."');"])->label(Yii::$app->globals->getTraductor(getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo']));
				echo '</div>';
			}
		}else{
			echo $form->field($model, $rCampos['nombreCampo'])->textInput(['type'=>'hidden'])->label(false);
		}
	}
}


if(isset($model->regEstado)){
	if($model->regEstado == 0){
		echo '<div class="col-4 float-left  pleft-0">';
			echo $form->field($model, 'regEstado')->checkbox(['label' => 'Activar registro eliminado']);
		echo '</div>';
		
	}
}

?>

<div style="clear: both;"></div>    
<div class="col-12  pleft-0">
    <div class="form-group">
		<br>
        <?= Html::submitButton('<i class="pe-7s-diskette"></i> &nbsp;'.Yii::$app->globals->getTraductor(14, Yii::$app->session['idiomaId'], 'Guardar'), ['class' => 'btn '.Yii::$app->globals->btnSave().' submitFormBtn']) ?>
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
	
$("#datepicker").datepicker();
var previousDate;

$("#datepicker").focus(function(){   
  previousDate= $(this).val(); ;
});
$("#datepicker").blur(function(){   
     var newDate = $(this).val();    
    if (!moment(newDate, 'YYYY/MM/DD', true).isValid()){         
        $(this).val(previousDate);      
        console.log("Error");
    }  
});
	
	
function InvalidMsg(textbox, modulo, tipo, nombre) {
    console.log("entro al invalid");
    if (textbox.value == '') {
       //valores vacios
		var mensaje = "El campo "+nombre+", intento de captura vacio";
		$.ajax({
			url:"<?php   echo Url::to(['usuarios/inputsave']); ?>",
			type: "GET",
			data:"modulo="+modulo+"&msj="+mensaje+"&tipoID=9",
			success: function(opciones){
					  
			}
		})
    }
    else if(textbox.validity.typeMismatch){
        //El tipo de dato ingresado es invalido
		 console.log("Tipo invalido");
		var mensaje = "El campo "+nombre+" es de tipo "+tipo+", intento de captura con un formato diferente";
		$.ajax({
			url:"<?php   echo Url::to(['usuarios/inputsave']); ?>",
			type: "GET",
			data:"modulo="+modulo+"&msj="+mensaje+"&tipoID=9",
			success: function(opciones){
					 console.log(opciones); 
			}
		})
    }
    else {
        textbox.setCustomValidity('');
    }
    return true;
}
</script>

