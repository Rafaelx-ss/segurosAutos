<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AformulariosperfilesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="aformulariosperfiles-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'permisoAccionID') ?>

    <?= $form->field($model, 'permisoFormularioID') ?>

    <?= $form->field($model, 'accionFormularioID') ?>

    <?= $form->field($model, 'perfilID') ?>

    <?= $form->field($model, 'establecimientoID') ?>

    <?php // echo $form->field($model, 'activoPermiso')->checkbox() ?>

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
