<?php

/* 
 * Author : Peter Odon
 * Email : peter@audmaster.com
 * Project Site : http://www.yumpeecms.com


 * YumpeeCMS is a Content Management and Application Development Framework.
 *  Copyright (C) 2018  Audmaster Technologies, Australia
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <https://www.gnu.org/licenses/>.

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
