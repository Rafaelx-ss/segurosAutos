<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Apiconfiguraciones;

/**
 * ApiconfiguracionesSearch represents the model behind the search form of `app\models\Apiconfiguraciones`.
 */
class ApiconfiguracionesSearch extends Apiconfiguraciones
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['apiListaConfiguracionID', 'usuarioApiLista', 'passwordApiLista', 'rutaApiLista', 'identificadorApiLista', 'tipoSolicitudApiLista', 'aplicacionID', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'safe'],
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
        $query = Apiconfiguraciones::find();
		$query->joinWith(['idAplicaciones']);
		        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
		
		$dataProvider->sort->attributes['idAplicaciones'] = [
            // se agregan los atributos relacionados en las tablas
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
            'apiListaConfiguracionID' => $this->apiListaConfiguracionID,
            //'aplicacionID' => $this->aplicacionID,
            'versionActual' => $this->versionActual,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'usuarioApiLista', $this->usuarioApiLista])
			->andFilterWhere(['like', 'Aplicaciones.nombreAplicacion', $this->aplicacionID])
            ->andFilterWhere(['like', 'passwordApiLista', $this->passwordApiLista])
            ->andFilterWhere(['like', 'rutaApiLista', $this->rutaApiLista])
            ->andFilterWhere(['like', 'identificadorApiLista', $this->identificadorApiLista])
            ->andFilterWhere(['like', 'tipoSolicitudApiLista', $this->tipoSolicitudApiLista]);

        $query->andWhere(['=', 'ApiListaConfiguraciones.regEstado', '1']);
		

	return $dataProvider;		
		
    }
}
