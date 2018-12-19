<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace common\models;

/**
 * Description of UserProfileFiles
 *
 * @author Peter
 */
class UserProfileFiles extends \yii\db\ActiveRecord{
    public static function tableName()
    {
        return 'tbl_user_profile_files';
    }
}
