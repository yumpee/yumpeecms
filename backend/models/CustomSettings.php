<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace backend\models;

/**
 * Description of CustomSettings
 *
 * @author Peter
 */
use Yii;
class CustomSettings extends yii\db\ActiveRecord{
    //put your code here
    
    public static function tableName()
    {
        return 'tbl_custom_settings';
    }
    public function afterFind(){
        
    }
}
