<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Conexionesapis;

/**
 * ConexionesapisSearch represents the model behind the search form of `app\models\Conexionesapis`.
 */
class ConexionesapisSearch extends Conexionesapis
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['conexionApiID', 'aplicacionConexionApiID', 'rutaApi', 'usuario', 'password', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'safe'],
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
public $idAplicacionesconexionapi; 


    public function search($params)
    {
        $query = Conexionesapis::find();
		$query->joinWith(['idAplicacionesconexionapi']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->sort->attributes['idAplicacionesconexionapi'] = [
						'asc' => ['AplicacionesConexionApi.descripcion' => SORT_ASC],
						'desc' => ['AplicacionesConexionApi.descripcion' => SORT_DESC],
				];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'conexionApiID' => $this->conexionApiID,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'rutaApi', $this->rutaApi])
            ->andFilterWhere(['like', 'usuario', $this->usuario])
            ->andFilterWhere(['like', 'password', $this->password]);
		$query->andFilterWhere(['like', 'AplicacionesConexionApi.descripcion', $this->aplicacionConexionApiID]);
$query->andWhere(['=', 'ConexionesApis.regEstado', '1']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }

	public function searchelimina($params)
    {
        $query = Conexionesapis::find();
		$query->joinWith(['idAplicacionesconexionapi']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->sort->attributes['idAplicacionesconexionapi'] = [
						'asc' => ['AplicacionesConexionApi.descripcion' => SORT_ASC],
						'desc' => ['AplicacionesConexionApi.descripcion' => SORT_DESC],
				];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'conexionApiID' => $this->conexionApiID,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'rutaApi', $this->rutaApi])
            ->andFilterWhere(['like', 'usuario', $this->usuario])
            ->andFilterWhere(['like', 'password', $this->password]);
		$query->andFilterWhere(['like', 'AplicacionesConexionApi.descripcion', $this->aplicacionConexionApiID]);
$query->andWhere(['=', 'ConexionesApis.regEstado', '0']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }
}
