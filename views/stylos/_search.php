<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\StylosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="stylos-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'configuracionesSistemaID') ?>

    <?= $form->field($model, 'logoLogin') ?>

    <?= $form->field($model, 'logoBanner') ?>

    <?= $form->field($model, 'iconoMenu') ?>

    <?= $form->field($model, 'temaBanner') ?>

    <?php // echo $form->field($model, 'temaMenu') ?>

    <?php // echo $form->field($model, 'temaContenido') ?>

    <?php // echo $form->field($model, 'activoConfiguracionesSistema')->checkbox() ?>

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
