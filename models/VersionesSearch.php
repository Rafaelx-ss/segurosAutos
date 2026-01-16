<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Versiones;

/**
 * VersionesSearch represents the model behind the search form of `app\models\Versiones`.
 */
class VersionesSearch extends Versiones
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['versionID', 'version', 'fechaLiberacionVersion', 'aliasVersion', 'urlVersion', 'urlDocumentacionVersion', 'aplicacionID', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'safe'],
            [['versionActual', 'regEstado'], 'boolean'],
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
        $query = Versiones::find();
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
            'versionID' => $this->versionID,
            'versionActual' => $this->versionActual,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'version', $this->version])
            ->andFilterWhere(['like', 'aliasVersion', $this->aliasVersion])
            ->andFilterWhere(['like', 'urlVersion', $this->urlVersion])
            ->andFilterWhere(['like', 'urlDocumentacionVersion', $this->urlDocumentacionVersion]);
		$query->andFilterWhere(['like', 'fechaLiberacionVersion', $this->fechaLiberacionVersion]);
$query->andFilterWhere(['like', 'Aplicaciones.nombreAplicacion', $this->aplicacionID]);
$query->andWhere(['=', 'Versiones.regEstado', '1']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }

	public function searchelimina($params)
    {
        $query = Versiones::find();
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
            'versionID' => $this->versionID,
            'versionActual' => $this->versionActual,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'version', $this->version])
            ->andFilterWhere(['like', 'aliasVersion', $this->aliasVersion])
            ->andFilterWhere(['like', 'urlVersion', $this->urlVersion])
            ->andFilterWhere(['like', 'urlDocumentacionVersion', $this->urlDocumentacionVersion]);
		$query->andFilterWhere(['like', 'fechaLiberacionVersion', $this->fechaLiberacionVersion]);
$query->andFilterWhere(['like', 'Aplicaciones.nombreAplicacion', $this->aplicacionID]);
$query->andWhere(['=', 'Versiones.regEstado', '0']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }
}
