<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Menuspos;

/**
 * MenusposSearch represents the model behind the search form of `app\models\Menuspos`.
 */
class MenusposSearch extends Menuspos
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['menuPOSID', 'nombreMenuPOS', 'tipoPosID', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'safe'],
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
public $idTipospos; 


    public function search($params)
    {
        $query = Menuspos::find();
		$query->joinWith(['idTipospos']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->sort->attributes['idTipospos'] = [
						'asc' => ['TiposPOS.descripcionTipoPOS' => SORT_ASC],
						'desc' => ['TiposPOS.descripcionTipoPOS' => SORT_DESC],
				];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'menuPOSID' => $this->menuPOSID,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'nombreMenuPOS', $this->nombreMenuPOS]);
		$query->andFilterWhere(['like', 'TiposPOS.descripcionTipoPOS', $this->tipoPosID]);
$query->andWhere(['=', 'MenusPOS.regEstado', '1']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }

	public function searchelimina($params)
    {
        $query = Menuspos::find();
		$query->joinWith(['idTipospos']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->sort->attributes['idTipospos'] = [
						'asc' => ['TiposPOS.descripcionTipoPOS' => SORT_ASC],
						'desc' => ['TiposPOS.descripcionTipoPOS' => SORT_DESC],
				];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'menuPOSID' => $this->menuPOSID,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);

        $query->andFilterWhere(['like', 'nombreMenuPOS', $this->nombreMenuPOS]);
		$query->andFilterWhere(['like', 'TiposPOS.descripcionTipoPOS', $this->tipoPosID]);
$query->andWhere(['=', 'MenusPOS.regEstado', '0']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }
}
