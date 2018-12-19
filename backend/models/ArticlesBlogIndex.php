<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace backend\models;

use backend\models\Pages;
/**
 * Description of ArticlesBlogIndex
 *
 * @author Peter
 */
class ArticlesBlogIndex extends \yii\db\ActiveRecord{
    //put your code here
    public static function tableName(){
        return 'tbl_articles_blog_index';
    }
    public function getPage(){
        return $this->hasOne(Pages::className(),['id'=>'blog_index_id']);
    }
}
