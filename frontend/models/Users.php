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
    public function getRole(){
        return $this->hasOne(\backend\models\Roles::className(),['id'=>'role_id']);
    }
    
}