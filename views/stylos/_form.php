<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

use app\assets\AppAsset;
use yii\helpers\Url;


$asset = AppAsset::register($this);
$baseUrl = $asset->baseUrl; 

/* @var $this yii\web\View */
/* @var $model app\models\Stylos */
/* @var $form yii\widgets\ActiveForm */
?>



 <?php $form = ActiveForm::begin([
        'options' => [
			'enctype' => 'multipart/form-data',
            'onsubmit' => 'cargando()'
        ]
    ]); ?>

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
					 <i class="fa fa-check-square-o" aria-hidden="true"></i>  Registro guardado con exito! ('.Html::a("Ver stylos", $url = ['stylos/update&id='.$_GET['id']], $options = ['class'=>'']).')
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
<div id="cargando" style="display:none;  color: green; font-size:12px; text-align: center;">
	<img src="<?php echo $baseUrl;?>/require/images/loader.gif" alt="cargando" /> (No cierre ni refresque la pagina hasta finalizar el proceso)
	<br>
</div>
<div style="clear: both;"></div>


<div class="col-6 float-left">	
	
	
	<?= $form->field($model, 'titlePagina')->textInput(['required'=>'required']) ?>
	<?= $form->field($model, 'tiempoSesion')->textInput(['required'=>'required']) ?>
	<?= $form->field($model, 'footerPagina')->textInput(['required'=>'required']) ?>
	
	<label class="control-label" for="stylos-temamenu">Tema Menu</label><br>
	<?php
	$arrayMenu = array('bg-primary', 'bg-midnight-bloom', 'bg-info', 'bg-danger', 'bg-dark', 'bg-night-sky', 'bg-warning', 'bg-success', 'bg-alternate', 'bg-light', 'bg-asteroid', 'bg-warm-flame', 'bg-night-fade', 'bg-sunny-morning', 'bg-tempting-azure', 'bg-amy-crisp', 'bg-heavy-rain', 'bg-mean-fruit', 'bg-malibu-beach', 'bg-deep-blue', 'bg-ripe-malin', 'bg-arielle-smile', 'bg-plum-plate', 'bg-happy-fisher', 'bg-happy-itmeo', 'bg-mixed-hopes', 'bg-strong-bliss', 'bg-grow-early', 'bg-premium-dark', 'bg-happy-green', 'bg-love-kiss');
	$actMenu = '';
	foreach($arrayMenu as $amenu){
		if($model->temaBanner == $amenu){ $actMenu = 'checked'; }else{ $actMenu = '';}
		echo  '<div class="'.$amenu.'" style="width: 50px; height: 20px; margin-right: 5px; margin-bottom: 5px;  float: left;  border-radius: 5px;"> <input type="radio" name="Stylos[temaBanner]" value="'.$amenu.'" '.$actMenu.' required style="height: 20px; width: 20px;" /></div>';
	}
	
	?>
	<div style="clear: both;"></div>
		
	<label class="control-label" for="stylos-temaContenido">Tema Contenido</label><br>
	<?php
	$arrayCont = array('bg-primary', 'bg-secondary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger', 'bg-light', 'bg-dark', 'bg-focus', 'bg-alternate', 'bg-vicious-stance', 'bg-midnight-bloom', 'bg-night-sky', 'bg-slick-carbon', 'bg-asteroid', 'bg-royal', 'bg-warm-flame', 'bg-night-fade', 'bg-sunny-morning', 'bg-tempting-azure', 'bg-amy-crisp', 'bg-heavy-rain', 'bg-mean-fruit', 'bg-malibu-beach', 'bg-deep-blue', 'bg-ripe-malin', 'bg-arielle-smile', 'bg-plum-plate', 'bg-happy-fisher', 'bg-happy-itmeo', 'bg-mixed-hopes', 'bg-strong-bliss', 'bg-grow-early', 'bg-love-kiss', 'bg-premium-dark', 'bg-happy-green');
	$actMenu = '';
	foreach($arrayCont as $acont){
		if($model->temaMenu == $acont){ $actMenu = 'checked'; }else{ $actMenu = '';}
		echo  '<div class="'.$acont.'" style="width: 50px; height: 20px; margin-right: 5px; margin-bottom: 5px;  float: left;  border-radius: 5px;"> <input type="radio" name="Stylos[temaMenu]" value="'.$acont.'" '.$actMenu.' required style="height: 20px; width: 20px;" /></div>';
	}
	?>
	<div style="clear: both;"></div>
	
	
	<label class="control-label" for="stylos-temamenu">Botón Acciones</label><br>
	<?php
	$arrayBtnAccion = array('btn-outline-primary', 'btn-outline-secondary', 'btn-outline-success', 'btn-outline-info', 'btn-outline-warning', 'btn-outline-danger', 'btn-outline-alternate', 'btn-outline-light', 'btn-outline-dark');
	$actBtnAct = '';
	foreach($arrayBtnAccion as $actBtnActi){
		if($model->btnAccion == $actBtnActi){ $actBtnAct = 'checked'; }else{ $actBtnAct = '';}
		echo  '<div class="btn '.$actBtnActi.'" style="width: 50px; height: 20px; margin-right: 5px; margin-bottom: 10px;  float: left;  border-radius: 5px;"> <input type="radio" name="Stylos[btnAccion]" value="'.$actBtnActi.'" '.$actBtnAct.' required style="height: 20px; width: 20px;" /></div>';
	}
	
	?>
	<div style="clear: both;"></div>
		
	<label class="control-label" for="stylos-temaContenido">Botón Guardar</label><br>
	<?php
	$arrayBtnG = array('btn-primary', 'btn-secondary', 'btn-success', 'btn-info', 'btn-warning', 'btn-danger', 'btn-focus', 'btn-alternate', 'btn-light', 'btn-dark');
	$actBtngd = '';
	foreach($arrayBtnG as $abtng){
		if($model->btnSave == $abtng){ $actBtngd = 'checked'; }else{ $actBtngd = '';}
		echo  '<div class="btn '.$abtng.'" style="width: 50px; height: 20px; margin-right: 5px; margin-bottom: 10px;  float: left;  border-radius: 5px;"> <input type="radio" name="Stylos[btnSave]" value="'.$abtng.'" '.$actBtngd.' required style="height: 20px; width: 20px;" /></div>';
	}
	?>
	<div style="clear: both;"></div>
	
		
	<label class="control-label" for="stylos-temaContenido">Botón Menus</label><br>
	<?php
	$arrayBtnM = array('btn-primary', 'btn-secondary', 'btn-success', 'btn-info', 'btn-warning', 'btn-danger', 'btn-focus', 'btn-alternate', 'btn-light', 'btn-dark');
	$actBtnmn = '';
	foreach($arrayBtnM as $abtnm){
		if($model->btnMenu == $abtnm){ $actBtnmn = 'checked'; }else{ $actBtnmn = '';}
		echo  '<div class="btn '.$abtnm.'" style="width: 50px; height: 20px; margin-right: 5px; margin-bottom: 10px;  float: left;  border-radius: 5px;"> <input type="radio" name="Stylos[btnMenu]" value="'.$abtnm.'" '.$actBtnmn.' required style="height: 20px; width: 20px;" /></div>';
	}
	?>
	<div style="clear: both;"></div>
		
	<?= $form->field($model, 'temaContenido')->dropDownList(['app-theme-white'=>'White Theme', 'app-theme-gray'=>'Gray Theme']) ?>
	
	
	
