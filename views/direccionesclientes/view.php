<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Direccionesclientes */

$this->title = $model->direccionClienteID;
$this->params['breadcrumbs'][] = ['label' => 'Direccionesclientes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="direccionesclientes-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->direccionClienteID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->direccionClienteID], [
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
            'direccionClienteID',
            'alias',
            'calle',
            'numeroInterior',
            'numeroExterior',
            'codigoPostal',
            'colonia',
            'localidad',
            'referencia',
            'municipio',
            'esDefault:boolean',
            'latitud',
            'longitud',
            'estadoID',
            'clienteID',
            'estadoDireccion:boolean',
            'versionRegistro',
            'regEstado:boolean',
            'regFechaUltimaModificacion',
            'regUsuarioUltimaModificacion',
            'regFormularioUltimaModificacion',
            'regVersionUltimaModificacion',
        ],
    ]) ?>

</div>
