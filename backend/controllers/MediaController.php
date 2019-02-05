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


class MediaController extends Controller{

    public function actionIndex(){
        $page['id']= Yii::$app->request->get('id',null);
        if($page['id']!=null):
                $page['rs'] = Media::find()->where(['id' => $page['id']])->one();
            else:
                $page['rs'] = Media::find()->where(['id' => "0"])->one();
        endif;
        $page['records'] = Media::find()->orderBy('name')->all();
        $page['model'] = new Media();
        return $this->render('index',$page);
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
            $a = Yii::$app->request->get('id');
                $random = rand(1,10000);
                $media = new Media();
                $media->setAttribute('id',md5(date('YmdHis')).$random);
                $media->setAttribute('upload_date',date('Y-m-d'));
                $media->setAttribute('author',Yii::$app->user->identity->id);
                $media->setAttribute('media_type','1');
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
}

