<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AformulariosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="aformularios-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'accionFormularioID') ?>

    <?= $form->field($model, 'estadoAccion')->checkbox() ?>

    <?= $form->field($model, 'accionID') ?>

    <?= $form->field($model, 'formularioID') ?>

    <?= $form->field($model, 'versionRegistro') ?>

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
