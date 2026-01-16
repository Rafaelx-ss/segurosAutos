<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

use app\assets\AppAsset;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Combos */
/* @var $form yii\widgets\ActiveForm */
$asset = AppAsset::register($this);
$baseUrl = $asset->baseUrl;

$this->registerJsFile($baseUrl.'/require/js/jquery/jquery-2.1.4.js', ['position' => \yii\web\View::POS_HEAD]);

use app\models\Campos;
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
					 <i class="fa fa-check-square-o" aria-hidden="true"></i>  Registro guardado con exito! ('.Html::a("Ver combos", $url = ['combos/update&token='.$_GET['token'].'&id='.$_GET['id']], $options = ['class'=>'']).')
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
		echo $form->field($model, 'campoIDPadre')->widget(Select2::classname(), [                         
			'data' => ArrayHelper::map(Campos::find()->andWhere(['=', 'regEstado', 1])->andWhere(['=', 'catalogoID', $model->catalogoID])->orderBy('nombreCampo')->all(), 'campoID', 'nombreCampo'),
			'language' => 'es',
			'options' => ['placeholder' => ' --- Selecciona --- ', 'required'=>'required'],
			'pluginOptions' => ['allowClear' => true]]); 	
	?>
</div>
<div class="col-sm-4 float-left pleft-0">    
	<?php 
		echo $form->field($model, 'campoIDdependiente')->widget(Select2::classname(), [                         
			'data' => ArrayHelper::map(Campos::find()->andWhere(['=', 'regEstado', 1])->andWhere(['=', 'catalogoID', $model->catalogoID])->orderBy('nombreCampo')->all(), 'campoID', 'nombreCampo'),
			'language' => 'es',
			'options' => ['placeholder' => ' --- Selecciona --- '],
			'pluginOptions' => ['allowClear' => true]]); 	
	?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'parametrosQuery')->textInput()->label('Control Query <i style="color:#b98f2a; font-size:20px;" class="pe-7s-info" data-toggle="tooltip" data-html="true"  data-placement="top" data-original-title="NombreCampoID,N2,N3"></i>') ?>
</div>

<div style="clear: both;"></div>
<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'controlQuery')->textarea(['rows' => 6])->label('Control Query <i style="color:#b98f2a; font-size:20px;" class="pe-7s-info" data-toggle="tooltip" data-html="true"  data-placement="top" data-original-title="Select valor, texto from tabla where IdRelacionado = \'?\' and NombreCampoID=\'?1\' and N2=\'?2\' and N3=\'?3\'"></i>') ?>
</div>

<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'queryValue')->textInput(['maxlength' => true]) ?>
</div>


<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'queryText')->textInput(['maxlength' => true]) ?>
</div>


<div class="col-4 float-left  pleft-0">
	<?= $form->field($model, 'activoCombo')->checkbox(['checked'=>'checked']) ?>
</div>

<div style="clear: both;"></div>    
<div class="col-12  pleft-0">
    <div class="form-group">
		<br>
        <?= Html::submitButton('<i class="pe-7s-diskette"></i> &nbsp;'.Yii::$app->globals->getTraductor(14, Yii::$app->session['idiomaId'], 'Guardar'), ['class' => 'btn '.Yii::$app->globals->btnSave()]) ?>
    </div>
</div>

 <?php ActiveForm::end(); ?>
