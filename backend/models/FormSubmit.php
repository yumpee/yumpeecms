<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
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
