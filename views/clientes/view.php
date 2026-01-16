<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Clientes */

$this->title = $model->clienteID;
$this->params['breadcrumbs'][] = ['label' => 'Clientes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="clientes-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->clienteID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->clienteID], [
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
            'clienteID',
            'nombreComercial',
            'clienteRazonSocial',
            'clienteRFC',
            'clienteTelefono',
            'clienteEmail:ntext',
            'vpc',
            'grupoClienteID',
            'cuentaContable',
            'establecimientoProvisiona',
            'tipoClienteID',
            'afectaSaldoRem',
            'cuentafactura',
            'metodoPagoID',
            'formaPagoID',
            'UsoCFDIID',
            'clienteGrupoFacturacionID',
            'condicionesPago',
            'regimenFiscalID',
            'codigoPostalCliente',
            'clienteTipoPersona',
            'validarSaldo:boolean',
            'estadoCliente:boolean',
            'versionRegistro',
            'regEstado:boolean',
            'regFechaUltimaModificacion',
            'regUsuarioUltimaModificacion',
            'regFormularioUltimaModificacion',
            'regVersionUltimaModificacion',
        ],
    ]) ?>

</div>
