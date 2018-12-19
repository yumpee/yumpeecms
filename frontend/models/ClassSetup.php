<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace frontend\models;

/**
 * Description of ClassSetup
 *
 * @author Peter
 */
class ClassSetup extends \yii\db\ActiveRecord{
    //put your code here
        
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
}
