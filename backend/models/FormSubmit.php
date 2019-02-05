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
 * Description of FormSubmit
 *
 * @author Peter
 */
use backend\models\CustomFormSettings;
use frontend\models\FormData;

class FormSubmit extends \common\models\FormSubmit{
    //put your code here
    
    public function getBackendData(){
        $data_arr = CustomFormSettings::find()->select('field_name')->where(['form_id'=>$this->form_id])->orderBy('field_name')->column();
        //return $this->hasMany(FormData::className(),['form_submit_id'=>'id'])->where(['IN','param',$data_arr]);
        return FormData::find()->where(['IN','param',$data_arr])->andWhere('form_submit_id="'.$this->id.'"')->orderBy('param')->all();
    }
    
}
