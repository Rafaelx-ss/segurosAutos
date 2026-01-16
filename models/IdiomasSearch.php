<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Idiomas;

/**
 * IdiomasSearch represents the model behind the search form of `app\models\Idiomas`.
 */
class IdiomasSearch extends Idiomas
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idiomaID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['iconIdioma', 'nombreIdioma', 'regFechaUltimaModificacion'], 'safe'],
            [['activoIdioma', 'regEstado'], 'boolean'],
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
        $query = Idiomas::find();

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
            'idiomaID' => $this->idiomaID,
            'activoIdioma' => $this->activoIdioma,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'iconIdioma', $this->iconIdioma])
            ->andFilterWhere(['like', 'nombreIdioma', $this->nombreIdioma])
			->andWhere(['=', 'regEstado', '1']);

        return $dataProvider;
    }
}
