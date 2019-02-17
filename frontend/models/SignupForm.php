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
use yii\base\Model;
use common\models\User;
use frontend\models\ProfileDetails;
use frontend\components\ContentBuilder;
use backend\models\Roles;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->first_name = $this->first_name;
        $user->last_name = $this->last_name;
        $user->update_at = time();
        $user->created_at=time();
        $user->status=1;
        $user->role_id=0;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        
        return $user->save() ? $user : null;
    }
    public function registerNewUser(){
        //check if a user already exist with this username
        if(trim(Yii::$app->request->post("username")=="")):
            return ["error"=>"Username cannot be blank"];
        endif;
        $user = User::find()->where(['username'=>Yii::$app->request->post("username")])->one();
        if($user!=null):
            return ["error"=>"Username already exist"];
        endif;
        
        $password = Yii::$app->request->post('password');
        $user = new User();
        //lets check for unique attributes been checked
        if(Yii::$app->request->post("unique-field")!==null && Yii::$app->request->post("unique-field")!=""):
            $field_list = explode(",",Yii::$app->request->post("unique-field"));
            foreach($field_list as $field):
                if ($user->hasAttribute($field)):
                    $record = User::find()->where([$field=>Yii::$app->request->post($field)])->one();
                    if($record!=null):
                        return ["error"=>"Field " .$field." already exist"];
                    endif;
                else:
                    $record = ProfileDetails::find()->where(['param'=>$field,'param_val'=>Yii::$app->request->post($field)])->one();
                    if($record!=null):
                        return ["error"=>"Field " .$field." already exist"];
                    endif;
                endif;
            endforeach;
        endif;     
        if(Yii::$app->request->post("role_id")!==null && Yii::$app->request->post("role_id")!=""):
            $role_arr = Roles::find()->where(['id'=>Yii::$app->request->post("role_id")])->andWhere('access_type="F"')->one();
        endif;
        
        $user->setAttribute('username',Yii::$app->request->post("username"));
        $user->setAttribute('first_name',Yii::$app->request->post("first_name"));
        $user->setAttribute('last_name',Yii::$app->request->post("last_name"));
        $user->setAttribute('password_hash',Yii::$app->security->generatePasswordHash($password));
        $user->setAttribute('extension',Yii::$app->request->post("extension"));
        $user->setAttribute('updated_at',time());
        $user->setAttribute('created_at',time());
        $user->setAttribute('about',Yii::$app->request->post("about"));
        $user->setAttribute('title','');
        $user->setAttribute('email',Yii::$app->request->post("email"));        
        if($role_arr!=null):
            $user->setAttribute('role_id',$role_arr->id);
        else:
            $user->setAttribute('role_id',ContentBuilder::getSetting("registration_role"));
        endif;
        
        $user->setAttribute('status',10);
        return $user->save() ? $user : null; 
        
    }
}
