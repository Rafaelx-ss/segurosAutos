<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ApiconfiguracionesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="apiconfiguraciones-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'apiListaConfiguracionID') ?>

    <?= $form->field($model, 'usuarioApiLista') ?>

    <?= $form->field($model, 'passwordApiLista') ?>

    <?= $form->field($model, 'rutaApiLista') ?>

    <?= $form->field($model, 'identificadorApiLista') ?>

    <?php // echo $form->field($model, 'tipoSolicitudApiLista') ?>

    <?php // echo $form->field($model, 'aplicacionID') ?>

    <?php // echo $form->field($model, 'versionActual')->checkbox() ?>

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
