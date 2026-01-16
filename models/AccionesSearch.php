<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Acciones;

/**
 * AccionesSearch represents the model behind the search form of `app\models\Acciones`.
 */
class AccionesSearch extends Acciones
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['accionID', 'textoID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['nombreAccion', 'imagen', 'regFechaUltimaModificacion', 'paginaAccion'], 'safe'],
            [['estadoAccion', 'regEstado'], 'boolean'],
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
        $query = Acciones::find();

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
            'accionID' => $this->accionID,
            'estadoAccion' => $this->estadoAccion,
            'textoID' => $this->textoID,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'nombreAccion', $this->nombreAccion])
            ->andFilterWhere(['like', 'imagen', $this->imagen])
			->andFilterWhere(['like', 'paginaAccion', $this->paginaAccion])
			->andWhere(['=', 'regEstado', '1']);

        return $dataProvider;
    }
}
