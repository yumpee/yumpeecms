<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace frontend\models;

use Yii;
use frontend\components\ContentBuilder;
use backend\models\Settings;
//use backend\models\Pages;
//use backend\models\Templates;
use backend\models\Users;
use backend\models\Forms;
use backend\models\Relationships;

class FormSubmit extends \yii\db\ActiveRecord
{
    public $parent_url;
    
    public function behaviors(){
        return[
            'frontend\components\FormSubmitBehavior',
            'frontend\components\FormSubmitHookBehaviour',
        ];
    }
    public static function tableName()
    {
        return 'tbl_form_submit';
    } 
    public function getData(){
        return $this->hasMany(FormData::className(),['form_submit_id'=>'id']);
    }
    
    public function getBaseURL(){
        return Yii::$app->request->getBaseUrl();
    }
    public function getFormattedIndexURL(){
      return Yii::$app->request->getBaseUrl()."/".ContentBuilder::getActionURL(Yii::$app->request->getAbsoluteUrl());
    }
    public function getParentURL(){
        $template = Templates::find()->where(['route'=>'forms/view'])->one();
        $page = Pages::find()->where(['form_id'=>$this->form_id])->andWhere('template="'.$template['id'].'"')->one();
        return Yii::$app->request->getBaseUrl()."/".$page['url'];
    }
    public function getFile(){
        return $this->hasMany(FormFiles::className(),['form_submit_id'=>'id']);
    }
    public function getUser(){
        return $this->hasOne(\backend\models\Users::className(),['username'=>'usrname']);
    }
    public function getRoleURL(){
        $user = Users::find()->where(['username'=>$this->usrname])->one();
        //get the role of this user
       return Pages::find()->where(['role_id'=>$user->role->id])->one();
        //this relation is used to get the parent url
    }
    public function getPublishDate(){
      //we get the date format type from settings and then use it to return the Publish Date
      $date_obj = Settings::findOne(['setting_name'=>'date_format']);
      return Yii::$app->formatter->asDate($this->date_stamp, 'php:'.$date_obj->setting_value);      
    }
    public function getForm(){
        return $this->hasOne(\backend\models\Forms::className(),['id'=>'form_id']);
    }
    public function afterFind(){
        $template = Templates::find()->where(['route'=>'forms/view'])->one();
        $page = Pages::find()->where(['form_id'=>$this->form_id])->andWhere('template="'.$template['id'].'"')->one();
        $this->parent_url = Yii::$app->request->getBaseUrl()."/".$page['url'];
        parent::afterFind();
    }
    
 public function getFeedback(){
    return $this->hasMany(Feedback::className(),['target_id'=>'id'])->where(['feedback_type'=>'forms']);
 }   
 public function getRatingdetails(){        
        return $this->hasMany(\backend\models\RatingDetails::className(),['target_id'=>'url'])->where(['target_type'=>'F']);
 }
 function getRelationships(){
        $return_val=[];
        $forms = Relationships::find()->select('source_id')->where(['source_id'=>$this->form->name])->all();
        if($forms!=null):
            return Forms::find()->where(['IN','name',$forms])->all();
        endif;
        return $return_val;
 }
 
    
}
