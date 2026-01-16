<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\FormulariosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="formularios-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'formularioID') ?>

    <?= $form->field($model, 'nombreFormulario') ?>

    <?= $form->field($model, 'urlArchivo') ?>

    <?= $form->field($model, 'estadoFormulario')->checkbox() ?>

    <?= $form->field($model, 'orden') ?>

    <?php  echo $form->field($model, 'tipoMenu') ?>

    <?php // echo $form->field($model, 'menuID') ?>

    <?php // echo $form->field($model, 'aplicacionID') ?>

    <?php // echo $form->field($model, 'catalogoID') ?>

    <?php // echo $form->field($model, 'textoID') ?>

    <?php // echo $form->field($model, 'tipoFormularioID') ?>

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
