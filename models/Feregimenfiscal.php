<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "FERegimenFiscal".
 *
 * @property int $regimenFiscalID
 * @property string $nombreRegimenFiscal
 * @property string $descripcionRegimenFiscal
 * @property string $fisica
 * @property string $moral
 * @property string $fechaInicioVigencia
 * @property string $fechaFinVigencia
 * @property bool $estadoRegimenfiscal
 */
class Feregimenfiscal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'FERegimenFiscal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descripcionRegimenFiscal'], 'string'],
            [['fechaInicioVigencia', 'fechaFinVigencia'], 'safe'],
            [['estadoRegimenfiscal'], 'boolean'],
            [['nombreRegimenFiscal'], 'string', 'max' => 50],
            [['fisica', 'moral'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'regimenFiscalID' => 'Regimen Fiscal ID',
            'nombreRegimenFiscal' => 'Nombre Regimen Fiscal',
            'descripcionRegimenFiscal' => 'Descripcion Regimen Fiscal',
            'fisica' => 'Fisica',
            'moral' => 'Moral',
            'fechaInicioVigencia' => 'Fecha Inicio Vigencia',
            'fechaFinVigencia' => 'Fecha Fin Vigencia',
            'estadoRegimenfiscal' => 'Estado Regimenfiscal',
        ];
    }


 /**
     * funciones relaciones
     * relaciones con tablas
     */


}
