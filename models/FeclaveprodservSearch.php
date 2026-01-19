<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Feclaveprodserv;

/**
 * FeclaveprodservSearch represents the model behind the search form of `app\models\Feclaveprodserv`.
 */
class FeclaveprodservSearch extends Feclaveprodserv
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['claveProdServID', 'claveProdServ', 'descripcion', 'fechaDeInicioDeVigencia', 'fechaDeFinDeVigencia', 'incluirIVATraslado', 'incluirIEPSTraslado', 'complementoQueDebeIncluir', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'safe'],
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
        $query = Feclaveprodserv::find();
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
            'claveProdServID' => $this->claveProdServID,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'claveProdServ', $this->claveProdServ])
            ->andFilterWhere(['like', 'descripcion', $this->descripcion])
            ->andFilterWhere(['like', 'incluirIVATraslado', $this->incluirIVATraslado])
            ->andFilterWhere(['like', 'incluirIEPSTraslado', $this->incluirIEPSTraslado])
            ->andFilterWhere(['like', 'complementoQueDebeIncluir', $this->complementoQueDebeIncluir]);
		$query->andFilterWhere(['like', 'fechaDeInicioDeVigencia', $this->fechaDeInicioDeVigencia]);
$query->andFilterWhere(['like', 'fechaDeFinDeVigencia', $this->fechaDeFinDeVigencia]);
$query->andWhere(['=', 'FEClaveProdServ.regEstado', '1']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }

	public function searchelimina($params)
    {
        $query = Feclaveprodserv::find();
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
            'claveProdServID' => $this->claveProdServID,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'claveProdServ', $this->claveProdServ])
            ->andFilterWhere(['like', 'descripcion', $this->descripcion])
            ->andFilterWhere(['like', 'incluirIVATraslado', $this->incluirIVATraslado])
            ->andFilterWhere(['like', 'incluirIEPSTraslado', $this->incluirIEPSTraslado])
            ->andFilterWhere(['like', 'complementoQueDebeIncluir', $this->complementoQueDebeIncluir]);
		$query->andFilterWhere(['like', 'fechaDeInicioDeVigencia', $this->fechaDeInicioDeVigencia]);
$query->andFilterWhere(['like', 'fechaDeFinDeVigencia', $this->fechaDeFinDeVigencia]);
$query->andWhere(['=', 'FEClaveProdServ.regEstado', '0']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }
}
