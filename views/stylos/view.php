<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Stylos */

$this->title = $model->configuracionesSistemaID;
$this->params['breadcrumbs'][] = ['label' => 'Stylos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="stylos-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->configuracionesSistemaID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->configuracionesSistemaID], [
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
            'configuracionesSistemaID',
            'logoLogin',
            'logoBanner',
            'iconoMenu',
            'temaBanner',
            'temaMenu',
            'temaContenido',
            'activoConfiguracionesSistema:boolean',
            'versionRegistro',
            'regEstado:boolean',
            'regFechaUltimaModificacion',
            'regUsuarioUltimaModificacion',
            'regFormularioUltimaModificacion',
            'regVersionUltimaModificacion',
        ],
    ]) ?>

</div>
