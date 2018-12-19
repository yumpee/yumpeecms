<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace backend\models;

/**
 * Description of WebHookEmail
 *
 * @author Peter
 */
class WebHookEmail extends \yii\db\ActiveRecord{
    public static function tableName()
    {
        return 'tbl_web_hook_email';
    }
}
