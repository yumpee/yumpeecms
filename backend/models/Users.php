<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\models;
use Yii;
use frontend\models\FormSubmit;
use backend\models\Forms;


class Users extends \yii\db\ActiveRecord
{
   public static function tableName()
    {
        return 'tbl_user';
    }
    public function getRole(){
        return $this->hasOne(Roles::className(),['id'=>'role_id']);
    }
    public function getDisplayImage(){
        return $this->hasOne(Media::className(),['id'=>'display_image_id']);
    }
    public function getDetails(){
        return $this->hasMany(ProfileDetails::className(),['profile_id'=>'id']);
    }
    public function getArticles(){
        return $this->hasMany(Articles::className(),['usrname'=>'username']);
    }
    public function getListings(){
        $forms = FormSubmit::find()->select('form_id')->where(['usrname'=>$this->username])->distinct();
        return Forms::find()->where(['IN','id',$forms])->all();
    }
    public function getFormSubmit(){
        return $this->hasMany(FormSubmit::className(),['usrname'=>'username']);
    }
    public function getArticlesHome(){
        $record = Templates::find()->where(['route'=>'tags/authors'])->one();
        return Yii::$app->request->getBaseUrl()."/".$record->url;
    }
    public function getProfileFiles(){
        return $this->hasMany(UserProfileFiles::className(),['profile_id'=>'id']);
    }
    public static function getDb() {
        return \yii::$app->db;
    }
    public static function getUsers(){
        return Users::find()->all();
        
    }
    public static function getUserName($id){
        return Users::find()->where(['id'=>$id])->one();
    }
        
     
    public static function saveUsers(){        
        $records = Users::find()->where(['id'=>Yii::$app->request->post('id')])->one();
        $password = Yii::$app->request->post('passwd');
        
        
        if($records!=null){     
            
            $id = Yii::$app->request->post("id");    
            $records->setAttribute('username',Yii::$app->request->post("usrname"));
            $records->setAttribute('first_name',Yii::$app->request->post("first_name"));
            $records->setAttribute('last_name',Yii::$app->request->post("last_name"));
            $records->setAttribute('extension',Yii::$app->request->post("extension"));
            $records->setAttribute('updated_at',time());
            $records->setAttribute('email',Yii::$app->request->post("email"));
            $records->setAttribute('about',Yii::$app->request->post("about"));
            $records->setAttribute('title',Yii::$app->request->post("title"));
            $records->setAttribute('role_id',Yii::$app->request->post("role_id"));
            $records->setAttribute('display_image_id',Yii::$app->request->post("display_image_id"));
           //if the password has changed
           if($records['password_hash']<>Yii::$app->request->post('passwd')):
               $records->setAttribute('password_hash',Yii::$app->security->generatePasswordHash($password));
           endif;
            $records->save();
            return "Updates successfully made";
        }else{  
            $records = new Users();
            $records->setAttribute('username',Yii::$app->request->post("usrname"));
            $records->setAttribute('first_name',Yii::$app->request->post("first_name"));
            $records->setAttribute('last_name',Yii::$app->request->post("last_name"));
            $records->setAttribute('extension',Yii::$app->request->post("extension"));
            $records->setAttribute('updated_at',time());
            $records->setAttribute('created_at',time());
            $records->setAttribute('email',Yii::$app->request->post("email"));
            $records->setAttribute('about',Yii::$app->request->post("about"));
            $records->setAttribute('title',Yii::$app->request->post("title"));
            $records->setAttribute('role_id',Yii::$app->request->post("role_id"));
            $records->setAttribute('display_image_id',Yii::$app->request->post("display_image_id"));
            $records->setAttribute('password_hash',Yii::$app->security->generatePasswordHash($password));
            $records->setAttribute('status',10);
            $records->save();
            return "New user successfully created";
        }
    }
    
    
    
}