<?php
namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\AuthorizationCodes;
use common\models\AccessTokens;
use backend\models\ServicesIncoming;

use backend\models\SignupForm;
use common\components\Verbcheck;
use common\components\Apiauth;


/**
 * Site controller
 */
class ApiController extends RestController
{
    /**
     * @inheritdoc
     */
    public $enableCsrfValidation = false;
    public function behaviors()
    {

        $behaviors = parent::behaviors();

        return $behaviors + [
            'apiauth' => [
                'class' => Apiauth::className(),
                'exclude' => ['authorize', 'register', 'accesstoken','index','authenticate','forms','classes','profile','articles'],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'me'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['authorize', 'register', 'accesstoken'],
                        'allow' => true,
                        'roles' => ['*'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => Verbcheck::className(),
                'actions' => [
                    'logout' => ['GET'],
                    'authorize' => ['POST'],
                    'register' => ['POST'],
                    'accesstoken' => ['POST'],
                    'me' => ['GET'],
                    'forms'=>['GET'],
                    'articles'=>['GET'],
                    'classes'=>['GET'],
                ],
            ],
        ];
    }


    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        Yii::$app->api->sendSuccessResponse(['Yumpee CMS API with OAuth2 is enabled']);
        //  return $this->render('index');
    }

    public function actionRegister()
    {

        $model = new SignupForm();
        $model->attributes = $this->request;

        if ($user = $model->signup()) {

            $data=$user->attributes;
            unset($data['auth_key']);
            unset($data['password_hash']);
            unset($data['password_reset_token']);

            Yii::$app->api->sendSuccessResponse($data);

        }

    }


    public function actionMe()
    {
        $data = Yii::$app->user->identity;
        $data = $data->attributes;
        unset($data['auth_key']);
        unset($data['password_hash']);
        unset($data['password_reset_token']);
        Yii::$app->api->sendSuccessResponse($data);
    }
    

    public function actionAccesstoken()
    {

        if (!isset($this->request["authorization_code"])) {
            Yii::$app->api->sendFailedResponse("Authorization code missing");
        }

        $authorization_code = $this->request["authorization_code"];

        $auth_code = AuthorizationCodes::isValid($authorization_code);
        if (!$auth_code) {
            Yii::$app->api->sendFailedResponse("Invalid Authorization Code");
        }

        $accesstoken = Yii::$app->api->createAccesstoken($authorization_code);

        $data = [];
        $data['access_token'] = $accesstoken->token;
        $data['expires_at'] = $accesstoken->expires_at;
        Yii::$app->api->sendSuccessResponse($data);

    }

    public function actionAuthorize()
    {
        $model = new LoginForm();

        $model->attributes = $this->request;


        if ($model->validate() && $model->login()) {

            $auth_code = Yii::$app->api->createAuthorizationCode(Yii::$app->user->identity['id']);

            $data = [];
            $data['authorization_code'] = $auth_code->code;
            $data['expires_at'] = $auth_code->expires_at;

            Yii::$app->api->sendSuccessResponse($data);
        } else {
            Yii::$app->api->sendFailedResponse($model->errors);
        }
    }

    public function actionLogout()
    {
        $headers = Yii::$app->getRequest()->getHeaders();
        $access_token = $headers->get('x-access-token');

        if(!$access_token){
            $access_token = Yii::$app->getRequest()->getQueryParam('access-token');
        }

        $model = AccessTokens::findOne(['token' => $access_token]);

        if ($model->delete()) {

            Yii::$app->api->sendSuccessResponse(["Logged Out Successfully"]);

        } else {
            Yii::$app->api->sendFailedResponse("Invalid Request");
        }


    }
    
