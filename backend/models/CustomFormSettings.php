<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace backend\models;

use Yii;
/**
 * Description of CustomFormSettings
 *
 * @author Peter
 */
class CustomFormSettings extends yii\db\ActiveRecord{
    //put your code here
    public static function tableName(){
        return 'tbl_form_custom_setting';
    }
}
