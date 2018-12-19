<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace frontend\models;

/**
 * Description of ProfileDetails
 *
 * @author Peter
 */
use Yii;
class ProfileDetails extends yii\db\ActiveRecord{
    public static function tableName()
    {
        return 'tbl_profile_details';
    }
}
