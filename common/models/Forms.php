<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace common\models;

/**
 * Description of Forms
 *
 * @author Peter
 */
use Yii;
class Forms extends \yii\db\ActiveRecord{
    //put your code here
    public static function tableName()
    {
        return 'tbl_forms';
    }
    public function getDataRecords(){   
        if ($this->form_fill_entry_type=="M"):
            return $this->hasMany(\frontend\models\FormSubmit::className(),['form_id'=>'id'])->andWhere('usrname="'.Yii::$app->user->identity->username.'"');
        else:
            return $this->hasOne(\frontend\models\FormSubmit::className(),['form_id'=>'id'])->andWhere('usrname="'.Yii::$app->user->identity->username.'"');
        endif;
    }
    public function getViewRenderer(){
        return $this->hasOne(Pages::className(),['form_id'=>'id']);
    }
    public function getHasContents(){
        if(Yii::$app->request->get("reload")=="true"):
            return $this->hasOne(Twig::className(),['renderer'=>'id'])->andWhere('theme_id="'.Yii::$app->request->get("theme").'"');
        else:
            return $this->hasOne(Twig::className(),['renderer'=>'id'])->andWhere('theme_id="'.\frontend\components\ContentBuilder::getSetting("current_theme").'"');;
        endif;
        
    }
    
}
