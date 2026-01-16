<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Usuarios;

/**
 * UsuariosSearch represents the model behind the search form of `app\models\Usuarios`.
 */
class UsuariosSearch extends Usuarios
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['usuarioID', 'activoUsuario', 'intentosValidos', 'versionRegistro', 'regEstado', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['nombreUsuario', 'passw', 'usuario', 'correoUsuario', 'codigoRecuperacionPassw', 'fechaGeneracionCodigoRecuperacionPassw', 'regFechaUltimaModificacion', 'AuthKey', 'fechaActualizaPass', 'primerLogin', 'cambioPass'], 'safe'],
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
        $query = Usuarios::find();

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
            'usuarioID' => $this->usuarioID,
            'activoUsuario' => $this->activoUsuario,
            'fechaGeneracionCodigoRecuperacionPassw' => $this->fechaGeneracionCodigoRecuperacionPassw,
            'intentosValidos' => $this->intentosValidos,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'nombreUsuario', $this->nombreUsuario])
            ->andFilterWhere(['like', 'passw', $this->passw])
            ->andFilterWhere(['like', 'usuario', $this->usuario])
            ->andFilterWhere(['like', 'correoUsuario', $this->correoUsuario])
            ->andFilterWhere(['like', 'codigoRecuperacionPassw', $this->codigoRecuperacionPassw])
            ->andFilterWhere(['like', 'AuthKey', $this->AuthKey])
            ->andWhere(['=', 'regEstado', '1']);

        return $dataProvider;
    }
}
