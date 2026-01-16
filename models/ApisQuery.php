<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Apis]].
 *
 * @see Apis
 */
class ApisQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Apis[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Apis|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
