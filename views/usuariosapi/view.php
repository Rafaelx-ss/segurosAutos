<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Usuariosapi */

$this->title = $model->usuarioApiID;
$this->params['breadcrumbs'][] = ['label' => 'Usuariosapis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="usuariosapi-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->usuarioApiID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->usuarioApiID], [
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
            'nombreUsuario',
            'passw',
            'usuario',
            'activoUsuario',
            'correoUsuario',
            'codigoRecuperacionPassw',
            'fechaGeneracionCodigoRecuperacionPassw',
            'intentosValidos',
            'usarSeguridadIP',
            'usarSeguridadMac',
            'usarLectura',
            'usarEscritura',
            'versionRegistro',
            'regEstado',
            'regFechaUltimaModificacion',
            'regUsuarioUltimaModificacion',
            'regFormularioUltimaModificacion',
            'regVersionUltimaModificacion',
            'tiempoCaducidadToken',
        ],
    ]) ?>

</div>