</div>
<div class="col-6 float-left">
	<div class="form-group">
	
		<br>
			 <div class="form-group">
				<label class="control-label">Selecciona un logo para el login (recomendado 256px X 77px)</label>
				<input type="file" class="form-control" id="img_login" name="img_login" />
			</div>
		<?php
		if($model->logoLogin != ''){
			echo '<img src="../web/logos/'.$model->logoLogin.'" alt="Slider" height="77px;" />';
		}
		echo $form->field($model, 'logoLogin')->textInput(['type'=>'hidden'])->label(false);
	?>
	</div>
	
	<div class="form-group">
			 <div class="form-group">
				<label class="control-label">Selecciona logo para el banner (recomendado 190px X 45px)</label>
				<input type="file" class="form-control" id="img_banner" name="img_banner" />
			</div>
		<?php
		if($model->logoBanner != ''){
			echo '<img src="../web/logos/'.$model->logoBanner.'" alt="Slider" height="23px;" />';
		}
		echo $form->field($model, 'logoBanner')->textInput(['type'=>'hidden'])->label(false);
	?>
	</div>
	
	<div class="form-group">
	
			 <div class="form-group">
				<label class="control-label">Selecciona icono menu (recomendado 64px X 64px)</label>
				<input type="file" class="form-control" id="img_icono" name="img_icono" />
			</div>
		<?php
		if($model->iconoMenu != ''){
			echo '<img src="../web/logos/'.$model->iconoMenu.'" alt="Slider" height="64px;" />';
		}
		echo $form->field($model, 'iconoMenu')->textInput(['type'=>'hidden'])->label(false);
	?>
	</div>
	
	<div class="form-group">
			 <div class="form-group">
				<label class="control-label">Selecciona logo para el footer (recomendado 190px X 45px)</label>
				<input type="file" class="form-control" id="img_footer" name="img_footer" />
			</div>
		<?php
		if($model->logoFooter != ''){
			echo '<img src="../web/logos/'.$model->logoFooter.'" alt="Slider" height="23px;" />';
		}
		echo $form->field($model, 'logoFooter')->textInput(['type'=>'hidden'])->label(false);
	?>
	</div>
	
	<div class="form-group">
			 <div class="form-group">
				<label class="control-label">Selecciona una imagen png para el favicon (recomendado 32px X 32px)</label>
				<input type="file" class="form-control" id="img_favicon" name="img_favicon" />
			</div>
		<?php
		if($model->favIcon != ''){
			echo '<img src="../web/logos/'.$model->favIcon.'" alt="Slider" height="23px;" />';
		}
		echo $form->field($model, 'favIcon')->textInput(['type'=>'hidden'])->label(false);
	?>
	</div>
	
	
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
	 function cargando(){
		document.getElementById('cargando').style.display = 'block';
	 }  
</script>
