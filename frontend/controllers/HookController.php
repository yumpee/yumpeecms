<?php
namespace frontend\controllers;
/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use backend\models\Forms;
use backend\models\ClassSetup;
use backend\models\ClassElement;
use backend\models\ClassAttributes;
use backend\models\Articles;
use frontend\models\FormSubmit;
use frontend\models\FormData;
use frontend\models\FormFiles;
use frontend\models\Feedback;
use frontend\models\FeedbackDetails;
use frontend\models\Users;

class HookController extends Controller{
    /*
     * This class is used to interract with different aspect of Yumpee once a form has been submitted or a process has been completed
     */
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
    
    public function actionIndex(){
        Yii::$app->api->sendSuccessResponse(['Yumpee CMS Hook active']);
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
    