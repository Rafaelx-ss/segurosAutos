<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Conexionesapisestablecimientos;

/**
 * ConexionesapisestablecimientosSearch represents the model behind the search form of `app\models\Conexionesapisestablecimientos`.
 */
class ConexionesapisestablecimientosSearch extends Conexionesapisestablecimientos
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['conexionApiEstablecimientoID', 'establecimientoID', 'conexionApiID', 'versionRegistro', 'regFechaUltimaModificacion', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'safe'],
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


    public function search($params)
    {
        $query = Conexionesapisestablecimientos::find();
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
            'conexionApiEstablecimientoID' => $this->conexionApiEstablecimientoID,
            'versionActual' => $this->versionActual,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);
		
			if($this->establecimientoID != ""){
				$conexionApiIDFind = Yii::$app->db->createCommand("select establecimientoID,concat(aliasEstablecimiento,' - ',razonSocialEstablecimiento,' (',establecimientoID,')') as establecimiento from Establecimientos where concat(aliasEstablecimiento,' - ',razonSocialEstablecimiento,' (',establecimientoID,')') like '%".$this->establecimientoID."%' order by aliasEstablecimiento ")->queryAll();

				$query->andWhere(['in', 'establecimientoID', $conexionApiIDFind]);
			}
			if($this->conexionApiID != ""){
				$conexionApiIDFind = Yii::$app->db->createCommand("Select ca.conexionApiID from ConexionesApis ca join AplicacionesConexionApi acp on ca.aplicacionConexionApiID=acp.aplicacionConexionApiID join Establecimientos est on ca.usuario=est.establecimientoID where concat(acp.descripcion,' ',ca.usuario,' ',est.aliasEstablecimiento) like '%".$this->conexionApiID."%' group by ca.conexionApiID")->queryAll();

				$query->andWhere(['in', 'conexionApiID', $conexionApiIDFind]);
			}$query->andWhere(['=', 'ConexionesApisEstablecimientos.regEstado', '1']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }

	public function searchelimina($params)
    {
        $query = Conexionesapisestablecimientos::find();
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
            'conexionApiEstablecimientoID' => $this->conexionApiEstablecimientoID,
            'versionActual' => $this->versionActual,
            'versionRegistro' => $this->versionRegistro,
            'regEstado' => $this->regEstado,
            'regFechaUltimaModificacion' => $this->regFechaUltimaModificacion,
            'regUsuarioUltimaModificacion' => $this->regUsuarioUltimaModificacion,
            'regFormularioUltimaModificacion' => $this->regFormularioUltimaModificacion,
            'regVersionUltimaModificacion' => $this->regVersionUltimaModificacion,
        ]);
		
			if($this->establecimientoID != ""){
				$conexionApiIDFind = Yii::$app->db->createCommand("select establecimientoID,concat(aliasEstablecimiento,' - ',razonSocialEstablecimiento,' (',establecimientoID,')') as establecimiento from Establecimientos where concat(aliasEstablecimiento,' - ',razonSocialEstablecimiento,' (',establecimientoID,')') like '%".$this->establecimientoID."%' order by aliasEstablecimiento ")->queryAll();

				$query->andWhere(['in', 'establecimientoID', $conexionApiIDFind]);
			}
			if($this->conexionApiID != ""){
				$conexionApiIDFind = Yii::$app->db->createCommand("Select ca.conexionApiID from ConexionesApis ca join AplicacionesConexionApi acp on ca.aplicacionConexionApiID=acp.aplicacionConexionApiID join Establecimientos est on ca.usuario=est.establecimientoID where concat(acp.descripcion,' ',ca.usuario,' ',est.aliasEstablecimiento) like '%".$this->conexionApiID."%' group by ca.conexionApiID")->queryAll();

				$query->andWhere(['in', 'conexionApiID', $conexionApiIDFind]);
			}$query->andWhere(['=', 'ConexionesApisEstablecimientos.regEstado', '0']);


		if (is_null($params) || empty($params)){
			$query->where("0 = 1");
			return $dataProvider;
		}else{
			return $dataProvider;
		}		
		
    }
}
