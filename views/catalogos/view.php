<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Catalogos */

$this->title = $model->catalogoID;
$this->params['breadcrumbs'][] = ['label' => 'Catalogos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="catalogos-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->catalogoID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->catalogoID], [
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
            'catalogoID',
            'nombreCatalogo',
            'sqlQuery:ntext',
            'versionRegistro',
            'regEstado:boolean',
            'regFechaUltimaModificacion',
            'regUsuarioUltimaModificacion',
            'regFormularioUltimaModificacion',
            'regVersionUltimaModificacion',
            'activoCatalogo:boolean',
        ],
    ]) ?>

</div>
