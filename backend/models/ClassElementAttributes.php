<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
namespace backend\models;

use Yii;
class ClassElementAttributes extends \yii\db\ActiveRecord
{
   
   public static function tableName()
    {
        return 'tbl_class_elements_attributes';
    }
   
}
