<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Apisaplicaciones;

/**
 * ApisaplicacionesSearch represents the model behind the search form of `app\models\Apisaplicaciones`.
 */
class ApisaplicacionesSearch extends Apisaplicaciones
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['apiAplicacionID', 'apiEndPoint', 'tiposolicitud', 'rutaAplicacionID', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'safe'],
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
public $idRutasapisaplicaciones; 


    public function search($params)
    {
        $query = Apisaplicaciones::find();
		$query->joinWith(['idRutasapisaplicaciones']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->sort->attributes['idRutasapisaplicaciones'] = [
						'asc' => ['RutasApisAplicaciones.rutaAplicacionDescripcion' => SORT_ASC],
						'desc' => ['RutasApisAplicaciones.rutaAplicacionDescripcion' => SORT_DESC],
				];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'apiAplicacionID' => $this->apiAplicacionID,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'apiEndPoint', $this->apiEndPoint])
            ->andFilterWhere(['like', 'tiposolicitud', $this->tiposolicitud]);
		$query->andFilterWhere(['like', 'RutasApisAplicaciones.rutaAplicacionDescripcion', $this->rutaAplicacionID]);
$query->andWhere(['=', 'ApisAplicaciones.regEstado', '1']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }

	public function searchelimina($params)
    {
        $query = Apisaplicaciones::find();
		$query->joinWith(['idRutasapisaplicaciones']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->sort->attributes['idRutasapisaplicaciones'] = [
						'asc' => ['RutasApisAplicaciones.rutaAplicacionDescripcion' => SORT_ASC],
						'desc' => ['RutasApisAplicaciones.rutaAplicacionDescripcion' => SORT_DESC],
				];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'apiAplicacionID' => $this->apiAplicacionID,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'apiEndPoint', $this->apiEndPoint])
            ->andFilterWhere(['like', 'tiposolicitud', $this->tiposolicitud]);
		$query->andFilterWhere(['like', 'RutasApisAplicaciones.rutaAplicacionDescripcion', $this->rutaAplicacionID]);
$query->andWhere(['=', 'ApisAplicaciones.regEstado', '0']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }
}
