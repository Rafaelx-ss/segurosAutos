<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Contactosclientes */

$this->title = $model->contactoClienteID;
$this->params['breadcrumbs'][] = ['label' => 'Contactosclientes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="contactosclientes-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->contactoClienteID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->contactoClienteID], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'contactoClienteID',
            'contactoClienteNombre',
            'clienteID',
            'email:email',
            'telefono',
            'celular',
            'tipoContactoID',
            'versionRegistro',
            'regEstado:boolean',
            'regFechaUltimaModificacion',
            'regUsuarioUltimaModificacion',
            'regFormularioUltimaModificacion',
            'regVersionUltimaModificacion',
        ],
    ]) ?>

</div>
