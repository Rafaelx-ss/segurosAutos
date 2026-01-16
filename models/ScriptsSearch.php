<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Scripts;

/**
 * ScriptsSearch represents the model behind the search form of `app\models\Scripts`.
 */
class ScriptsSearch extends Scripts
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['scriptID', 'aplicacionID', 'version', 'descripcion', 'fechaInicio', 'fechaFin', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'safe'],
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
public $idAplicaciones; 


    public function search($params)
    {
        $query = Scripts::find();
		$query->joinWith(['idAplicaciones']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->sort->attributes['idAplicaciones'] = [
						'asc' => ['Aplicaciones.nombreAplicacion' => SORT_ASC],
						'desc' => ['Aplicaciones.nombreAplicacion' => SORT_DESC],
				];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'scriptID' => $this->scriptID,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'version', $this->version])
            ->andFilterWhere(['like', 'descripcion', $this->descripcion]);
		$query->andFilterWhere(['like', 'Aplicaciones.nombreAplicacion', $this->aplicacionID]);
$query->andFilterWhere(['like', 'fechaInicio', $this->fechaInicio]);
$query->andFilterWhere(['like', 'fechaFin', $this->fechaFin]);
$query->andWhere(['=', 'Scripts.regEstado', '1']);


	return $dataProvider;		
		
    }

	public function searchelimina($params)
    {
        $query = Scripts::find();
		$query->joinWith(['idAplicaciones']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->sort->attributes['idAplicaciones'] = [
						'asc' => ['Aplicaciones.nombreAplicacion' => SORT_ASC],
						'desc' => ['Aplicaciones.nombreAplicacion' => SORT_DESC],
				];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'scriptID' => $this->scriptID,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'version', $this->version])
            ->andFilterWhere(['like', 'descripcion', $this->descripcion]);
		$query->andFilterWhere(['like', 'Aplicaciones.nombreAplicacion', $this->aplicacionID]);
$query->andFilterWhere(['like', 'fechaInicio', $this->fechaInicio]);
$query->andFilterWhere(['like', 'fechaFin', $this->fechaFin]);
$query->andWhere(['=', 'Scripts.regEstado', '0']);


	return $dataProvider;		
		
    }
}
