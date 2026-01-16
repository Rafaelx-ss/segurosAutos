<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ReportescamposSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reportescampos-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'reporteCampoID') ?>

    <?= $form->field($model, 'reporteConfiguracionID') ?>

    <?= $form->field($model, 'nombreCampo') ?>

    <?= $form->field($model, 'visible')->checkbox() ?>

    <?= $form->field($model, 'searchVisible')->checkbox() ?>

    <?php // echo $form->field($model, 'orden') ?>

    <?php // echo $form->field($model, 'textoID') ?>

    <?php // echo $form->field($model, 'tipoControl') ?>

    <?php // echo $form->field($model, 'controlQuery') ?>

    <?php // echo $form->field($model, 'queryValor') ?>

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
