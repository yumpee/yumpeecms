<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\models;
use backend\models\CustomSettings;
class Settings extends \yii\db\ActiveRecord{
    
    public static function tableName()
    {
        return 'tbl_settings';
    }
    public function getSetting($setting){
		$setting_obj = $this->find()->where(['setting_name'=>$setting])->one();
		if($setting_obj!=null){
				return $setting_obj->setting_value;
		}
		$setting_obj = CustomSettings::find()->where(['setting_name'=>$setting])->one();
		if($setting_obj!=null){
			return $setting_obj->setting_value;
		}
		return "";
    }
    public function getWebsiteName(){
        return $this->find()->where(['setting_name'=>'website_name'])->one()->setting_value;
    }
    public function getWebsiteTagline(){
        return $this->find()->where(['setting_name'=>'website_short_name'])->one()->setting_value;
    }
    public function getLogo(){
        return $this->find()->where(['setting_name'=>'website_logo'])->one()->setting_value;
    }
    public function getHasLogo(){
        if ($this->find()->where(['setting_name'=>'website_logo'])->one()->setting_value!=""):
            return true;
        else:
            return false;
        endif;
    }
    
}