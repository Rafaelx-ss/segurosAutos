<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Reportesconfiguraciones;

/**
 * ReportesconfiguracionesSearch represents the model behind the search form of `app\models\Reportesconfiguraciones`.
 */
class ReportesconfiguracionesSearch extends Reportesconfiguraciones
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reporteConfiguracionID', 'templateReporteID', 'nombreReporte', 'queryReporte', 'orientacionPagina', 'columnasReporte', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'safe'],
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


    public function search($params)
    {
        $query = Reportesconfiguraciones::find();
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
            'reporteConfiguracionID' => $this->reporteConfiguracionID,
            'templateReporteID' => $this->templateReporteID,
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
            ->andFilterWhere(['like', 'queryReporte', $this->queryReporte])
            ->andFilterWhere(['like', 'orientacionPagina', $this->orientacionPagina])
            ->andFilterWhere(['like', 'columnasReporte', $this->columnasReporte]);
		$query->andWhere(['=', 'ReportesConfiguraciones.regEstado', '1']);


	return $dataProvider;		
		
    }

	public function searchelimina($params)
    {
        $query = Reportesconfiguraciones::find();
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
            'reporteConfiguracionID' => $this->reporteConfiguracionID,
            'templateReporteID' => $this->templateReporteID,
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
            ->andFilterWhere(['like', 'queryReporte', $this->queryReporte])
            ->andFilterWhere(['like', 'orientacionPagina', $this->orientacionPagina])
            ->andFilterWhere(['like', 'columnasReporte', $this->columnasReporte]);
		$query->andWhere(['=', 'ReportesConfiguraciones.regEstado', '0']);


	return $dataProvider;		
		
    }
}
