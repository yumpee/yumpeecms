<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
namespace backend\models;

/**
 * Description of Forms
 *
 * @author Peter
 */
use Yii;
class CustomWidget extends \yii\db\ActiveRecord{
    //put your code here
    public static function tableName()
    {
        return 'tbl_custom_widget';
    }
    public function getForm(){
        return $this->hasOne(Forms::className(),['id'=>'form_id']);
    }
    public function getHasContents(){
        if(Yii::$app->request->get("reload")=="true"):
            return $this->hasOne(Twig::className(),['renderer'=>'name'])->andWhere('theme_id="'.Yii::$app->request->get("theme").'"');
        else:
            return $this->hasOne(Twig::className(),['renderer'=>'name'])->andWhere('theme_id="'.\frontend\components\ContentBuilder::getSetting("current_theme").'"');;
        endif;
    }
}

