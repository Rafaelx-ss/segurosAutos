<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Textos;

/**
 * TextosSearch represents the model behind the search form of `app\models\Textos`.
 */
class TextosSearch extends Textos
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['textoID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['tipoTexto','nombreTexto', 'regFechaUltimaModificacion'], 'safe'],
            [['activoTexto', 'regEstado'], 'boolean'],
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
        $query = Textos::find();

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
            'textoID' => $this->textoID,
            'activoTexto' => $this->activoTexto,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'nombreTexto', $this->nombreTexto])
			->andFilterWhere(['like', 'tipoTexto', $this->tipoTexto])
			->andWhere(['=', 'regEstado', '1']);
		
				
		if(isset($_GET['tipo'])){
			if($_GET['tipo'] == 'Botones'){
				$query->andWhere(['=', 'tipoTexto', 'Botones']);
			}elseif($_GET['tipo'] == 'Mensajes'){
				$query->andWhere(['=', 'tipoTexto', 'Mensajes']);
			}elseif($_GET['tipo'] == 'Catalogos'){
				$query->andWhere(['=', 'tipoTexto', 'Catalogos']);
			}elseif($_GET['tipo'] == 'Menus'){
				$query->andWhere(['=', 'tipoTexto', 'Menus']);
			}else{
				$query->andWhere(['=', 'tipoTexto', 'Catalogos']);
			}
		}else{
			$query->andWhere(['=', 'tipoTexto', 'Catalogos']);
		}

        return $dataProvider;
    }
}
