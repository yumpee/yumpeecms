<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace backend\models;

/**
 * Description of ServicesIncoming
 *
 * @author Peter
 */
class ServicesIncoming extends \yii\db\ActiveRecord{
    //put your code here
    public static function tableName(){
        return 'tbl_services_incoming';
    }
}
