<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Templatereporte;

/**
 * TemplatereporteSearch represents the model behind the search form of `app\models\Templatereporte`.
 */
class TemplatereporteSearch extends Templatereporte
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['templateReporteID', 'nombreTemplateReporte', 'logoTemplateReporte', 'encabezadoTemplateReporte', 'pieTemplateReporteL1', 'pieTemplateReporteL2', 'pieTemplateReporteL3', 'colorLinea', 'colorTituloTabla', 'colorTituloTexto', 'colorTextoFooter', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'safe'],
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
    public function search($params)
    {
        $query = Templatereporte::find();

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
            'templateReporteID' => $this->templateReporteID,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'nombreTemplateReporte', $this->nombreTemplateReporte])
            ->andFilterWhere(['like', 'logoTemplateReporte', $this->logoTemplateReporte])
            ->andFilterWhere(['like', 'encabezadoTemplateReporte', $this->encabezadoTemplateReporte])
            ->andFilterWhere(['like', 'pieTemplateReporteL1', $this->pieTemplateReporteL1])
            ->andFilterWhere(['like', 'pieTemplateReporteL2', $this->pieTemplateReporteL2])
            ->andFilterWhere(['like', 'pieTemplateReporteL3', $this->pieTemplateReporteL3])
            ->andFilterWhere(['like', 'colorLinea', $this->colorLinea])
            ->andFilterWhere(['like', 'colorTituloTabla', $this->colorTituloTabla])
            ->andFilterWhere(['like', 'colorTituloTexto', $this->colorTituloTexto])
            ->andFilterWhere(['like', 'colorTextoFooter', $this->colorTextoFooter]);

        $query->andWhere(['=', 'regEstado', '1']);

        return $dataProvider;
    }
}
