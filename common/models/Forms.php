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
