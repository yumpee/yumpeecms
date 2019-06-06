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

namespace frontend\models;
use \frontend\models\CustomSettings;
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
    public function getLogoDetails(){
        $id = $this->find()->where(['setting_name'=>'website_logo'])->one()->setting_value;
        return Media::find()->where(['id'=>$id])->one();
    }
    public function getHasLogo(){
        if ($this->find()->where(['setting_name'=>'website_logo'])->one()->setting_value!=""):
            return true;
        else:
            return false;
        endif;
    }
    
}