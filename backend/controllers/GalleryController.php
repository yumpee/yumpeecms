<?php

/*
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */

namespace backend\controllers;

/**
 * Description of GalleryController
 *
 * @author Peter
 */
use Yii;
use yii\web\Controller;
use backend\models\Gallery;
use backend\models\GalleryImage;

class GalleryController extends Controller{
    public function actionIndex()
    {
    $page=[]; 
    $page['name']="";
    
    $page['id']="";
    
    $page['id'] = Yii::$app->request->get('id',null);
        if($page['id']!=null){            
            $page['rs'] = Gallery::find()->where(['id' => $page['id']])->one();
            $page['edit']=true;
        }else{
            $page['rs'] = new Gallery();
        }
            
       
    
    $page['records'] = Gallery::find()->all();
    return $this->render('index',$page); 
   }
   
public function actionSave(){
            $model = Gallery::findOne(Yii::$app->request->post("id"));
            if($model!=null):
                $model->setAttribute("name",Yii::$app->request->post("name"));
                $model->setAttribute("title",Yii::$app->request->post("title"));
                $model->setAttribute("description",Yii::$app->request->post("description"));
                $model->save();
                GalleryImage::deleteAll(['gallery_id'=>Yii::$app->request->post("id")]);
                $img_array = Yii::$app->request->post("image_listing");            
                $ev_arr = explode(" ",$img_array);
                for($i=0; $i < count($ev_arr);$i++): 
                    if(trim($ev_arr[$i])!=""):
                            $model = new GalleryImage();
                            $random=rand(1000,10000);
                            $model->setAttribute("id",md5(date('YmdHis')).$random);
                            $model->setAttribute("gallery_id",Yii::$app->request->post("id"));
                            $model->setAttribute("image_id",$ev_arr[$i]);
                            $model->save();                            
                    endif;
                endfor;
                
                return "Gallery successfully updated";
            else:
                $model =  new Gallery();
                $model->setAttribute('id',md5(date('YmdHis')));
                $model->setAttribute("name",Yii::$app->request->post("name"));
                $model->setAttribute("title",Yii::$app->request->post("title"));
                $model->setAttribute("description",Yii::$app->request->post("description"));
                $model->save();
                return "New gallery created";
            endif;
            
}
public function actionDelete(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = Gallery::findOne($id);
    $a->delete();
    echo "Record successfully deleted";
}
   
public function actionDeleteGalleryImage(){
    GalleryImage::deleteAll(['gallery_id'=>Yii::$app->request->get('gallery'),'image_id'=>Yii::$app->request->get('id')]);
    echo "Record successfully deleted";
}
}
