<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
namespace frontend\models;

use backend\models\Settings;
use common\components\GUIBehavior;


class CSS extends \backend\models\CSS{
    private $fields = array('css');
    public function behaviors() {
        return [
                   
            ['class'=>GUIBehavior::className(),                
                'fields'=>$this->fields,
            ],          
            
        ];
    }  
    public static function tableName()
    {
        return 'tbl_css';
    }
}