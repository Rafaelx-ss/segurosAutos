<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Reportesconfiguraciones */

$this->title = $model->reporteConfiguracionID;
$this->params['breadcrumbs'][] = ['label' => 'Reportesconfiguraciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="reportesconfiguraciones-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->reporteConfiguracionID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->reporteConfiguracionID], [
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
            'reporteConfiguracionID',
            'templateReporteID',
            'nombreReporte',
            'queryReporte:ntext',
            'orientacionPagina',
            'columnasReporte',
            'imprimirLogoPdf:boolean',
            'imprimirEncabezado:boolean',
            'imprimirFechaHora:boolean',
            'imprimirNombreUsuario:boolean',
            'imprimirLogoExcel:boolean',
            'imprimirPie:boolean',
            'imprimirEncabezadoExcel:boolean',
            'imprimirFechaHoraExcel:boolean',
            'imprimirNombreUsuarioExcel:boolean',
            'versionRegistro',
            'regEstado:boolean',
            'regFechaUltimaModificacion',
            'regUsuarioUltimaModificacion',
            'regFormularioUltimaModificacion',
            'regVersionUltimaModificacion',
        ],
    ]) ?>

</div>
