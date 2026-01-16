<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Configestaciones;

/**
 * ConfigestacionesSearch represents the model behind the search form of `app\models\Configestaciones`.
 */
class ConfigestacionesSearch extends Configestaciones
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['configEstacionID', 'colorBombasc', 'tamanoBomba', 'logoIzquierda', 'logoDerecha', 'titulo', 'numRegistros', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'safe'],
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
        $query = Configestaciones::find();

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
            'configEstacionID' => $this->configEstacionID,
            'tamanoBomba' => $this->tamanoBomba,
            'numRegistros' => $this->numRegistros,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'colorBombasc', $this->colorBombasc])
            ->andFilterWhere(['like', 'logoIzquierda', $this->logoIzquierda])
            ->andFilterWhere(['like', 'logoDerecha', $this->logoDerecha])
            ->andFilterWhere(['like', 'titulo', $this->titulo]);

        $query->andWhere(['=', 'regEstado', '1']);

        return $dataProvider;
    }
}