    public function actionServices(){
		$request = Yii::$app->request->post();
		$headers = Yii::$app->request->headers;
		$authorisation = $headers->get('Authorization');
		list($title,$encryption) = explode(" ",$authorisation);
		if(trim($title)=="Basic"):
			//this is using a Basic HTTP Authentication
			$credential = base64_decode($encryption);
			list($authentication_id,$authentication_key) = explode(":",trim($credential));
			//now we validate to get the client
			$client_record = ServicesIncoming::find()->where(['client_authenticate_id'=>$authentication_id,'client_authenticate_key'=>$authentication_key])->one();
			if($client_record==null):
				return Yii::$app->api->sendFailedResponse("Invalid Authentication Information");
			endif;
			$client_id = $client_record->client_id;
		
		elseif(trim($title)=="Bearer"):		
			//return $request['client_id'];
			//now we validate to get the client
			$client_record = ServicesIncoming::find()->where(['authentication_token'=>$encryption,'client_id'=>$request['client_id']])->one();
			if($client_record==null):
				return Yii::$app->api->sendFailedResponse("Invalid Authorization Bearer token");
			endif;
			$client_id = $client_record->client_id;
		
		elseif(isset($request['token'])):
			//now we validate by QueryAuth
			$token_rec = ServicesIncoming::find()->where(['authentication_token'=>$request['token'],'client_id'=>$request['client_id']])->one();
			if($token_rec==null){					
					return Yii::$app->api->sendFailedResponse("Invalid token ID");
			}else{
					$client_id = $token_rec['client_id'];
			}
		else:
					return Yii::$app->api->sendFailedResponse("Invalid token ID");
		endif;
		
		$status = Yii::$app->request->post("status",null);
		switch($status){
			case "fetch-status":
				if(isset($request["order_id"])):
					if($request["order_id"]=="all"):
						$record = ServiceRequest::find()->where(['ClientID'=>$client_id])->asArray()->limit("20")->all();
						Yii::$app->api->sendSuccessResponse($record);
					else:
						$record = ServiceRequest::find()->asArray()->where(['SRExternalID'=>$request["order_id"]])->andWhere('ClientID="'.$client_id.'"')->one();
						Yii::$app->api->sendSuccessResponse($record);
					endif;
				else:
					return Yii::$app->api->sendFailedResponse("Order ID not specified");
				endif;
			break;
			
		}
	}
        
	public function actionAuthenticate(){		
		$request = Yii::$app->request->post();
		
		//return json_encode($request);
                //Yii::$app->api->sendSuccessResponse(["23"]);
		if(isset($request['client_id']) && isset($request['client_secret'])){
		$token = ServicesIncoming::find()->where(['client_id'=>$request['client_id'],'client_key'=>$request['client_secret']])->one();
		if($token==null):
			return Yii::$app->api->sendFailedResponse("Invalid credentials");
		else:
			//we generate new token key
			$rand_token=md5(date("YmdHis").$request['client_id']);
			$token->setAttribute("authentication_token",$rand_token);
			$token->save(false);
			Yii::$app->api->sendSuccessResponse([$rand_token]);
		endif;
		}else{
                        //return Yii::$app->api->sendFailedResponse($request["client_id"]);
			return Yii::$app->api->sendFailedResponse("Invalid request parameters");
		}
	}
        
        
        public function actionForms(){
        $uri = substr(Yii::$app->request->url,strlen(Yii::$app->homeUrl));
        $request = explode("/",$uri);
        $query_request = Yii::$app->request; 
        if($query_request->isAjax):
        if($request[3]=="delete"):            
            $check = FormSubmit::find()->where(['id'=>$request[4]])->andWhere('usrname="'.Yii::$app->user->identity->username.'"')->one();
            if($check!=null):
                FormSubmit::deleteAll(['id'=>$request[4]]);
                FormData::deleteAll(['form_submit_id'=>$request[4]]);
                FormFiles::deleteAll(['form_submit_id'=>$request[4]]);
                Yii::$app->api->sendSuccessResponse(['Delete successful']);
            endif;
            Yii::$app->api->sendFailedResponse("Operation cannot be performed");
            return;
        endif;
        if($request[3]=="delete-file"):            
            $check = FormSubmit::find()->where(['id'=>$request[4]])->andWhere('usrname="'.Yii::$app->user->identity->username.'"')->one();
            if($check!=null):                
                FormFiles::deleteAll(['form_submit_id'=>$request[4],'id'=>$request[5]]);
                Yii::$app->api->sendSuccessResponse(['Delete successful']);
            endif;
            Yii::$app->api->sendFailedResponse("Operation cannot be performed");
            return;
        endif;
        if($request[3]=="delete-feedback"):      
            $check = Feedback::find()->where(['id'=>$request[4]])->andWhere('usrname="'.Yii::$app->user->identity->username.'"')->one();
            if($check!=null):  
                Feedback::deleteAll(['id'=>$request[4]]);
                FeedbackDetails::deleteAll(['feedback_id'=>$request[4],'id'=>$request[5]]);
                Yii::$app->api->sendSuccessResponse(['Delete successful']);
            endif;
            Yii::$app->api->sendFailedResponse("Operation cannot be performed");
            return;
        endif;
        if($request[2]!=null):
            $form = Forms::find()->where(['name'=>$request[2]])->one();
            if($form==null):
                Yii::$app->api->sendFailedResponse("Invalid object request");        
            endif;
            
            $data = FormSubmit::find()->with('data','file')->asArray()->where(['IN','form_id',$form->id])->all();
            Yii::$app->api->sendSuccessResponse($data);
            return;
        endif;
        endif;
        Yii::$app->api->sendSuccessResponse(['Yumpee CMS Hook form active']);
    }
    
