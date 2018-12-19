<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace backend\models;

/**
 * Description of Gallery
 *
 * @author Peter
 */
use Yii;
class Gallery extends \yii\db\ActiveRecord{
    //put your code here
    public static function tableName()
    {
        return 'tbl_gallery';
    }
   public function rules()
    {
        return [
            [['id'],'safe'],
            [['name'], 'string', 'max' => 100],
            
        ];
    } 
    public function getImages(){
        $return="";
        //$records = Yii::$app->db->createCommand("SELECT media_id FROM tbl_slider_image WHERE slider_id='".Yii::$app->request->get('id')."'")->queryAll();
        $records = GalleryImage::find()->where(['gallery_id'=>$this->id])->all();
        foreach($records as $rec):
            $return.=" ".$rec['image_id'];
        endforeach;
        return $return;
    }
    public function getUploadURL(){
        return \frontend\components\ContentBuilder::getSetting("website_image_url");
    }
    
}
