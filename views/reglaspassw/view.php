<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Reglaspassw */

$this->title = $model->minimioLongitudPassw;
$this->params['breadcrumbs'][] = ['label' => 'Reglaspassws', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="reglaspassw-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'minimioLongitudPassw' => $model->minimioLongitudPassw, 'maximoIntentosFallidos' => $model->maximoIntentosFallidos, 'tiempoCaducidadCodigoRecuperacionPassw' => $model->tiempoCaducidadCodigoRecuperacionPassw, 'tiempoCaducidadInactivadadPassw' => $model->tiempoCaducidadInactivadadPassw, 'contieneMayuscula' => $model->contieneMayuscula, 'contieneCaracteresEspeciales' => $model->contieneCaracteresEspeciales, 'contieneNumeros' => $model->contieneNumeros], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'minimioLongitudPassw' => $model->minimioLongitudPassw, 'maximoIntentosFallidos' => $model->maximoIntentosFallidos, 'tiempoCaducidadCodigoRecuperacionPassw' => $model->tiempoCaducidadCodigoRecuperacionPassw, 'tiempoCaducidadInactivadadPassw' => $model->tiempoCaducidadInactivadadPassw, 'contieneMayuscula' => $model->contieneMayuscula, 'contieneCaracteresEspeciales' => $model->contieneCaracteresEspeciales, 'contieneNumeros' => $model->contieneNumeros], [
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
            'minimioLongitudPassw',
            'maximoIntentosFallidos',
            'tiempoCaducidadCodigoRecuperacionPassw',
            'tiempoCaducidadInactivadadPassw',
            'contieneMayuscula',
            'contieneMinusculas',
            'contieneCaracteresEspeciales',
            'contieneNumeros',
            'duracionActualizaPass',
            'cantidadPassRepetidos',
            'tiempoAlmacenadoPassword',
            'contieneRepetidos:boolean',
            'cantidadRepetidos',
            'contieneConsecutivos:boolean',
            'cantidadConsecutivos',
            'versionRegistro',
            'regEstado',
            'regFechaUltimaModificacion',
            'regUsuarioUltimaModificacion',
            'regFormularioUltimaModificacion',
            'regVersionUltimaModificacion',
        ],
    ]) ?>

</div>
