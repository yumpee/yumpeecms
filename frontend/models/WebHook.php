<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace frontend\models;

/**
 * Description of WebHook
 *
 * @author Peter
 */
class WebHook extends \yii\db\ActiveRecord{
    public static function tableName()
    {
        return 'tbl_web_hook';
    }
}