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
        if(Yii::$app->request->get("article_id")!=null):
            $page['records'] = Comments::find()->where(['target_id'=>Yii::$app->request->get("article_id")])->all();
        else:
            $page['records'] = Comments::find()->all();
        endif;
        
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
