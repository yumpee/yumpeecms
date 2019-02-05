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

namespace backend\controllers;
use Yii;
use yii\web\Controller;
use backend\models\Feedback;
use backend\models\FeedbackDetails;
/**
 * Description of Feedback
 *
 * @author Peter
 */
class FeedbackController extends Controller{
    //put your code here
    public function actionIndex(){
        $page=[];
        $page['id']= Yii::$app->request->get('id',null);
        $page['cat_id']= Yii::$app->request->get('cat_id',null);
        if($page['id']!=null):
                $page['rs'] = Feedback::find()->where(['id' => $page['id']])->one();
            else:
                $page['rs'] = Feedback::find()->where(['id' => "0"])->one();
        endif;
        $page['records'] = Feedback::find()->where(['feedback_type'=>'contact'])->orderBy(['date_submitted'=>SORT_DESC])->all();
        return $this->render('index',$page);
    }
    public function actionDelete(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = Feedback::findOne($id);
    $a->delete();
    FeedbackDetails::deleteAll(['feedback_id'=>Yii::$app->request->get("id")]);
    echo "Record successfully deleted";
    }
    
    public function actionDetails(){
        $return="";
        $details = FeedbackDetails::find()->where(['feedback_id'=>Yii::$app->request->get("id")])->all();
        foreach($details as $record):
            $return.="<br><b>".$record['param'].":</b><br>".$record['param_val'];            
        endforeach;
        $a = Feedback::findOne(['id'=>Yii::$app->request->get("id")]);        
        $a->setAttribute("status","R");
        $a->update(false);
        return $return;
    }
}
