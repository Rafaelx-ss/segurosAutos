<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CombosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="combos-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'comboAnidadoID') ?>

    <?= $form->field($model, 'catalogoID') ?>
    <?= $form->field($model, 'campoIDPadre') ?>

    <?= $form->field($model, 'campoIDdependiente') ?>

    <?php // echo $form->field($model, 'controlQuery') ?>

    <?php // echo $form->field($model, 'queryValue') ?>

    <?php // echo $form->field($model, 'queryText') ?>

    <?php // echo $form->field($model, 'versionRegistro') ?>

    <?php // echo $form->field($model, 'regEstado')->checkbox() ?>

    <?php // echo $form->field($model, 'regFechaUltimaModificacion') ?>

    <?php // echo $form->field($model, 'regUsuarioUltimaModificacion') ?>

    <?php // echo $form->field($model, 'regFormularioUltimaModificacion') ?>

    <?php // echo $form->field($model, 'regVersionUltimaModificacion') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn '.Yii::$app->globals->btnSave()]) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
