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
use backend\models\ClassElementAttributes;

class ClassElement extends \yii\db\ActiveRecord
{
   
   public static function tableName()
    {
        return 'tbl_class_elements';
    }
    public function getParent(){
        return $this->hasOne(ClassElement::className(),['id'=>'parent_id']);
    }
    public function getChild(){
        return $this->hasMany(ClassElement::className(),['parent_id'=>'id']);
    }
    public function getElementProperties($property_id){
       return ClassElementAttributes::find()->where(['element_id'=>$this->id])->andWhere('attribute_id="'.$property_id.'"')->one();
    }
    
    public function getDisplayImage(){
        //this gets the Display object array from the Media class
        return $this->hasOne(Media::className(),['id'=>'display_image_id']);
    }
    public function getSetup(){
        return $this->hasOne(ClassSetup::className(),['id'=>'class_id']);
    }
    
}