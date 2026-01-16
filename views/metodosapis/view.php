<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Metodosapis */

$this->title = $model->metodoApiID;
$this->params['breadcrumbs'][] = ['label' => 'Metodosapis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="metodosapis-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->metodoApiID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->metodoApiID], [
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
            'metodoApiID',
            'apiID',
            'estadoMetodo:boolean',
            'versionRegistro',
            'regEstado',
            'regFechaUltimaModificacion',
            'regUsuarioUltimaModificacion',
            'regFormularioUltimaModificacion',
            'regVersionUltimaModificacion',
            'tipoMetodoApi',
            'permisoMaster:boolean',
            'permisoGrupoEstablecimiento:boolean',
            'permisoEstablecimiento:boolean',
        ],
    ]) ?>

</div>
