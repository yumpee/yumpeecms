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

use Yii;

class FormData extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tbl_form_data';
    } 
    public function getBaseURL(){
        return Yii::$app->request->getBaseUrl();
    }
    public function getElement(){
        return $this->hasOne(\backend\models\ClassElement::className(),['name'=>'param']);
    }
    public function getProperty(){
        return $this->hasOne(\backend\models\ClassAttributes::className(),['name'=>'param']);
    }
    public function getElementVal(){
        return $this->hasOne(\backend\models\ClassElement::className(),['name'=>'param_val']);
    }
    public function getPropertyVal(){
        return $this->hasOne(\backend\models\ClassAttributes::className(),['name'=>'param_val']);
    }
    public function getSetup(){
        return $this->hasOne(\backend\models\ClassSetup::className(),['name'=>'param']);
    }
    public function getSetupVal(){
        return $this->hasOne(\backend\models\ClassSetup::className(),['name'=>'param_val']);
    }
    
}
