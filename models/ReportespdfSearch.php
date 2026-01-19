<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Reportespdf;

/**
 * ReportespdfSearch represents the model behind the search form of `app\models\Reportespdf`.
 */
class ReportespdfSearch extends Reportespdf
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reportesPdfID', 'establecimientoID', 'folioReporte', 'arrayCampos', 'codigoReporte', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion', 'headerReporte', 'footerReporte', 'paginacionReporte', 'altoHeader', 'altoFooter'], 'safe'],
            [['regEstado'], 'boolean'],
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
public $idEstablecimientos; 


    public function search($params)
    {
        $query = Reportespdf::find();
		$query->joinWith(['idEstablecimientos']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->sort->attributes['idEstablecimientos'] = [
						'asc' => ['Establecimientos.aliasEstablecimiento' => SORT_ASC],
						'desc' => ['Establecimientos.aliasEstablecimiento' => SORT_DESC],
				];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'reportesPdfID' => $this->reportesPdfID,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'folioReporte', $this->folioReporte])
            ->andFilterWhere(['like', 'arrayCampos', $this->arrayCampos])
            ->andFilterWhere(['like', 'codigoReporte', $this->codigoReporte]);
		$query->andFilterWhere(['like', 'Establecimientos.aliasEstablecimiento', $this->establecimientoID]);
$query->andWhere(['=', 'ReportesPdf.regEstado', '1']);


	return $dataProvider;		
		
    }

	public function searchelimina($params)
    {
        $query = Reportespdf::find();
		$query->joinWith(['idEstablecimientos']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->sort->attributes['idEstablecimientos'] = [
						'asc' => ['Establecimientos.aliasEstablecimiento' => SORT_ASC],
						'desc' => ['Establecimientos.aliasEstablecimiento' => SORT_DESC],
				];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'reportesPdfID' => $this->reportesPdfID,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'folioReporte', $this->folioReporte])
            ->andFilterWhere(['like', 'arrayCampos', $this->arrayCampos])
            ->andFilterWhere(['like', 'codigoReporte', $this->codigoReporte]);
		$query->andFilterWhere(['like', 'Establecimientos.aliasEstablecimiento', $this->establecimientoID]);
$query->andWhere(['=', 'ReportesPdf.regEstado', '0']);


	return $dataProvider;		
		
    }
}
