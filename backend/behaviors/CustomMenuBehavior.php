<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace backend\behaviors;

use yii\db\ActiveRecord;
use yii\base\Behavior;
use backend\models\BackEndMenus;
use Yii;
/**
 * Description of CustomMenuBehavior
 *
 * @author Peter
 */
class CustomMenuBehavior extends Behavior{
    public function events()
    {
        return [
            // after find event
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',            
        ];
    }
    public function afterSave(){
        
    }
}