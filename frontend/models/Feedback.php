<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
namespace frontend\models;

/**
 * Description of Gallery
 *
 * @author Peter
 */
use Yii;
class Feedback extends \yii\db\ActiveRecord{
    //put your code here
    public function behaviors(){
        return[
            'frontend\components\FormSubmitBehavior',
            'frontend\components\FormSubmitHookBehaviour',
        ];
    }
    public static function tableName()
    {
        return 'tbl_feedback';
    }
    
    public function getDetails(){        
        return $this->hasMany(FeedbackDetails::className(),['feedback_id'=>'id']);
    }
    public function getOwner(){
        return $this->hasOne(Users::className(),['username'=>'usrname']);
    }
    public function getArticle(){
        return $this->hasOne(Articles::className(),['id'=>'target_id','feedback_type'=>'article']);
    }
    public function getSubmitForm(){
        return $this->hasOne(FormSubmit::className(),['id'=>'target_id']);
    }
    public function getFile(){
        return $this->hasMany(FeedbackFiles::className(),['feedback_id'=>'id']);
    }
    
}
