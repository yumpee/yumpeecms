<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace backend\models;

/**
 * Description of BackEndMenuRole
 *
 * @author Peter
 */
use Yii;
class BackEndMenuRole extends yii\db\ActiveRecord{
    //put your code here
    public static function tableName(){
        return 'tbl_backend_menu_role';
    }
}
