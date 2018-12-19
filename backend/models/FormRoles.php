<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace backend\models;
use Yii;
/**
 * Description of FormRoles
 *
 * @author Peter
 */
class FormRoles extends yii\db\ActiveRecord{
    public static function tableName()
    {
        return 'tbl_form_roles';
    }
    
    
}
