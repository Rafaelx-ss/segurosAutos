<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Facturasdetalles;

/**
 * FacturasdetallesSearch represents the model behind the search form of `app\models\Facturasdetalles`.
 */
class FacturasdetallesSearch extends Facturasdetalles
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['facturaDetalleID', 'facturaID', 'productoID', 'almacenID', 'nombreProducto', 'unidad', 'conceptoFacturado', 'facturaRelacionadaID', 'numeroParcialidad'], 'safe'],
            [['cantidadDetalleFactura', 'precioDetalleFactura', 'subTotalDetalleFactura', 'totalDetalleFactura', 'ivaDetalleFactura', 'iepsDetalleFactura', 'isrDetalleFactura', 'saldoAnterior', 'importePagado', 'saldoInsoluto'], 'number'],
            [['activoFacturaDetalle', 'estadoFacturaDetalle', 'regEstado'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */


    public function search($params)
    {
        $query = Facturasdetalles::find();
		        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'facturaDetalleID' => $this->facturaDetalleID,
            'facturaID' => $this->facturaID,
            'productoID' => $this->productoID,
            'almacenID' => $this->almacenID,
            'cantidadDetalleFactura' => $this->cantidadDetalleFactura,
            'precioDetalleFactura' => $this->precioDetalleFactura,
            'subTotalDetalleFactura' => $this->subTotalDetalleFactura,
            'totalDetalleFactura' => $this->totalDetalleFactura,
            'ivaDetalleFactura' => $this->ivaDetalleFactura,
            'iepsDetalleFactura' => $this->iepsDetalleFactura,
            'isrDetalleFactura' => $this->isrDetalleFactura,
            'facturaRelacionadaID' => $this->facturaRelacionadaID,
            'saldoAnterior' => $this->saldoAnterior,
            'importePagado' => $this->importePagado,
            'saldoInsoluto' => $this->saldoInsoluto,
            'numeroParcialidad' => $this->numeroParcialidad,
            'activoFacturaDetalle' => $this->activoFacturaDetalle,
            'estadoFacturaDetalle' => $this->estadoFacturaDetalle,
            'regEstado' => $this->regEstado,
        ]);

        $query->andFilterWhere(['like', 'nombreProducto', $this->nombreProducto])
            ->andFilterWhere(['like', 'unidad', $this->unidad])
            ->andFilterWhere(['like', 'conceptoFacturado', $this->conceptoFacturado]);
		$query->andWhere(['=', 'FacturasDetalles.regEstado', '1']);


	return $dataProvider;		
		
    }

	public function searchelimina($params)
    {
        $query = Facturasdetalles::find();
		        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'facturaDetalleID' => $this->facturaDetalleID,
            'facturaID' => $this->facturaID,
            'productoID' => $this->productoID,
            'almacenID' => $this->almacenID,
            'cantidadDetalleFactura' => $this->cantidadDetalleFactura,
            'precioDetalleFactura' => $this->precioDetalleFactura,
            'subTotalDetalleFactura' => $this->subTotalDetalleFactura,
            'totalDetalleFactura' => $this->totalDetalleFactura,
            'ivaDetalleFactura' => $this->ivaDetalleFactura,
            'iepsDetalleFactura' => $this->iepsDetalleFactura,
            'isrDetalleFactura' => $this->isrDetalleFactura,
            'facturaRelacionadaID' => $this->facturaRelacionadaID,
            'saldoAnterior' => $this->saldoAnterior,
            'importePagado' => $this->importePagado,
            'saldoInsoluto' => $this->saldoInsoluto,
            'numeroParcialidad' => $this->numeroParcialidad,
            'activoFacturaDetalle' => $this->activoFacturaDetalle,
            'estadoFacturaDetalle' => $this->estadoFacturaDetalle,
            'regEstado' => $this->regEstado,
        ]);

        $query->andFilterWhere(['like', 'nombreProducto', $this->nombreProducto])
            ->andFilterWhere(['like', 'unidad', $this->unidad])
            ->andFilterWhere(['like', 'conceptoFacturado', $this->conceptoFacturado]);
		$query->andWhere(['=', 'FacturasDetalles.regEstado', '0']);


	return $dataProvider;		
		
    }
}
