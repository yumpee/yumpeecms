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
namespace backend\components;   
/**
 * Description of DBComponent
 *
 * @author Peter
 */
use backend\models\CustomFormSettings;
use backend\models\ClassSetup;
use backend\models\ClassElement;
use backend\models\ClassAttributes;


class DBComponent {
    //put your code here
    public static function parseData($data_array,$form_id){
        
        $setting = CustomFormSettings::find()->where(['form_id'=>$form_id])->andWhere('field_name="'.$data_array['param'].'"')->one();        
        if ($setting==null):
            return $data_array['param_val'];
        else:
            if($setting['return_alias']=="Y"):
                $setup = ClassSetup::find()->where(['name'=>$data_array['param_val']])->one();
                if($setup!=null):
                    return $setup->alias;
                endif;
                $setup = ClassElement::find()->where(['name'=>$data_array['param_val']])->one();
                if($setup!=null):
                    return $setup->alias;
                endif;
                $setup = ClassAttributes::find()->where(['name'=>$data_array['param_val']])->one();
                if($setup!=null):
                    return $setup->alias;
                endif;     
            else:
                return $data_array['param_val'];
            endif;
        endif;
    }
    
    public static function parseField($data_array,$form_id){
        $setting = CustomFormSettings::find()->where(['form_id'=>$form_id])->andWhere('field_name="'.$data_array['param'].'"')->one();
        if ($setting==null):
            return $data_array['param'];
        else:
                return $setting['view_label'];
        endif;
    }
    
    public static function parseRecord($content,$submit_record){
        //this function is used to evaluate a user defined field during record fetch
        $pattern_setting= "/{yumpee_setting}(.*?){\/yumpee_setting}/";
        $pattern_record= "/{yumpee_record}(.*?){\/yumpee_record}/";
        $content = preg_replace_callback($pattern_setting,function ($matches) {
                            $replacer = \frontend\components\ContentBuilder::getSetting($matches[1]);                            
                            return $replacer;
                    },$content);
        $content = preg_replace_callback($pattern_record,function ($matches) use($submit_record) {
                            if($submit_record[$matches[1]]!=null):
                                return $submit_record[$matches[1]];
                            endif;
                            foreach($submit_record['backendData'] as $data):
                                if($data['param']==$matches[1]):
                                    return $data['param_val'];
                                endif;
                            endforeach;                            
                            
                    },$content);
                    
       return $content;
    }
    public static function parseWidget($widget,$submit_record){
        $codebase = \frontend\models\Twig::find()->where(['name'=>$widget])->one();
        $loader = new Twig();
                            $twig = new \Twig_Environment($loader);
                            $content= $twig->render($codebase['filename'], ['form'=>$submit_record,'app'=>Yii::$app]);
                            return $this->render('@frontend/views/layouts/html',['data'=>$content]);
    }
}
