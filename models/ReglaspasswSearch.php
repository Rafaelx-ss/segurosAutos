<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Reglaspassw;

/**
 * ReglaspasswSearch represents the model behind the search form of `app\models\Reglaspassw`.
 */
class ReglaspasswSearch extends Reglaspassw
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['minimioLongitudPassw', 'maximoIntentosFallidos', 'tiempoCaducidadCodigoRecuperacionPassw', 'tiempoCaducidadInactivadadPassw', 'contieneMayuscula', 'contieneMinusculas', 'contieneCaracteresEspeciales', 'contieneNumeros', 'duracionActualizaPass', 'cantidadPassRepetidos', 'tiempoAlmacenadoPassword', 'cantidadRepetidos', 'cantidadConsecutivos', 'versionRegistro', 'regEstado', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'safe'],
            [['contieneRepetidos', 'contieneConsecutivos'], 'boolean'],
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
        $query = Reglaspassw::find();
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
            'minimioLongitudPassw' => $this->minimioLongitudPassw,
            'maximoIntentosFallidos' => $this->maximoIntentosFallidos,
            'tiempoCaducidadCodigoRecuperacionPassw' => $this->tiempoCaducidadCodigoRecuperacionPassw,
            'tiempoCaducidadInactivadadPassw' => $this->tiempoCaducidadInactivadadPassw,
            'contieneMayuscula' => $this->contieneMayuscula,
            'contieneMinusculas' => $this->contieneMinusculas,
            'contieneCaracteresEspeciales' => $this->contieneCaracteresEspeciales,
            'contieneNumeros' => $this->contieneNumeros,
            'duracionActualizaPass' => $this->duracionActualizaPass,
            'cantidadPassRepetidos' => $this->cantidadPassRepetidos,
            'tiempoAlmacenadoPassword' => $this->tiempoAlmacenadoPassword,
            'contieneRepetidos' => $this->contieneRepetidos,
            'cantidadRepetidos' => $this->cantidadRepetidos,
            'contieneConsecutivos' => $this->contieneConsecutivos,
            'cantidadConsecutivos' => $this->cantidadConsecutivos,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);
		$query->andWhere(['=', 'ReglasPassw.regEstado', '1']);


	return $dataProvider;		
		
    }

	public function searchelimina($params)
    {
        $query = Reglaspassw::find();
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
            'minimioLongitudPassw' => $this->minimioLongitudPassw,
            'maximoIntentosFallidos' => $this->maximoIntentosFallidos,
            'tiempoCaducidadCodigoRecuperacionPassw' => $this->tiempoCaducidadCodigoRecuperacionPassw,
            'tiempoCaducidadInactivadadPassw' => $this->tiempoCaducidadInactivadadPassw,
            'contieneMayuscula' => $this->contieneMayuscula,
            'contieneMinusculas' => $this->contieneMinusculas,
            'contieneCaracteresEspeciales' => $this->contieneCaracteresEspeciales,
            'contieneNumeros' => $this->contieneNumeros,
            'duracionActualizaPass' => $this->duracionActualizaPass,
            'cantidadPassRepetidos' => $this->cantidadPassRepetidos,
            'tiempoAlmacenadoPassword' => $this->tiempoAlmacenadoPassword,
            'contieneRepetidos' => $this->contieneRepetidos,
            'cantidadRepetidos' => $this->cantidadRepetidos,
            'contieneConsecutivos' => $this->contieneConsecutivos,
            'cantidadConsecutivos' => $this->cantidadConsecutivos,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);
		$query->andWhere(['=', 'ReglasPassw.regEstado', '0']);


	return $dataProvider;		
		
    }
}
