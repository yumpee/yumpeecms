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
use Yii;

/**
 * Description of Themes
 *
 * @author Peter
 */
use backend\models\Settings;
use backend\models\SliderImage;

class Slider extends \yii\db\ActiveRecord{
    //put your code here
    public static function tableName()
    {
        return 'tbl_sliders';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'transition_type','duration'], 'required'],
            [['duration','default_height','default_width'], 'integer'],
            [['id'],'safe'],
            [['name','transition_type','title'], 'string', 'max' => 200],           
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name of theme',
            'folder'=>'Name of theme folder',
            'is_default' => 'Is Default',
            'description'=>'Theme description'
        ];
    }
    public function getImagesObject(){        
        return SliderImage::find()->where(['slider_id'=>$this->id])->all();
    }
    public function getImages(){
        $return="";
        $records = SliderImage::find()->where(['slider_id'=>Yii::$app->request->get('id')])->all();
        foreach($records as $rec):
            $return.=" ".$rec['media_id'];
        endforeach;
        return $return;
    }
    public function getUploadURL(){
        return \frontend\components\ContentBuilder::getSetting("website_image_url");
    }
    public static function updateSliderImage($id){        
        SliderImage::deleteAll(['slider_id'=>$id]);
        $img_array = Yii::$app->request->post("image_listing");            
        $ev_arr = explode(" ",$img_array);           
        $counter=0;
                       
        for($i=0; $i < count($ev_arr);$i++): 
               if(trim($ev_arr[$i])!=""):
                    $c = new SliderImage();
                    $c->setAttribute('media_id', $ev_arr[$i]);
                    $c->setAttribute('slider_id',$id);
                    $c->save();
               endif;
        endfor;
    }
    public static function deleteSliderImage(){        
        SliderImage::deleteAll(['slider_id'=>Yii::$app->request->get('slide'),'media_id'=>Yii::$app->request->get('id')]);
    }
    
}
