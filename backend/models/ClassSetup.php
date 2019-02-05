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
namespace backend\models;

use Yii;
use backend\behaviors\CustomMenuBehavior;

class ClassSetup extends \yii\db\ActiveRecord
{
   //we attach a behaviour on safe
    
   public static function tableName()
    {
        return 'tbl_class_setup';
    }
    public function getParent(){
        return $this->hasOne(ClassSetup::className(),['id'=>'parent_id']);
    }
    public function getDisplayImage(){
        //this gets the Display object array from the Media class
        return $this->hasOne(Media::className(),['id'=>'display_image_id']);
        
    }
    
    public function getChild(){
        return $this->hasMany(ClassSetup::className(),['parent_id'=>'id']);
    }
    public function afterSave($insert, $changedAttributes){
        parent::afterSave($insert, $changedAttributes);
        if($this->show_in_menu=="Y"){
            $url="?r=setup/details&actions=edit&class=".$this->id;
            $model= BackEndMenus::find()->where(['url'=>$url])->one();
            if($model==null):
                $parent=BackEndMenus::find()->where(['original_label'=>'Setup'])->one();
                $model = new BackEndMenus();
                $id=md5(date("Hmis").rand(1000,10000));        
                $model->setAttribute("id",$id);
                $model->setAttribute("label",$this->alias);
                $model->setAttribute("url",$url);
                $model->setAttribute("parent_id",$parent->id);
                $model->setAttribute("priority","1");
                $model->setAttribute("custom_stat","N");
                $model->save();
            endif;
        }else{
            //we have to unlink it from the menus
            $url="?r=setup/details&actions=edit&class=".$this->id;
            BackEndMenus::deleteAll(['url'=>$url]);
        }
    }
    
    
    
}
