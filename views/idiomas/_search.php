<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\IdiomasSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="idiomas-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'idiomaID') ?>

    <?= $form->field($model, 'iconIdioma') ?>

    <?= $form->field($model, 'nombreIdioma') ?>

    <?= $form->field($model, 'activoIdioma')->checkbox() ?>

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
