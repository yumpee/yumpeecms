<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace backend\controllers;
use Yii;
/**
 * Description of CommentController
 *
 * @author Peter
 */
use yii\web\Controller;
use backend\models\Comments;

class CommentController extends Controller{
    //put your code here
    public function actionIndex(){
        $page = [];
        $page['records'] = Comments::find()->all();
        return $this->render('index',$page);
    }
    public function actionDelete(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = Comments::findOne($id);
    $a->delete();
    echo "Record successfully deleted";
}
    public function actionApprove(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = Comments::findOne($id);
    if($a->status=='N'):
            $a->setAttribute('status', 'Y');
        else:
            $a->setAttribute('status', 'N');
    endif;
    $a->update();
    echo "Record successfully updated";
}
}
