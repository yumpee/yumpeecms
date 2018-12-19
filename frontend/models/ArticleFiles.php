<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace frontend\models;

/**
 * Description of ArticleFiles
 *
 * @author Peter
 */
class ArticleFiles extends \yii\db\ActiveRecord{
    //put your code here
    public static function tableName(){
        return 'tbl_article_files';
    }
}
