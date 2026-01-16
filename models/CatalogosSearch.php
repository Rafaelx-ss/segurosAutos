<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Catalogos;

/**
 * CatalogosSearch represents the model behind the search form of `app\models\Catalogos`.
 */
class CatalogosSearch extends Catalogos
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['catalogoID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['nombreCatalogo', 'nombreModelo', 'sqlQuery', 'regFechaUltimaModificacion'], 'safe'],
            [['regEstado', 'activoCatalogo'], 'boolean'],
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
        $query = Catalogos::find();

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
            'catalogoID' => $this->catalogoID,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            //'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
            'activoCatalogo' => $this->activoCatalogo,
        ]);

        $query->andFilterWhere(['like', 'nombreCatalogo', $this->nombreCatalogo])
			->andFilterWhere(['like', 'nombreModelo', $this->nombreModelo])			
            ->andFilterWhere(['like', 'sqlQuery', $this->sqlQuery])
			->andFilterWhere(['>=', 'regFechaUltimaModificacion', $this->regFechaUltimaModificacion])
			->andWhere(['=', 'regEstado', 1]);
		
		

        return $dataProvider;
    }
}
