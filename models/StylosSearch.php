<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Stylos;

/**
 * StylosSearch represents the model behind the search form of `app\models\Stylos`.
 */
class StylosSearch extends Stylos
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['configuracionesSistemaID', 'logoLogin', 'logoBanner', 'iconoMenu', 'temaBanner', 'temaMenu', 'temaContenido', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion', 'logoFooter', 'favIcon', 'titlePagina', 'footerPagina', 'btnAccion', 'btnSave', 'btnMenu', 'tiempoSesion'], 'safe'],
            [['activoConfiguracionesSistema', 'regEstado'], 'boolean'],
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
        $query = Stylos::find();

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
            'configuracionesSistemaID' => $this->configuracionesSistemaID,
            'activoConfiguracionesSistema' => $this->activoConfiguracionesSistema,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'logoLogin', $this->logoLogin])
            ->andFilterWhere(['like', 'logoBanner', $this->logoBanner])
            ->andFilterWhere(['like', 'iconoMenu', $this->iconoMenu])
            ->andFilterWhere(['like', 'temaBanner', $this->temaBanner])
            ->andFilterWhere(['like', 'temaMenu', $this->temaMenu])
            ->andFilterWhere(['like', 'temaContenido', $this->temaContenido])
			->andWhere(['=', 'regEstado', '1']);

        return $dataProvider;
    }
}
