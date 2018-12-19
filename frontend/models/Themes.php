<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace frontend\models;

/**
 * Description of Themes
 *
 * @author Peter
 */
use common\components\GUIBehavior;


class Themes extends \backend\models\Themes{
    private $fields = array('header','footer','custom_styles');
    public function behaviors() {
        
        return [
                   
            ['class'=>GUIBehavior::className(),                
                'fields'=>$this->fields,
            ],          
            
        ];
    }
}
