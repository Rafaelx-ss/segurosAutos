<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CamposSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="campos-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'campoID') ?>

    <?= $form->field($model, 'nombreCampo') ?>

    <?= $form->field($model, 'tipoControl') ?>

    <?= $form->field($model, 'longitud') ?>

    <?= $form->field($model, 'campoPK')->checkbox() ?>

    <?php // echo $form->field($model, 'campoFK')->checkbox() ?>

    <?php // echo $form->field($model, 'controlQuery') ?>

    <?php // echo $form->field($model, 'visible')->checkbox() ?>

    <?php // echo $form->field($model, 'orden') ?>

    <?php // echo $form->field($model, 'tipoCampo') ?>

    <?php // echo $form->field($model, 'campoRequerido')->checkbox() ?>

    <?php // echo $form->field($model, 'textField') ?>

    <?php // echo $form->field($model, 'valueField') ?>

    <?php // echo $form->field($model, 'valorDefault') ?>

    <?php // echo $form->field($model, 'CSS') ?>

    <?php // echo $form->field($model, 'catalogoID') ?>

    <?php // echo $form->field($model, 'textoID') ?>

    <?php // echo $form->field($model, 'catalogoReferenciaID') ?>

    <?php // echo $form->field($model, 'versionRegistro') ?>

    <?php // echo $form->field($model, 'regEstado')->checkbox() ?>

    <?php // echo $form->field($model, 'regFechaUltimaModificacion') ?>

    <?php // echo $form->field($model, 'regUsuarioUltimaModificacion') ?>

    <?php // echo $form->field($model, 'regFormularioUltimaModificacion') ?>

    <?php // echo $form->field($model, 'regVersionUltimaModificacion') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
