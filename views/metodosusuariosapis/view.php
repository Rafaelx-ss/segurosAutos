<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Metodosusuariosapis */

$this->title = $model->usuarioApiID;
$this->params['breadcrumbs'][] = ['label' => 'Metodosusuariosapis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="metodosusuariosapis-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'usuarioApiID' => $model->usuarioApiID, 'metodoApiID' => $model->metodoApiID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'usuarioApiID' => $model->usuarioApiID, 'metodoApiID' => $model->metodoApiID], [
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
            'usuarioApiID',
            'metodoApiID',
            'estadoDetalleApi:boolean',
            'versionRegistro',
            'regEstado',
            'regFechaUltimaModificacion',
            'regUsuarioUltimaModificacion',
            'regFormularioUltimaModificacion',
            'regVersionUltimaModificacion',
        ],
    ]) ?>

</div>
