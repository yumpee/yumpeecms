<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
namespace frontend\models;
use Yii;
use frontend\models\ProfileDetails;

class Users extends \yii\db\ActiveRecord {
    
    public static function tableName()
    {
        return 'tbl_user';
    }
    public function getDisplayImage(){
        return $this->hasOne(\backend\models\Media::className(),['id'=>'display_image_id']);
    }
    public function getDetails(){
        return $this->hasOne(ProfileDetails::className(),['profile_id'=>'id']);
    }
    public function getProfileFiles(){
        return $this->hasMany(UserProfileFiles::className(),['profile_id'=>'id']);
    }
public static function saveUser(){
        $records = Users::find()->where(['id'=>Yii::$app->user->identity->id])->one();
        $password = Yii::$app->request->post('passwd');       
        
        if($records!=null){                 
            $id = Yii::$app->request->post("id");               
            $records->setAttribute('first_name',Yii::$app->request->post("first_name"));
            $records->setAttribute('last_name',Yii::$app->request->post("last_name"));
            $records->setAttribute('extension',Yii::$app->request->post("extension"));
            $records->setAttribute('updated_at',time());
            $records->setAttribute('email',Yii::$app->request->post("email"));
            $records->setAttribute('about',Yii::$app->request->post("about"));
            $records->setAttribute('title',Yii::$app->request->post("title"));            
            
           //if the password has changed
           if($records['password_hash']<>Yii::$app->request->post('passwd')):
               $records->setAttribute('password_hash',Yii::$app->security->generatePasswordHash($password));
           endif;
           $records->save();
            
            return "Updates successfully made";
        }
    }
    
}