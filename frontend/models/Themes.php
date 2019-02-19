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

/**
 * Description of Themes
 *
 * @author Peter
 */
use common\components\GUIBehavior;
use backend\models\Settings;
use frontend\components\ContentBuilder;
use frontend\models\Domains;
use Yii;


class Themes extends \backend\models\Themes{
    private $fields = array('header','footer','custom_styles');
    public function behaviors() {
        
        return [
                   
            ['class'=>GUIBehavior::className(),                
                'fields'=>$this->fields,
            ],          
            
        ];
    }
    public function getCurrentTheme(){
        //this returns the current theme from the Settings Model class
        
            $session = Yii::$app->session;
            $session->open(); // open a session
            $theme_id = $session->get('yumpee_preview_theme');
            $session->close();  // close a session
         if($theme_id!=null):             
             return $theme_id;             
         endif;
         //handle multiple domains here
         if(ContentBuilder::getSetting("allow_multiple_domains")=="Yes"):
            $install_domain = ContentBuilder::getSetting("home_url");
            $curr_domain = Yii::$app->request->hostInfo;
            $theme_id=null;
            if(strpos($install_domain, $curr_domain)===false):
                $sub_domain = \frontend\models\Domains::find()->where(['domain_url'=>$curr_domain])->one();
                if($sub_domain!=null):
                    $theme_id = $sub_domain->theme_id;
                endif;
            endif; 
            if($theme_id!=null):
                return $theme_id;
            endif;
         endif;
         
         
         
         
        //this returns the current theme from the Settings Model class
         $theme = Settings::findOne(['setting_name'=>'current_theme']);
         if($theme['setting_value']!=null):
             return $theme['setting_value'];
         else:
             return '0';
         endif;
         
    }
	public function getDataTheme(){
        //this returns the current theme from the Settings Model class         
         if(ContentBuilder::getSetting("allow_multiple_domains")=="Yes"):
            $install_domain = ContentBuilder::getSetting("home_url");
            $curr_domain = Yii::$app->request->hostInfo;
            $theme_id=null;
            if(strpos($install_domain, $curr_domain)===false):
                $sub_domain = \frontend\models\Domains::find()->where(['domain_url'=>$curr_domain])->one();
                if($sub_domain!=null):
                    $theme_id = $sub_domain->theme_id;
                endif;
            endif; 
            if($theme_id!=null):
                return $theme_id;
            endif;
         endif;
         
         
         
         
        //this returns the current theme from the Settings Model class
         $theme = Settings::findOne(['setting_name'=>'current_theme']);
         if($theme['setting_value']!=null):
             return $theme['setting_value'];
         else:
             return '0';
         endif;
         
    }
}
