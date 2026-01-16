<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "FacturasDetalles".
 *
 * @property int $facturaDetalleID
 * @property int $facturaID
 * @property int $productoID
 * @property int $almacenID
 * @property string $nombreProducto
 * @property string $unidad
 * @property string $conceptoFacturado
 * @property double $cantidadDetalleFactura
 * @property double $precioDetalleFactura
 * @property double $subTotalDetalleFactura
 * @property double $totalDetalleFactura
 * @property double $ivaDetalleFactura
 * @property double $iepsDetalleFactura
 * @property double $isrDetalleFactura
 * @property int $facturaRelacionadaID
 * @property double $saldoAnterior
 * @property double $importePagado
 * @property double $saldoInsoluto
 * @property int $numeroParcialidad
 * @property bool $activoFacturaDetalle
 * @property bool $estadoFacturaDetalle
 * @property bool $regEstado
 * @property int $establecimientoID
 */
class Facturasdetalles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Facturas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['facturaID', 'productoID', 'almacenID', 'facturaRelacionadaID', 'numeroParcialidad','establecimientoID'], 'integer'],
            [['cantidadDetalleFactura', 'precioDetalleFactura', 'subTotalDetalleFactura', 'totalDetalleFactura', 'ivaDetalleFactura', 'iepsDetalleFactura', 'isrDetalleFactura', 'saldoAnterior', 'importePagado', 'saldoInsoluto'], 'number'],
            [['activoFacturaDetalle', 'estadoFacturaDetalle', 'regEstado'], 'boolean'],
            [['nombreProducto', 'unidad'], 'string', 'max' => 250],
            [['conceptoFacturado'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'facturaDetalleID' => 'Factura Detalle ID',
            'facturaID' => 'Factura ID',
            'productoID' => 'Producto ID',
            'almacenID' => 'Almacen ID',
            'nombreProducto' => 'Nombre Producto',
            'unidad' => 'Unidad',
            'conceptoFacturado' => 'Concepto Facturado',
            'cantidadDetalleFactura' => 'Cantidad Detalle Factura',
            'precioDetalleFactura' => 'Precio Detalle Factura',
            'subTotalDetalleFactura' => 'Sub Total Detalle Factura',
            'totalDetalleFactura' => 'Total Detalle Factura',
            'ivaDetalleFactura' => 'Iva Detalle Factura',
            'iepsDetalleFactura' => 'Ieps Detalle Factura',
            'isrDetalleFactura' => 'Isr Detalle Factura',
            'facturaRelacionadaID' => 'Factura Relacionada ID',
            'saldoAnterior' => 'Saldo Anterior',
            'importePagado' => 'Importe Pagado',
            'saldoInsoluto' => 'Saldo Insoluto',
            'numeroParcialidad' => 'Numero Parcialidad',
            'activoFacturaDetalle' => 'Activo Factura Detalle',
            'estadoFacturaDetalle' => 'Estado Factura Detalle',
            'regEstado' => 'Reg Estado',
            'establecimientoID' => 'Establecimiento',
            'clienteID' => 'Cliente',
            'formaPagoID' => 'Forma de pago'
        ];
    }


 /**
     * funciones relaciones
     * relaciones con tablas
     */


}
