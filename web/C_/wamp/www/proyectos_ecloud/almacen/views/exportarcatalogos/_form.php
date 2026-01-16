<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\ExportarCatalogos */
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
					 <i class="fa fa-check-square-o" aria-hidden="true"></i>  '.Yii::$app->globals->getTraductor(8, Yii::$app->session['idiomaId'], 'Registro actualizado con exito').'! ('.Html::a(Yii::$app->globals->getTraductor(15, Yii::$app->session['idiomaId'], 'Ver')." exportar-catalogos", $url = ['exportar-catalogos/update&f='.$_GET['f'].'&id='.$_GET['id']], $options = ['class'=>'']).')
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
$catalogo = Yii::$app->db->createCommand("SELECT * FROM Catalogos where regEstado='1' and nombreCatalogo='ExportarCatalogos'")->queryOne();

if(isset($catalogo['catalogoID'])){	
	$campos = Yii::$app->db->createCommand("SELECT * FROM Campos where regEstado='1' and catalogoID='".$catalogo['catalogoID']."' order by orden ASC")->queryAll();

	foreach($campos as $rCampos){
		if($rCampos['visible'] == 1){		
			if($rCampos['tipoControl'] == 'text' or $rCampos['tipoControl'] == 'number' or $rCampos['tipoControl'] == 'date' or $rCampos['tipoControl'] == 'email' or $rCampos['tipoControl'] == 'password' or $rCampos['tipoControl'] == 'color'){
				echo '<div class="col-4 float-left  pleft-0">';
				if($rCampos['campoRequerido'] == 1){
					echo $form->field($model, $rCampos['nombreCampo'])->textInput(['type'=>$rCampos['tipoControl'], 'required'=>'required'])->label(Yii::$app->globals->getTraductor(getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo']));
				}else{
					echo $form->field($model, $rCampos['nombreCampo'])->textInput(['type'=>$rCampos['tipoControl']])->label(Yii::$app->globals->getTraductor(getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo']));
				}

				echo '</div>';
			}elseif($rCampos['tipoControl'] == 'checkbox'){
				echo '<div class="col-4 float-left  pleft-0">';
					echo $form->field($model, $rCampos['nombreCampo'])->checkbox(['label' => Yii::$app->globals->getTraductor(getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo'])]);
				echo '</div>';
			}elseif($rCampos['tipoControl'] == 'textArea'){
				echo '<div class="col-4 float-left  pleft-0">';
					echo $form->field($model, $rCampos['nombreCampo'])->textarea(['rows' => 4])->label(Yii::$app->globals->getTraductor(getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo']));
				echo '</div>';
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
			}elseif($rCampos['tipoControl'] == 'Autoincremental'){
				//no imprime el input
				echo '<div class="col-4 float-left  pleft-0">';
				echo $form->field($model, $rCampos['nombreCampo'])->textInput(['readonly'=>'readonly'])->label(Yii::$app->globals->getTraductor(getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo']));
				echo '</div>';
			}else{
				echo '<div class="col-4 float-left  pleft-0">';
				echo $form->field($model, $rCampos['nombreCampo'])->textInput()->label(Yii::$app->globals->getTraductor(getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo']));
				echo '</div>';
			}
		}else{
			echo $form->field($model, $rCampos['nombreCampo'])->textInput(['value'=>$rCampos['valorDefault'], 'type'=>'hidden'])->label(false);
		}
	}
}
?>

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

