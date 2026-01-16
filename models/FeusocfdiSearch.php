<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Feusocfdi;

/**
 * FeusocfdiSearch represents the model behind the search form of `app\models\Feusocfdi`.
 */
class FeusocfdiSearch extends Feusocfdi
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['usoCfdiID', 'usoCFDIClave', 'usoCFDIDescripcion', 'fechaInicioDeVigencia', 'fechaFinDeVigencia', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'safe'],
            [['fisica', 'moral', 'regEstado'], 'boolean'],
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
        $query = Feusocfdi::find();
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
            'usoCfdiID' => $this->usoCfdiID,
            'fisica' => $this->fisica,
            'moral' => $this->moral,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'usoCFDIClave', $this->usoCFDIClave])
            ->andFilterWhere(['like', 'usoCFDIDescripcion', $this->usoCFDIDescripcion]);

        $query->andWhere(['=', 'FEUsoCFDI.regEstado', '1']);
		$query->andFilterWhere(['like', 'fechaInicioDeVigencia', $this->fechaInicioDeVigencia]);
$query->andFilterWhere(['like', 'fechaFinDeVigencia', $this->fechaFinDeVigencia]);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }
}
