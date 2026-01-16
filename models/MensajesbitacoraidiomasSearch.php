<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Mensajesbitacoraidiomas;

/**
 * MensajesbitacoraidiomasSearch represents the model behind the search form of `app\models\Mensajesbitacoraidiomas`.
 */
class MensajesbitacoraidiomasSearch extends Mensajesbitacoraidiomas
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mensajeBitacoraIdiomasID', 'mensaje', 'mensajeBitacoraID', 'idiomaID', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'safe'],
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
public $idMensajesbitacora; 
public $idIdiomas; 


    public function search($params)
    {
        $query = Mensajesbitacoraidiomas::find();
		$query->joinWith(['idMensajesbitacora']);
$query->joinWith(['idIdiomas']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->sort->attributes['idMensajesbitacora'] = [
						'asc' => ['MensajesBitacora.nombreMensaje' => SORT_ASC],
						'desc' => ['MensajesBitacora.nombreMensaje' => SORT_DESC],
				];
$dataProvider->sort->attributes['idIdiomas'] = [
						'asc' => ['Idiomas.nombreIdioma' => SORT_ASC],
						'desc' => ['Idiomas.nombreIdioma' => SORT_DESC],
				];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'mensajeBitacoraIdiomasID' => $this->mensajeBitacoraIdiomasID,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'mensaje', $this->mensaje]);
		$query->andFilterWhere(['like', 'MensajesBitacora.nombreMensaje', $this->mensajeBitacoraID]);
$query->andFilterWhere(['like', 'Idiomas.nombreIdioma', $this->idiomaID]);
$query->andWhere(['=', 'MensajesBitacoraIdiomas.regEstado', '1']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }

	public function searchelimina($params)
    {
        $query = Mensajesbitacoraidiomas::find();
		$query->joinWith(['idMensajesbitacora']);
$query->joinWith(['idIdiomas']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->sort->attributes['idMensajesbitacora'] = [
						'asc' => ['MensajesBitacora.nombreMensaje' => SORT_ASC],
						'desc' => ['MensajesBitacora.nombreMensaje' => SORT_DESC],
				];
$dataProvider->sort->attributes['idIdiomas'] = [
						'asc' => ['Idiomas.nombreIdioma' => SORT_ASC],
						'desc' => ['Idiomas.nombreIdioma' => SORT_DESC],
				];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'mensajeBitacoraIdiomasID' => $this->mensajeBitacoraIdiomasID,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'mensaje', $this->mensaje]);
		$query->andFilterWhere(['like', 'MensajesBitacora.nombreMensaje', $this->mensajeBitacoraID]);
$query->andFilterWhere(['like', 'Idiomas.nombreIdioma', $this->idiomaID]);
$query->andWhere(['=', 'MensajesBitacoraIdiomas.regEstado', '0']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }
}
