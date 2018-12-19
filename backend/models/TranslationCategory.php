<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace backend\models;

/**
 * Description of TranslationCategory
 *
 * @author Peter
 */
class TranslationCategory extends \yii\db\ActiveRecord{
    public static function tableName(){
        return 'tbl_translation_category';
    }
}