    public function actionArticles(){
        $uri = substr(Yii::$app->request->url,strlen(Yii::$app->homeUrl));
        $request = explode("/",$uri);
        $query_request = Yii::$app->request; //we fetch the body parameters incase there is any sent
        if($query_request->isAjax):
        if($request[2]!=null):
            switch ($request[2]):
                case "fetch":
                    $query = Articles::find()->where(['published'=>'1']);
                    if(count($request) > 3):  
                        $query->andFilterWhere(['id'=>$request[3]])->with('displayImage','author','approvedComments')->asArray();
                    endif;
                    $data = $query->all();
                    Yii::$app->api->sendSuccessResponse($data);
                    return;
                break;
                
                default:
                    Yii::$app->api->sendFailedResponse("Invalid object request");
                
            endswitch;
            //Yii::$app->api->sendFailedResponse("Invalid object request");
        endif;
        else:
            Yii::$app->api->sendFailedResponse("Invalid object request");
            return;
        endif;
        
    }
    public function actionClasses(){
        $uri = substr(Yii::$app->request->url,strlen(Yii::$app->homeUrl));
        $request = explode("/",$uri);
        if($request[2]!=null):
            $form = ClassSetup::find()->where(['name'=>$request[2]])->one();
            if($form==null):
                Yii::$app->api->sendFailedResponse("Invalid object request");
        
            endif;
            if($request[3]!=null):
                if($request[3]=="elements"):
                    //  $data = ClassElement::find()->with('child','parent')->asArray()->where(['class_id'=>$form->id])->all();
                                if($request[4]==null):
                                    $data = ClassElement::find()->with('parent','child')->asArray()->where(['class_id'=>$form->id])->orderBy(['display_order'=>SORT_ASC,'alias'=>SORT_ASC])->all();
                                elseif($request[4]=="parent"):
                                    $data = ClassElement::find()->with('parent','child')->asArray()->where(['class_id'=>$form->id])->andWhere("parent_id=''")->orderBy(['display_order'=>SORT_ASC,'alias'=>SORT_ASC])->all();
                                                                   
                                else:
                                    if(trim($request[5])==""):
                                        $data = ClassElement::find()->with('parent','child')->asArray()->where(['class_id'=>$form->id])->andWhere("name='".$request[4]."'")->orderBy(['display_order'=>SORT_ASC,'alias'=>SORT_ASC])->one();
                                    else:
                                        $parent_obj = ClassElement::find()->where(['name'=>$request[4]])->one();
                                        $data = ClassElement::find()->asArray()->where(['parent_id'=>$parent_obj->id])->orderBy(['display_order'=>SORT_ASC,'alias'=>SORT_ASC])->all();
                                    endif;
                                endif;
                    return Yii::$app->api->sendSuccessResponse($data);
                endif;
                if($request[3]=="property"):
                    
                                if($request[4]==null):
                                    $data = ClassAttributes::find()->with('parent','child')->asArray()->where(['class_id'=>$form->id])->orderBy(['display_order'=>SORT_ASC,'alias'=>SORT_ASC])->all();
                                elseif($request[4]=="parent"):
                                    $data = ClassAttributes::find()->with('child','parent')->asArray()->where(['class_id'=>$form->id])->andWhere("parent_id=''")->orderBy(['alias'=>SORT_ASC])->all();
                                    
                                else:
                                    if($request[5]==null):
                                        $data = ClassAttributes::find()->with('parent','child')->asArray()->where(['class_id'=>$form->id])->andWhere("name='".$request[4]."'")->orderBy(['display_order'=>SORT_ASC,'alias'=>SORT_ASC])->one();
                                    else:
                                        $parent_obj = ClassAttributes::find()->where(['name'=>$request[4]])->one();
                                        $data = ClassAttributes::find()->where(['parent_id'=>$parent_obj->id])->orderBy(['display_order'=>SORT_ASC,'alias'=>SORT_ASC])->all();
                                    endif;
                                endif;
                    Yii::$app->api->sendSuccessResponse($data);
                endif;
            endif;
        endif;
        Yii::$app->api->sendSuccessResponse(['Yumpee CMS Hook form active']);
    }
    public function actionProfile(){
        //this is used to check the profile information of users
        $data=[];
        $uri = substr(Yii::$app->request->url,strlen(Yii::$app->homeUrl));
        $request = explode("/",$uri);
        //Yii::$app->api->sendSuccessResponse([$request[2]]);
        if($request[2]!=null):            
                if($request[2]=="fetch"):
                    $data = Users::find()->asArray()->where([$request[3]=>$request[4]])->one();
                    if($data==null):
                        Yii::$app->api->sendSuccessResponse(["message"=>"error"]);
                    endif;
                    Yii::$app->api->sendSuccessResponse($data);
                endif;
        endif;
        Yii::$app->api->sendSuccessResponse(['Yumpee CMS Hook form active']);
    }
    
    
}
