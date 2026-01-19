<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Relacionpaquetessotfware */

$this->title = $model->relacionPaqueteSotfwareID;
$this->params['breadcrumbs'][] = ['label' => 'Relacionpaquetessotfwares', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="relacionpaquetessotfware-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->relacionPaqueteSotfwareID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->relacionPaqueteSotfwareID], [
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
            'relacionPaqueteSotfwareID',
            'paqueteKairosID',
            'softwareKairosID',
            'versionRegistro',
            'regEstado:boolean',
            'regFechaUltimaModificacion',
            'regUsuarioUltimaModificacion',
            'regFormularioUltimaModificacion',
            'regVersionUltimaModificacion',
        ],
    ]) ?>

</div>
