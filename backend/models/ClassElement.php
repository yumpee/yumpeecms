<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
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
    
}