<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;
use frontend\models\ProfileDetails;
use frontend\components\ContentBuilder;

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
        
        $user->setAttribute('role_id',ContentBuilder::getSetting("registration_role"));
        $user->setAttribute('status',10);
        return $user->save() ? $user : null; 
        
    }
}
