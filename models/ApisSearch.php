<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Apis;

/**
 * ApisSearch represents the model behind the search form of `app\models\Apis`.
 */
class ApisSearch extends Apis
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['apiID', 'nombreApi', 'versionRegistro', 'regEstado', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion', 'rutaApi', 'aplicacionID', 'tipoLista'], 'safe'],
            [['estadoApi'], 'boolean'],
            [['ordenMigracion'], 'number'],
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
        $query = Apis::find();
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
            'apiID' => $this->apiID,
            'estadoApi' => $this->estadoApi,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
            'ordenMigracion' => $this->ordenMigracion,
        ]);

        $query->andFilterWhere(['like', 'nombreApi', $this->nombreApi])
            ->andFilterWhere(['like', 'rutaApi', $this->rutaApi])
            ->andFilterWhere(['like', 'tipoLista', $this->tipoLista]);
		$query->andFilterWhere(['like', 'Aplicaciones.nombreAplicacion', $this->aplicacionID]);
$query->andWhere(['=', 'Apis.regEstado', '1']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }

	public function searchelimina($params)
    {
        $query = Apis::find();
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
            'apiID' => $this->apiID,
            'estadoApi' => $this->estadoApi,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
            'ordenMigracion' => $this->ordenMigracion,
        ]);

        $query->andFilterWhere(['like', 'nombreApi', $this->nombreApi])
            ->andFilterWhere(['like', 'rutaApi', $this->rutaApi])
            ->andFilterWhere(['like', 'tipoLista', $this->tipoLista]);
		$query->andFilterWhere(['like', 'Aplicaciones.nombreAplicacion', $this->aplicacionID]);
$query->andWhere(['=', 'Apis.regEstado', '0']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }
}
