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
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\FileHelper;
use backend\models\Media;
use backend\models\BackEndMenus;
use backend\models\BackEndMenuRole;
use backend\models\Settings;
use backend\models\Gallery;
use backend\models\GalleryImage;

class MediaController extends Controller{
public function behaviors()
{
    if(Settings::find()->where(['setting_name'=>'use_custom_backend_menus'])->one()->setting_value=="on" && !Yii::$app->user->isGuest):
    $can_access=1;
    $route = "/".Yii::$app->request->get("r");
    //check to see if route exists in our system
    $menu_rec = BackEndMenus::find()->where(['url'=>$route])->one();
    if($menu_rec!=null):
        //we now check that the current role has rights to use it
        $role_access = BackEndMenuRole::find()->where(['menu_id'=>$menu_rec->id,'role_id'=>Yii::$app->user->identity->role_id])->one();
        if(!$role_access):
            //let's take a step further if there is a custom module
            $can_access=0;            
        endif;
    endif;
    if($can_access < 1):
        echo "You do not have permission to view this page";
        exit;
    endif;
    endif;
    
    return [
        'access' => [
            'class' => \yii\filters\AccessControl::className(),
            'only' => ['create', 'update'],
            'rules' => [
                // deny all POST requests
                [
                    'allow' => false,
                    'verbs' => ['POST']
                ],
                // allow authenticated users
                [
                    'allow' => true,
                    'roles' => ['@'],
                ],
                // everything else is denied
            ],
        ],
    ];
}
    public function actionIndex(){
        $page['id']= Yii::$app->request->get('id',null);
        $page['page_count']=40;
        if($page['id']!=null):
                $page['rs'] = Media::find()->where(['id' => $page['id']])->one();
            else:
                $page['rs'] = Media::find()->where(['id' => "0"])->one();
        endif;
        $page['records'] = Media::find()->orderBy('name')->limit($page['page_count'])->all();
        $page['total_count']= Media::find()->orderBy('name')->count();
        $page['model'] = new Media();
        $page['gallery'] = Gallery::find()->orderBy('name')->all();
        return $this->render('index',$page);
    }
    public function actionSearch(){
        $page=[];
        $page['total_count'] = Yii::$app->request->post("total_count");
        $page['page_count'] = Yii::$app->request->post("page_count");  
        $page['page_no'] = Yii::$app->request->post("page_no");  
        $offset = (Yii::$app->request->post("page_no") - 1) * Yii::$app->request->post("page_count");
        $page['records'] = Media::find()->where(['LIKE','name',Yii::$app->request->post("search_text")])->offset($offset)->orderBy('name')->limit(Yii::$app->request->post("page_count"))->all();
        
        return $this->renderAjax('search',$page,true,true);
    }
    public function actionEdit(){
        $page['id']= Yii::$app->request->get('id',null);
        $media_object = Media::find()->with('publisher')->asArray()->where(['id' => $page['id']])->one();
        return \yii\helpers\Json::encode($media_object);
    }
    
    public function actionInsertMedia(){
        $page['model'] = new Media();
        $page['records'] = Media::find()->orderBy('name')->all();
        $page['id']= Yii::$app->request->get('id',null);
        return $this->render('insert_media',$page);
    }
    
    public function actionFeaturedMedia(){
        //what if we want to reload just the data side of things
        $page['model'] = new Media();
        $page['records'] = Media::find()->orderBy('name')->all();
        $page['id']= Yii::$app->request->get('id',null);
        return $this->render('featured',$page);
    }
    
    public function actionSave(){
            $model = Media::findOne(Yii::$app->request->post("id"));
            if($model!=null):
                $model->attributes = Yii::$app->request->post();
                $model->save();
                return "Media successfully updated";
            else:
                $media =  new Media();
                $media->attributes = Yii::$app->request->post();
                $media->setAttribute('id',md5(date('YmdHis')));
                $media->setAttribute('upload_date',date('Y-m-d'));
                $media->setAttribute('author',Yii::$app->user->identity->id);
                $media->setAttribute('media_type','1');
                $media->setAttribute('size','100');
                $media->save();
                return "New media uploaded";
            endif;
    }
    public function actionDelete(){
    $id = str_replace("}","",Yii::$app->request->get("id"));    
    $a = Media::findOne($id);
    $a->delete();
    echo "Record successfully deleted";
    }
    
