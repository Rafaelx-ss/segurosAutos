<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Formularios;

/**
 * FormulariosSearch represents the model behind the search form of `app\models\Formularios`.
 */
class FormulariosSearch extends Formularios
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['formularioID', 'orden', 'menuID', 'aplicacionID', 'catalogoID', 'textoID', 'tipoFormularioID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['tipoMenu', 'nombreFormulario', 'urlArchivo', 'icono', 'regFechaUltimaModificacion', 'formID'], 'safe'],
            [['estadoFormulario', 'regEstado'], 'boolean'],
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
        $query = Formularios::find();

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
            'formularioID' => $this->formularioID,
            'estadoFormulario' => $this->estadoFormulario,
            'orden' => $this->orden,
            'menuID' => $this->menuID,
            'aplicacionID' => $this->aplicacionID,
            'catalogoID' => $this->catalogoID,
            'textoID' => $this->textoID,
            'tipoFormularioID' => $this->tipoFormularioID,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
			'formID'=>$this->formID,
        ]);

        $query->andFilterWhere(['like', 'nombreFormulario', $this->nombreFormulario])
			->andFilterWhere(['like', 'tipoMenu', $this->tipoMenu])
            ->andFilterWhere(['like', 'urlArchivo', $this->urlArchivo])
            ->andFilterWhere(['like', 'icono', $this->icono])
			->andWhere(['=', 'regEstado', '1']);

        return $dataProvider;
    }
}
