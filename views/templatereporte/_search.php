<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TemplatereporteSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="templatereporte-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'templateReporteID') ?>

    <?= $form->field($model, 'nombreTemplateReporte') ?>

    <?= $form->field($model, 'logoTemplateReporte') ?>

    <?= $form->field($model, 'encabezadoTemplateReporte') ?>

    <?= $form->field($model, 'pieTemplateReporteL1') ?>

    <?php // echo $form->field($model, 'pieTemplateReporteL2') ?>

    <?php // echo $form->field($model, 'pieTemplateReporteL3') ?>

    <?php // echo $form->field($model, 'colorLinea') ?>

    <?php // echo $form->field($model, 'colorTituloTabla') ?>

    <?php // echo $form->field($model, 'colorTituloTexto') ?>

    <?php // echo $form->field($model, 'colorTextoFooter') ?>

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
