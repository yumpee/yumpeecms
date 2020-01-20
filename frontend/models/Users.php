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
use backend\models\Relationships;
use backend\models\RelationshipDetails;

class Users extends \backend\models\Users {
    
    public static function tableName()
    {
        return 'tbl_user';
    }
    public function getDisplayImage(){
        return $this->hasOne(\backend\models\Media::className(),['id'=>'display_image_id']);
    }
    public function getDetails(){
        return $this->hasMany(ProfileDetails::className(),['profile_id'=>'id']);
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
    
    public function getRelationData() {
        $ARMethods = get_class_methods('\yii\db\ActiveRecord');
        $modelMethods = get_class_methods('\yii\base\Model');
        $reflection = new \ReflectionClass($this);
        $i = 0;
        $stack = [];
        
        /* @var $method \ReflectionMethod */
        
        foreach ($reflection->getMethods() as $method) {
            if (in_array($method->name, $ARMethods) || in_array($method->name, $modelMethods)) {
                continue;
            }
            if($method->name === 'bindModels')  {continue;}
            if($method->name === 'attachBehaviorInternal')  {continue;}
            if($method->name === 'getRelationData')  {continue;}
            if($method->name ==='resetDependentRelations') {continue;}
            if($method->name ==='setRelationDependencies') {continue;}
            try {
                $rel = call_user_func(array($this,$method->name));
                if($rel instanceof \yii\db\ActiveQuery){
                    $stack[$i]['name'] = lcfirst(str_replace('get', '', $method->name));
                    $stack[$i]['method'] = $method->name;
                    $stack[$i]['ismultiple'] = $rel->multiple;
                    $i++;
                }
            } catch (\yii\base\ErrorException $exc) {
//                
            }
        }
        return $stack;
    }
    function getRelationships($name=""){        
        $return_val=[];        
        $forms = Relationships::find()->select('source_id')->where(['source_id'=>$this->form->name])->all();
        if($forms!=null):
            return Forms::find()->where(['IN','name',$forms])->all();
        endif;
        return $return_val;
 }
}