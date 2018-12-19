<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace backend\models;

/**
 * Description of PageTags
 *
 * @author Peter
 */
class PageTags extends \yii\db\ActiveRecord{
    //put your code here
    public static function tableName(){
        return 'tbl_page_tags';
    }
}
