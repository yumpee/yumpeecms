<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\models;
use Yii;
use backend\models\TagTypes;
use backend\models\TagsIndex;

class Tags extends \yii\db\ActiveRecord
{
   public static function tableName()
    {
        return 'tbl_tags';
    }
    public function getArticles($limit=0,$offset=0){
        return $this->hasMany(Articles::className(),['id'=>'articles_id'])->viaTable('tbl_articles_tag',['tags_id'=>'id']);
    }
    
    public static function saveTags(){
        $records = Tags::find()->where(['id'=>Yii::$app->request->post('id')])->one();
        $rec=1;
        $published=0;
        if(Yii::$app->request->post("published")=="on"){
            $published='1';
        }
        if($records!=null){   
           $records->setAttribute('url',Yii::$app->request->post("url"));
           $records->setAttribute('name',Yii::$app->request->post("name"));
           $records->setAttribute('description',Yii::$app->request->post("description"));
           $records->setAttribute('master_content','1');
           $records->save();
            return "Updates successfully made";
        }else{           
            if(Yii::$app->request->post("url")==""):
                $url=strtolower(str_replace(" ","-",Yii::$app->request->post("name")));
            else:
                $url=strtolower(str_replace(" ","-",Yii::$app->request->post("url")));
            endif;
           $url_gen=0;
           $url_exist = Pages::find()->where(['url'=>$url])->one();
           if($url_exist!=null):
               $url_gen=1;
               $url=$url."_".md5(date("YmdHis").rand(10000,1000000));
           endif;
           if($url_gen==0):
            $url_exist = Tags::find()->where(['url'=>$url])->one();
            if($url_exist!=null):
                $url_gen=1;
                $url=$url."_".md5(date("YmdHis").rand(10000,1000000));
            endif;
           endif;
           if($url_gen==0):
            $url_exist = Templates::find()->where(['url'=>$url])->one();
            if($url_exist!=null):
                $url_gen=1;
                $url=$url."_".md5(date("YmdHis").rand(10000,1000000));
            endif;
           endif;
           if($url_gen==0):
            $url_exist = ArticlesCategories::find()->where(['url'=>$url])->one();
            if($url_exist!=null):
                $url_gen=1;
                $url=$url."_".md5(date("YmdHis").rand(10000,1000000));
            endif;
           endif;
           $records = new Tags();
           $records->setAttribute('id',md5(date('Ymdis').rand(1000,100000)));
           $records->setAttribute('url',Yii::$app->request->post("url"));
           $records->setAttribute('name',Yii::$app->request->post("name"));
           $records->setAttribute('description',Yii::$app->request->post("description"));
           $records->setAttribute('master_content','1');
           $records->save();    
           return "New tag successfully created";
        }
    }
    public static function getTags(){
        $session = Yii::$app->session;
        $mydatabase = $session['mydatabase'];
        $query = new \yii\db\Query;
        $search = Yii::$app->request->get('search');
        if(Yii::$app->request->get('search')):
                return Tags::find()->where(['LIKE','name','%'.$search.'%'])->orderBy('name')->all();
            else:
                return Tags::find()->orderBy('name')->all();
        endif;
        
    }
    public static function getTagTypes(){
        return TagTypes::find()->orderBy('name')->all();
    }
    
     public static function saveTagType(){        
        $records = TagTypes::find()->where(['id'=>Yii::$app->request->post('id')])->one();
        if($records!=null){    
            $records->setAttribute('name',Yii::$app->request->post("name"));
            $records->save();
            TagsIndex::deleteAll(['index_id'=>$id]);       
        
        $tag_array = Yii::$app->request->post("tag_array");            
        $ev_arr = explode(" ",$tag_array);           
        $counter=0;
                       
        for($i=0; $i < count($ev_arr);$i++):            
               $ic = md5(date('YmdHi').$counter);
               $counter++;
               if(trim($ev_arr[$i])!=""):                    
                    $c = new TagsIndex();
                        $c->setAttribute('tags_id',$ev_arr[$i]);
                        $c->setAttribute('index_id',$id);
                        $c->save();
               endif;
        endfor;
        
            return "Tag Type Updated ";
        }else{
            $id = md5(date('Ymdis'));
            $records = new TagTypes();
            $records->setAttribute('id',$id);
            $records->setAttribute('name',Yii::$app->request->post("name"));
            $records->save();                       
            TagsIndex::deleteAll(['index_id'=>$id]);       
            $tag_array = Yii::$app->request->post("tag_array");            
            $ev_arr = explode(" ",$tag_array);           
            $counter=0;
                       
            for($i=0; $i < count($ev_arr);$i++):            
                $ic = md5(date('YmdHi').$counter);
                $counter++;
                if(trim($ev_arr[$i])!=""):
                        
                        $c = new TagsIndex();
                        $c->setAttribute('tags_id',$ev_arr[$i]);
                        $c->setAttribute('index_id',$id);
                        $c->save();
                endif;
            endfor;
            return "New tag successfully created";
        }
        
     }

}