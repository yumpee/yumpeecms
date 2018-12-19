<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace backend\models;

/**
 * Description of ArticleDetails
 *
 * @author Peter
 */
class ArticleDetails extends \yii\db\ActiveRecord {
    //put your code here
    public static function tableName()
    {
        return 'tbl_article_details';
    }
    //put your code here
}
