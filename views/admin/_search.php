<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AdminSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="admin-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'Id_admin') ?>

    <?= $form->field($model, 'Tipo_user') ?>

    <?= $form->field($model, 'Nombre_admin') ?>

    <?= $form->field($model, 'User_admin') ?>

    <?= $form->field($model, 'Pass_hadmin') ?>

    <?php // echo $form->field($model, 'Pass_radmin') ?>

    <?php // echo $form->field($model, 'AuthKey') ?>

    <?php // echo $form->field($model, 'Status_admin') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
