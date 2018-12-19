<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace backend\models;

/**
 * Description of SliderImage
 *
 * @author Peter
 */
class SliderImage extends \yii\db\ActiveRecord{
    //put your code here
    public static function tableName(){
        return 'tbl_slider_image';
    }
}
