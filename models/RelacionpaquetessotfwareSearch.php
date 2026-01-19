<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Relacionpaquetessotfware;

/**
 * RelacionpaquetessotfwareSearch represents the model behind the search form of `app\models\Relacionpaquetessotfware`.
 */
class RelacionpaquetessotfwareSearch extends Relacionpaquetessotfware
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['relacionPaqueteSotfwareID', 'paqueteKairosID', 'softwareKairosID', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'safe'],
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
public $idPaqueteskairos; 
public $idSoftwarekairos; 


    public function search($params)
    {
        $query = Relacionpaquetessotfware::find();
		$query->joinWith(['idPaqueteskairos']);
$query->joinWith(['idSoftwarekairos']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->sort->attributes['idPaqueteskairos'] = [
						'asc' => ['PaquetesKairos.nombrePaquete' => SORT_ASC],
						'desc' => ['PaquetesKairos.nombrePaquete' => SORT_DESC],
				];
$dataProvider->sort->attributes['idSoftwarekairos'] = [
						'asc' => ['SoftwareKairos.nombreSoftware' => SORT_ASC],
						'desc' => ['SoftwareKairos.nombreSoftware' => SORT_DESC],
				];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'relacionPaqueteSotfwareID' => $this->relacionPaqueteSotfwareID,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);
		$query->andFilterWhere(['like', 'PaquetesKairos.nombrePaquete', $this->paqueteKairosID]);
$query->andFilterWhere(['like', 'SoftwareKairos.nombreSoftware', $this->softwareKairosID]);
$query->andWhere(['=', 'RelacionPaquetesSotfware.regEstado', '1']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }

	public function searchelimina($params)
    {
        $query = Relacionpaquetessotfware::find();
		$query->joinWith(['idPaqueteskairos']);
$query->joinWith(['idSoftwarekairos']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->sort->attributes['idPaqueteskairos'] = [
						'asc' => ['PaquetesKairos.nombrePaquete' => SORT_ASC],
						'desc' => ['PaquetesKairos.nombrePaquete' => SORT_DESC],
				];
$dataProvider->sort->attributes['idSoftwarekairos'] = [
						'asc' => ['SoftwareKairos.nombreSoftware' => SORT_ASC],
						'desc' => ['SoftwareKairos.nombreSoftware' => SORT_DESC],
				];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'relacionPaqueteSotfwareID' => $this->relacionPaqueteSotfwareID,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);
		$query->andFilterWhere(['like', 'PaquetesKairos.nombrePaquete', $this->paqueteKairosID]);
$query->andFilterWhere(['like', 'SoftwareKairos.nombreSoftware', $this->softwareKairosID]);
$query->andWhere(['=', 'RelacionPaquetesSotfware.regEstado', '0']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }
}
