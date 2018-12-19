<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
namespace frontend\models;

use Yii;
use frontend\components\ContentBuilder;
use frontend\components\Minify;
use common\components\GUIBehavior;


class Twig extends \common\models\Twig
{
   
   private $fields = array('code');
    public function behaviors() {
        return [
                   
            ['class'=>GUIBehavior::className(),                
                'fields'=>$this->fields,
            ],          
            
        ];
    }
    public function afterFind(){
        if(substr($this->filename, 0, strlen("twig/")) === "twig/"):
            $this->code = file_get_contents(__DIR__ .'/../themes/'.ContentBuilder::getThemeFolder().'/'.$this->filename);            
        endif;        
        parent::afterFind();
        if(ContentBuilder::getSetting("minify_twig")=="on"):
            $minify = new Minify();
            $this->code = $minify->minify_html($this->code);
        endif;
        
        
    }
    
}
