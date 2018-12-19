<?php
namespace api\models;
/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

/**
 * Description of Resource
 *
 * @author Peter
 */
class Resource extends \yii\db\ActiveRecord{
    //put your code here
    public static function tableName(){
        return 'tbl_forms';
    }
}
