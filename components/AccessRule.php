<?php
 
namespace app\components;
 
use app\models\Admin;
class AccessRule extends \yii\filters\AccessRule {
 
    /**
     * @inheritdoc
     */
    protected function matchRole($user)
    {
        
    }
}