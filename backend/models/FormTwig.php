<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
namespace backend\models;

use Yii;

use frontend\components\ContentBuilder;
use frontend\components\Minify;
use common\components\GUIBehavior;


class FormTwig extends \common\models\Twig
{
   private $fields = array('code');
    public function behaviors() {
        return [
                   
            ['class'=>GUIBehavior::className(),                
                'fields'=>$this->fields,
            ],          
            
        ];
    }
    
}
