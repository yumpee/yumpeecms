<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace backend\models;

/**
 * Description of TranslationMessage
 *
 * @author Peter
 */
class TranslationMessage extends \yii\db\ActiveRecord{
    public static function tableName(){
        return 'message';
    }
}
