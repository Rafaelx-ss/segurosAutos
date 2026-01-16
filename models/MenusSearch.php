<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Menus;

/**
 * MenusSearch represents the model behind the search form of `app\models\Menus`.
 */
class MenusSearch extends Menus
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['menuID', 'menuPadre', 'orden', 'versionRegistro', 'regEstado', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['nombreMenu', 'urlPagina', 'imagen', 'regFechaUltimaModificacion', 'textoID'], 'safe'],
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
        $query = Menus::find();

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
            'menuID' => $this->menuID,
            'menuPadre' => $this->menuPadre,
            'orden' => $this->orden,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'nombreMenu', $this->nombreMenu])
            ->andFilterWhere(['like', 'urlPagina', $this->urlPagina])
            ->andFilterWhere(['like', 'imagen', $this->imagen])
			->andWhere(['=', 'regEstado', '1']);

        return $dataProvider;
    }
}
