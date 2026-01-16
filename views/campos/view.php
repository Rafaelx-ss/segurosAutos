<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Campos */

$this->title = $model->campoID;
$this->params['breadcrumbs'][] = ['label' => 'Campos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="campos-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->campoID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->campoID], [
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
            'campoID',
            'nombreCampo',
            'tipoControl',
            'longitud',
            'campoPK:boolean',
            'campoFK:boolean',
            'controlQuery:ntext',
            'visible:boolean',
            'orden',
            'tipoCampo',
            'campoRequerido:boolean',
            'textField',
            'valueField',
            'valorDefault',
            'CSS:ntext',
            'catalogoID',
            'textoID',
            'catalogoReferenciaID',
            'versionRegistro',
            'regEstado:boolean',
            'regFechaUltimaModificacion',
            'regUsuarioUltimaModificacion',
            'regFormularioUltimaModificacion',
            'regVersionUltimaModificacion',
        ],
    ]) ?>

</div>
