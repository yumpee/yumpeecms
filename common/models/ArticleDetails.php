<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace common\models;

/**
 * Description of ArticleDetails
 *
 * @author Peter
 */
class ArticleDetails extends \yii\db\ActiveRecord{
public static function tableName(){
    return 'tbl_article_details';
}
}
