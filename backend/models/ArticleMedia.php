<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace backend\models;
use Yii;
/**
 * Description of GalleryImage
 *
 * @author Peter
 */
class ArticleMedia extends \yii\db\ActiveRecord {
    //put your code here
    public static function tableName()
    {
        return 'tbl_article_media';
    }
    public function getUploadURL(){
        return \frontend\components\ContentBuilder::getSetting("website_image_url");
    }
    public function getDetails(){
        return $this->hasOne(Media::className(),['path'=>'media_id']);
        
    }
}