<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Reportesconfig;

/**
 * ReportesconfigSearch represents the model behind the search form of `app\models\Reportesconfig`.
 */
class ReportesconfigSearch extends Reportesconfig
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reporteConfiguracionID', 'templateReporteID', 'nombreReporte', 'queryReporte', 'columnasReporte', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion', 'orientacionPagina'], 'safe'],
            [['imprimirLogoPdf', 'imprimirEncabezado', 'imprimirFechaHora', 'imprimirNombreUsuario', 'imprimirLogoExcel', 'imprimirPie', 'imprimirEncabezadoExcel', 'imprimirFechaHoraExcel', 'imprimirNombreUsuarioExcel', 'regEstado'], 'boolean'],
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
	public $idTemplete;
    public function search($params)
    {
        $query = Reportesconfig::find();
		$query->joinWith(['idTemplete']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
		
		$dataProvider->sort->attributes['idTemplete'] = [
				'asc' => ['TemplatesReportes.nombreTemplateReporte' => SORT_ASC],
				'desc' => ['TemplatesReportes.nombreTemplateReporte' => SORT_DESC],
		];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'reporteConfiguracionID' => $this->reporteConfiguracionID,
            //'templateReporteID' => $this->templateReporteID,
            'imprimirLogoPdf' => $this->imprimirLogoPdf,
            'imprimirEncabezado' => $this->imprimirEncabezado,
            'imprimirFechaHora' => $this->imprimirFechaHora,
            'imprimirNombreUsuario' => $this->imprimirNombreUsuario,
            'imprimirLogoExcel' => $this->imprimirLogoExcel,
            'imprimirPie' => $this->imprimirPie,
            'imprimirEncabezadoExcel' => $this->imprimirEncabezadoExcel,
            'imprimirFechaHoraExcel' => $this->imprimirFechaHoraExcel,
            'imprimirNombreUsuarioExcel' => $this->imprimirNombreUsuarioExcel,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'nombreReporte', $this->nombreReporte])
			->andFilterWhere(['like', 'orientacionPagina', $this->orientacionPagina])
            ->andFilterWhere(['like', 'queryReporte', $this->queryReporte])
            ->andFilterWhere(['like', 'columnasReporte', $this->columnasReporte]);
		$query->andFilterWhere(['like', 'TemplatesReportes.nombreTemplateReporte', $this->templateReporteID]);

        $query->andWhere(['=', 'ReportesConfiguraciones.regEstado', '1']);

        return $dataProvider;
    }
}
