<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UsuariosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="usuarios-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'usuarioID') ?>

    <?= $form->field($model, 'nombreUsuario') ?>

    <?= $form->field($model, 'passw') ?>

    <?= $form->field($model, 'usuario') ?>

    <?= $form->field($model, 'activoUsuario') ?>

    <?php // echo $form->field($model, 'correoUsuario') ?>

    <?php // echo $form->field($model, 'codigoRecuperacionPassw') ?>

    <?php // echo $form->field($model, 'fechaGeneracionCodigoRecuperacionPassw') ?>

    <?php // echo $form->field($model, 'intentosValidos') ?>

    <?php // echo $form->field($model, 'versionRegistro') ?>

    <?php // echo $form->field($model, 'regEstado') ?>

    <?php // echo $form->field($model, 'regFechaUltimaModificacion') ?>

    <?php // echo $form->field($model, 'regUsuarioUltimaModificacion') ?>

    <?php // echo $form->field($model, 'regFormularioUltimaModificacion') ?>

    <?php // echo $form->field($model, 'regVersionUltimaModificacion') ?>

    <?php // echo $form->field($model, 'AuthKey') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
