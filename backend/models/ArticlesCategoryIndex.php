<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace backend\models;

/**
 * Description of ArticlesCategoryIndex
 *
 * @author Peter
 */
class ArticlesCategoryIndex extends \yii\db\ActiveRecord{
    //put your code here
    public static function tableName(){
        return 'tbl_articles_category_index';
    }
}
