<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
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