    public function actionImageUpload()
{
    $model = new Media();

    $imageFile = \yii\web\UploadedFile::getInstance($model, 'id');
    $current_year = date("Y");
    
    $directory = Yii::getAlias('@uploads/uploads/') .Yii::$app->session->id;
    
    if (!is_dir($directory)) {
        FileHelper::createDirectory($directory);
    }

    if ($imageFile) {
        $uid = uniqid(time(), true);
        $uid= str_replace(".","-",$uid);
        $fileName = $uid . '.' . $imageFile->extension;
        $filePath = $directory . "/".$fileName;
        if ($imageFile->saveAs($filePath)) {
            $path = Yii::getAlias('@uploads/uploads/').Yii::$app->session->id ."/".$fileName;
            //$path = Yii::getAlias('@webstart/images/'). Yii::$app->session->id . DIRECTORY_SEPARATOR . $fileName;
            //we need to get the media type
            $image_location=realpath($path);
            $mime = mime_content_type($image_location);
            $mime_type = explode("/",$mime);
            $up_type="1";
            if($mime_type[0]=="image"):
                $up_type="1";
            elseif($mime_type[0]=="video"):
                $up_type="2";
            elseif($mime_type[0]=="audio"):
                $up_type="3";
            elseif($mime_type[0]=="text"):
                $up_type="4";
            elseif($mime_type[0]=="application"):
                $up_type="5";
            else:
                $up_type="6";
            endif;
            $a = Yii::$app->request->get('id');
                $random = rand(1,10000);
                $media = new Media();
                $media->setAttribute('id',md5(date('YmdHis')).$random);
                $media->setAttribute('upload_date',date('Y-m-d'));
                $media->setAttribute('author',Yii::$app->user->identity->id);
                $media->setAttribute('media_type',$up_type);
                $media->setAttribute('size',$imageFile->size);
                $media->setAttribute('path',Yii::$app->session->id ."/".$fileName);
                $media->setAttribute('name',$imageFile->baseName);
                $media->setAttribute('alt_tag',$imageFile->baseName);
                
                $media->save();
            return \yii\helpers\Json::encode([
                'files' => [
                    [
                        'name' => $fileName,
                        'size' => $imageFile->size,
                        'url' => $path,
                        'thumbnailUrl' => $path,
                        'deleteUrl' => 'media/image-delete?name=' . $fileName,
                        'deleteType' => 'POST',
                    ],
                ],
            ]);
        }
    }

    return '{}';
}

public function actionImageDelete($name)
{
    $current_year = date("Y");
    $directory = Yii::getAlias('@uploads/uploads/') . $current_year.DIRECTORY_SEPARATOR . Yii::$app->session->id;
    if (is_file($directory . DIRECTORY_SEPARATOR . $name)) {
        unlink($directory . DIRECTORY_SEPARATOR . $name);
    }

    $files = FileHelper::findFiles($directory);
    $output = [];
    foreach ($files as $file) {
        $fileName = basename($file);
        $path = Yii::getAlias('@uploads/uploads/').Yii::$app->session->id ."/".$fileName;
        $output['files'][] = [
            'name' => $fileName,
            'size' => filesize($file),
            'url' => $path,
            'thumbnailUrl' => $path,
            'deleteUrl' => 'media/image-delete?name=' . $fileName,
            'deleteType' => 'POST',
        ];
    }
    return \yii\helpers\Json::encode($output);
}
public function actionGallery(){
    $gallery = Gallery::find()->orderBy('name')->all();
    foreach($gallery as $a):
        if(Yii::$app->request->post("g".$a->id)=="on"):            
            $model = new GalleryImage();
            $id = md5(date("YmdHis").rand(1000,10000000));
            $model->setAttribute("id",$id);
            $model->setAttribute("gallery_id",$a->id);
            $image = Media::find()->where(['id'=>Yii::$app->request->post("gallery_image_id")])->one();
            $check = GalleryImage::find()->where(['gallery_id'=>$a->id,'image_id'=>$image["path"]])->one();
            if($check!==null):
                continue;
            endif;
            if($image!=null):
                $model->setAttribute("image_id",$image["path"]);
                $model->save(false);
            endif;
        endif;
    endforeach;
    return "Image(s) successfully added";
}

}

