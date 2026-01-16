<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Logaccesosapi */

$this->title = $model->logAccesoApiId;
$this->params['breadcrumbs'][] = ['label' => 'Logaccesosapis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="logaccesosapi-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'logAccesoApiId' => $model->logAccesoApiId, 'establecimientoID' => $model->establecimientoID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'logAccesoApiId' => $model->logAccesoApiId, 'establecimientoID' => $model->establecimientoID], [
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
            'logAccesoApiId',
            'establecimientoID',
            'usuarioApiID',
            'ipAddress',
            'macAddress',
            'fechaAcceso',
            'versionRegistro',
            'regEstado',
            'regFechaUltimaModificacion',
            'regUsuarioUltimaModificacion',
            'regFormularioUltimaModificacion',
            'regVersionUltimaModificacion',
            'nombreApi',
            'codigoResultado',
            'mensajeResultado',
            'parametrosApi',
        ],
    ]) ?>

</div>
