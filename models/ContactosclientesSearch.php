<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Contactosclientes;

/**
 * ContactosclientesSearch represents the model behind the search form of `app\models\Contactosclientes`.
 */
class ContactosclientesSearch extends Contactosclientes
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['contactoClienteID', 'contactoClienteNombre', 'clienteID', 'email', 'telefono', 'celular', 'tipoContactoID', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'safe'],
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
        $query = Contactosclientes::find();
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
            'contactoClienteID' => $this->contactoClienteID,
            'clienteID' => $this->clienteID,
            'tipoContactoID' => $this->tipoContactoID,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'contactoClienteNombre', $this->contactoClienteNombre])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'telefono', $this->telefono])
            ->andFilterWhere(['like', 'celular', $this->celular]);
		$query->andWhere(['=', 'ContactosClientes.regEstado', '1']);


	return $dataProvider;		
		
    }

	public function searchelimina($params)
    {
        $query = Contactosclientes::find();
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
            'contactoClienteID' => $this->contactoClienteID,
            'clienteID' => $this->clienteID,
            'tipoContactoID' => $this->tipoContactoID,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'contactoClienteNombre', $this->contactoClienteNombre])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'telefono', $this->telefono])
            ->andFilterWhere(['like', 'celular', $this->celular]);
		$query->andWhere(['=', 'ContactosClientes.regEstado', '0']);


	return $dataProvider;		
		
    }
}
