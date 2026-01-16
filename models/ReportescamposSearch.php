<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Reportescampos;

/**
 * ReportescamposSearch represents the model behind the search form of `app\models\Reportescampos`.
 */
class ReportescamposSearch extends Reportescampos
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reporteCampoID', 'reporteConfiguracionID', 'nombreCampo', 'orden', 'textoID', 'tipoControl', 'controlQuery', 'queryValor', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion', 'aliasTabla', 'sumarCampo'], 'safe'],
            [['visible', 'searchVisible', 'regEstado'], 'boolean'],
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
        $query = Reportescampos::find();
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
            'reporteCampoID' => $this->reporteCampoID,
            'reporteConfiguracionID' => $this->reporteConfiguracionID,
            'visible' => $this->visible,
            'searchVisible' => $this->searchVisible,
            'orden' => $this->orden,
            'textoID' => $this->textoID,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'nombreCampo', $this->nombreCampo])
            ->andFilterWhere(['like', 'tipoControl', $this->tipoControl])	
			->andFilterWhere(['like', 'sumarCampo', $this->sumarCampo])
			->andFilterWhere(['like', 'aliasTabla', $this->aliasTabla])
            ->andFilterWhere(['like', 'controlQuery', $this->controlQuery])
            ->andFilterWhere(['like', 'queryValor', $this->queryValor]);

        $query->andWhere(['=', 'regEstado', '1']);
		$query->andWhere(['=', 'reporteConfiguracionID', $_GET['token']]);
		

	return $dataProvider;		
		
    }
}
