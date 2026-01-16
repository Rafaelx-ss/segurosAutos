<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ReportesconfigSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reportesconfig-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'reporteConfiguracionID') ?>

    <?= $form->field($model, 'templateReporteID') ?>

    <?= $form->field($model, 'nombreReporte') ?>

    <?= $form->field($model, 'queryReporte') ?>

    <?= $form->field($model, 'columnasReporte') ?>

    <?php // echo $form->field($model, 'imprimirLogoPdf')->checkbox() ?>

    <?php // echo $form->field($model, 'imprimirEncabezado')->checkbox() ?>

    <?php // echo $form->field($model, 'imprimirFechaHora')->checkbox() ?>

    <?php // echo $form->field($model, 'imprimirNombreUsuario')->checkbox() ?>

    <?php // echo $form->field($model, 'imprimirLogoExcel')->checkbox() ?>

    <?php // echo $form->field($model, 'imprimirPie')->checkbox() ?>

    <?php // echo $form->field($model, 'imprimirEncabezadoExcel')->checkbox() ?>

    <?php // echo $form->field($model, 'imprimirFechaHoraExcel')->checkbox() ?>

    <?php // echo $form->field($model, 'imprimirNombreUsuarioExcel')->checkbox() ?>

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
