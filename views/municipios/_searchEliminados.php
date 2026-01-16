<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MunicipiosSearch */
/* @var $form yii\widgets\ActiveForm */


use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
?>

<div class="municipios-search">

    <?php $form = ActiveForm::begin([
        'action' => ['eliminados'],
        'method' => 'get',
    ]); ?>

	<input type="hidden" name="f" value="<?= $f ?>" />
	
<?php  
$catalogo = Yii::$app->db->createCommand("SELECT * FROM Catalogos where regEstado='1' and nombreCatalogo='Municipios'")->queryOne();

if(isset($catalogo['catalogoID'])){	
	$campos = Yii::$app->db->createCommand("SELECT * FROM CamposGrid where regEstado='1' and catalogoID='".$catalogo['catalogoID']."' order by orden ASC")->queryAll();
	
	

	foreach($campos as $rCampos){
		if($rCampos['searchVisible'] == 1){		
			if($rCampos['tipoControl'] == 'text' or $rCampos['tipoControl'] == 'number' or $rCampos['tipoControl'] == 'date' or $rCampos['tipoControl'] == 'email' or $rCampos['tipoControl'] == 'password' or $rCampos['tipoControl'] == 'color'){
				echo '<div class="col-4 float-left  pleft-0">';
						echo $form->field($model, $rCampos['nombreCampo'])->textInput(['type'=>$rCampos['tipoControl']])->label(Yii::$app->globals->getTraductor(getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo']));
				echo '</div>';
			}elseif($rCampos['tipoControl'] == 'checkbox'){
				echo '<div class="col-4 float-left  pleft-0">';
					echo $form->field($model, $rCampos['nombreCampo'])->checkbox(['label' => Yii::$app->globals->getTraductor(getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo'])]);
				echo '</div>';
			}elseif($rCampos['tipoControl'] == 'textArea'){
				echo '<div class="col-4 float-left  pleft-0">';
					echo $form->field($model, $rCampos['nombreCampo'])->textarea(['rows' => 4])->label(Yii::$app->globals->getTraductor(getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo']));
				echo '</div>';
			}elseif($rCampos['tipoControl'] == 'select' or $rCampos['tipoControl'] == 'consulta'){
				$catalogoRef = Yii::$app->db->createCommand("SELECT * FROM Catalogos where catalogoID='".$rCampos['catalogoReferenciaID']."'")->queryOne();
				
				if(isset($catalogoRef['nombreCatalogo'])){
				echo '<div class="col-4 float-left  pleft-0">';	
				echo $form->field($model, $rCampos['nombreCampo'])->widget(Select2::classname(), [                         
						'data' => ArrayHelper::map(Yii::$app->db->createCommand('Select * from '.$catalogoRef['nombreCatalogo'])->queryAll(), $rCampos['textField'], $rCampos['textField']),
						'language' => 'es',
						'options' => ['placeholder' => ' --- Selecciona --- '],
						'pluginOptions' => ['allowClear' => true]])->label(Yii::$app->globals->getTraductor(getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo'])); 
				echo '</div>';
				}	
			}elseif($rCampos['tipoControl'] == 'Autoincremental'){
				//no imprime el input
			}else{
				echo '<div class="col-4 float-left  pleft-0">';
				echo $form->field($model, $rCampos['nombreCampo'])->textInput()->label(Yii::$app->globals->getTraductor(getNameInput($rCampos['textoID']), Yii::$app->session['idiomaId'], $rCampos['nombreCampo']));
				echo '</div>';
			}
		}
	}
}
?>

	<div class="col-12 float-left  pleft-0">
        <?=  Html::submitButton(Yii::$app->globals->getTraductor(19, Yii::$app->session['idiomaId'], 'Buscar'), ['class' => 'btn '.Yii::$app->globals->btnSave()]) ?>
        <?=  Html::resetButton(Yii::$app->globals->getTraductor(20, Yii::$app->session['idiomaId'], 'Reiniciar'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

	<div style="clear: both;"></div>
	<hr>	
    <?php ActiveForm::end(); ?>

</div>


<?php function getNameInput($id){
	if($id == 1){
		return 0;
	}else{
		return $id;
	}
}
?>
