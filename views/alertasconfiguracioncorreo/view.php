<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Alertasconfiguracioncorreo */

$this->title = $model->alertaConfiguracionCorreoID;
$this->params['breadcrumbs'][] = ['label' => 'Alertasconfiguracioncorreos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="alertasconfiguracioncorreo-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->alertaConfiguracionCorreoID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->alertaConfiguracionCorreoID], [
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
            'alertaConfiguracionCorreoID',
            'correo',
            'pass',
            'nombreHost',
            'puertoHost',
            'usaSSL:boolean',
            'fechaInicial',
            'fechaFinal',
            'pieMensaje:ntext',
            'tipoCorreo',
            'versionRegistro',
            'regEstado:boolean',
            'regFechaUltimaModificacion',
            'regUsuarioUltimaModificacion',
            'regFormularioUltimaModificacion',
            'regVersionUltimaModificacion',
        ],
    ]) ?>

</div>
