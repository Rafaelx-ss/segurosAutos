<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Formulariosperfiles */

$this->title = $model->permisoFormularioID;
$this->params['breadcrumbs'][] = ['label' => 'Formulariosperfiles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="formulariosperfiles-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'permisoFormularioID' => $model->permisoFormularioID, 'perfilID' => $model->perfilID, 'establecimientoID' => $model->establecimientoID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'permisoFormularioID' => $model->permisoFormularioID, 'perfilID' => $model->perfilID, 'establecimientoID' => $model->establecimientoID], [
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
            'permisoFormularioID',
            'perfilID',
            'formularioID',
            'establecimientoID',
            'activoPermiso:boolean',
            'versionRegistro',
            'regEstado:boolean',
            'regFechaUltimaModificacion',
            'regUsuarioUltimaModificacion',
            'regFormularioUltimaModificacion',
            'regVersionUltimaModificacion',
        ],
    ]) ?>

</div>
