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


$asset = AppAsset::register($this);
$baseUrl = $asset->baseUrl;
$this->registerJsFile($baseUrl.'/require/js/jquery/jquery-2.1.4.js', ['position' => \yii\web\View::POS_HEAD]);


$form = ActiveForm::begin([
        'options' => [
			'enctype' => 'multipart/form-data',
			'autocomplete'=>'nope'
        ]
    ]);
?>

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
<div class="col-4 float-left  pleft-0">
	<div class="form-group field-reportespdf-arrayCampos has-success">
		<label class="control-label" for="reportespdf-arrayCampos">Campos permitidos</label>
		<select id="arrayCode" class="form-control">
			<?php
			$htmlDecode = Html::decode($model->arrayCampos);
			$json = json_decode($htmlDecode, true); 
			echo '<option value=""> -- Selecciona -- </option>';
			if(is_array($json)){
				ksort($json);
				foreach ($json as $key => $value) {
					echo '<option value="{{'.$key.'}}">'.$key.'</option>';
				}
				echo '<option value="{{QR}}">QR</option>';
			}					
			?>				
		</select>
	</div>
</div>

<div class="col-4 float-left  pleft-0">
	<?php
		echo $form->field($model, 'altoHeader')->textInput(['type'=>'number', 'required'=>'required'])->label();
	?>
</div>

<div style="clear: both;"></div>
<div class="col-12 float-left  pleft-0">
<?php echo froala\froalaeditor\FroalaEditorWidget::widget([
   'model' => $model,
	'attribute' => 'headerReporte',
    'options' => [
        'id'=>'headerReporte',
    ],
    'clientOptions' => [
        'toolbarInline'=> false,
        'theme' =>'royal', //optional: dark, red, gray, royal
        'language'=>'es', // optional: ar, bs, cs, da, de, en_ca, en_gb, en_us ...
		'key'=> "1C%kZV[IX)_SL}UJHAEFZMUJOYGYQE[\\ZJ]RAe(+%$==",
		'attribution'=> false, // to hide "Powered by Froala"'
		'imageUploadParam' => 'file',
        'imageUploadURL' => \yii\helpers\Url::to(['reportespdf/uploadfile'])
    ]
]); ?>	
</div>


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
$( '#arrayCode' ).on('change', function(){
	//console.log($(this).val());
	copyToClipboard($(this).val());
	alertify.success('<span style="color: #FFFFFF;"><i class="fa fa-copy" aria-hidden="true"></i> &nbsp;&nbsp; Elemento copiado al portapapeles</span>', 2 , function (){
		//location.reload();
	}); 
});
	

function copyToClipboard(e) {
    var tempItem = document.createElement('input');

    tempItem.setAttribute('type','text');
    tempItem.setAttribute('display','none');
    
    let content = e;
    if (e instanceof HTMLElement) {
    	content = e.innerHTML;
    }
    
    tempItem.setAttribute('value',content);
    document.body.appendChild(tempItem);
    
    tempItem.select();
    document.execCommand('Copy');

    tempItem.parentElement.removeChild(tempItem);
}
	
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

