<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Feformapago;

/**
 * FeformapagoSearch represents the model behind the search form of `app\models\Feformapago`.
 */
class FeformapagoSearch extends Feformapago
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['formaPagoID', 'formaPago', 'descripcion', 'bancarizado', 'numeroOperacion', 'rfcEmisorCuentaOrdenante', 'cuentaOrdenante', 'patronCuentaOrdenante', 'rfcEmisorCuentaBeneficiario', 'cuentaBenenficiario', 'patronCuentaBeneficiaria', 'tipoCadenaPago', 'nombreBancoEmisorCuentaOrdenanteEnCasoExtranjero', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'safe'],
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
        $query = Feformapago::find();
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
            'formaPagoID' => $this->formaPagoID,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'formaPago', $this->formaPago])
            ->andFilterWhere(['like', 'descripcion', $this->descripcion])
            ->andFilterWhere(['like', 'bancarizado', $this->bancarizado])
            ->andFilterWhere(['like', 'numeroOperacion', $this->numeroOperacion])
            ->andFilterWhere(['like', 'rfcEmisorCuentaOrdenante', $this->rfcEmisorCuentaOrdenante])
            ->andFilterWhere(['like', 'cuentaOrdenante', $this->cuentaOrdenante])
            ->andFilterWhere(['like', 'patronCuentaOrdenante', $this->patronCuentaOrdenante])
            ->andFilterWhere(['like', 'rfcEmisorCuentaBeneficiario', $this->rfcEmisorCuentaBeneficiario])
            ->andFilterWhere(['like', 'cuentaBenenficiario', $this->cuentaBenenficiario])
            ->andFilterWhere(['like', 'patronCuentaBeneficiaria', $this->patronCuentaBeneficiaria])
            ->andFilterWhere(['like', 'tipoCadenaPago', $this->tipoCadenaPago])
            ->andFilterWhere(['like', 'nombreBancoEmisorCuentaOrdenanteEnCasoExtranjero', $this->nombreBancoEmisorCuentaOrdenanteEnCasoExtranjero]);
		$query->andWhere(['=', 'FEFormaPago.regEstado', '1']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }

	public function searchelimina($params)
    {
        $query = Feformapago::find();
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
            'formaPagoID' => $this->formaPagoID,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'formaPago', $this->formaPago])
            ->andFilterWhere(['like', 'descripcion', $this->descripcion])
            ->andFilterWhere(['like', 'bancarizado', $this->bancarizado])
            ->andFilterWhere(['like', 'numeroOperacion', $this->numeroOperacion])
            ->andFilterWhere(['like', 'rfcEmisorCuentaOrdenante', $this->rfcEmisorCuentaOrdenante])
            ->andFilterWhere(['like', 'cuentaOrdenante', $this->cuentaOrdenante])
            ->andFilterWhere(['like', 'patronCuentaOrdenante', $this->patronCuentaOrdenante])
            ->andFilterWhere(['like', 'rfcEmisorCuentaBeneficiario', $this->rfcEmisorCuentaBeneficiario])
            ->andFilterWhere(['like', 'cuentaBenenficiario', $this->cuentaBenenficiario])
            ->andFilterWhere(['like', 'patronCuentaBeneficiaria', $this->patronCuentaBeneficiaria])
            ->andFilterWhere(['like', 'tipoCadenaPago', $this->tipoCadenaPago])
            ->andFilterWhere(['like', 'nombreBancoEmisorCuentaOrdenanteEnCasoExtranjero', $this->nombreBancoEmisorCuentaOrdenanteEnCasoExtranjero]);
		$query->andWhere(['=', 'FEFormaPago.regEstado', '0']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }
}
