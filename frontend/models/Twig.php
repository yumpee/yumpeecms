<?php

/* 
 * Author : Peter Odon
 * Email : peter@audmaster.com
 * Project Site : http://www.yumpeecms.com


 * YumpeeCMS is a Content Management and Application Development Framework.
 *  Copyright (C) 2018  Audmaster Technologies, Australia
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <https://www.gnu.org/licenses/>.

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
    public function getIsCustom(){
        return $this->hasOne(CustomWidget::className(),['name'=>'renderer']);
    }
    public function afterFind(){
        if(substr($this->filename, 0, strlen("twig/")) === "twig/"):
            $this->code = file_get_contents(__DIR__ .'/../themes/'.ContentBuilder::getThemeFolder().'/'.$this->filename);           
        endif; 
        
        if($this->isCustom!=null):
            if($this->isCustom->require_login=="Y"):
                if(Yii::$app->user->isGuest):
                    $this->code="";
                elseif (strpos($this->isCustom->permissions,Yii::$app->user->identity->role_id) === false) :
                    $this->code="";                
                endif;
            endif;
        endif;
        
        parent::afterFind();
        if(ContentBuilder::getSetting("minify_twig")=="on"):
            $minify = new Minify();
            $this->code = $minify->minify_html($this->code);
        endif;
        
        
    }
    
}
