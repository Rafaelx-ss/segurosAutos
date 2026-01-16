<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Aformulariosperfiles */

$this->title = $model->permisoAccionID;
$this->params['breadcrumbs'][] = ['label' => 'Aformulariosperfiles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="aformulariosperfiles-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'permisoAccionID' => $model->permisoAccionID, 'permisoFormularioID' => $model->permisoFormularioID, 'perfilID' => $model->perfilID, 'establecimientoID' => $model->establecimientoID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'permisoAccionID' => $model->permisoAccionID, 'permisoFormularioID' => $model->permisoFormularioID, 'perfilID' => $model->perfilID, 'establecimientoID' => $model->establecimientoID], [
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
            'permisoAccionID',
            'permisoFormularioID',
            'accionFormularioID',
            'perfilID',
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
