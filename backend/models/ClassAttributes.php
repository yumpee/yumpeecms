<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
namespace backend\models;

use Yii;
class ClassAttributes extends \yii\db\ActiveRecord
{
   
   public static function tableName()
    {
        return 'tbl_class_attributes';
    }
    public function getParent(){
        return $this->hasOne(ClassAttributes::className(),['id'=>'parent_id']);
    }
    public function getChild(){
        return $this->hasMany(ClassAttributes::className(),['parent_id'=>'id']);
    }
    
}
