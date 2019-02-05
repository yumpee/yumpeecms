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
namespace backend\models;

use Yii;
use backend\models\Articles;
use backend\models\ArticlesCategoryIndex;

use frontend\components\ContentBuilder;

class ArticlesCategories extends \yii\db\ActiveRecord
{
   public static function tableName()
    {
        return 'tbl_articles_category';
    }
  protected function getDisplayImage(){
      return $this->hasOne(Media::className(),['id'=>'display_image_id']);
  }
  protected function getCount(){
      //returns the number of articles within this class instantiation. Also available via the count attribute
      return count($records = Yii::$app->db->createCommand("SELECT articles_id FROM tbl_articles_category_related WHERE category_id='".$this->id."'")->queryAll());      
  }
  public function getCategoryURL(){
      return Yii::$app->request->getBaseUrl()."/".ContentBuilder::getURLByRoute("blog/category");
  }
  public function getImage(){
      return ContentBuilder::getImage($this->display_image_id);
  }
  protected function getIndexURL(){
      $index_category = Yii::$app->db->createCommand("SELECT category_index_id FROM tbl_articles_category_index WHERE category_id='".$this->id."'")->queryOne();
      if(count($index_category)>0):
          $article_id = $index_category['category_index_id'];
          if($article_id!=null):
                $pg = Pages::find()->where(['id'=>$article_id])->one();
                if($pg!=null):
                    return $pg->url;
                else:
                    return "";
                endif;
          endif;
      else:
          return "";
      endif;
  }
  
  public function getBlogCategoryIndex($page_id){
       $subquery = (new \yii\db\Query())->select('category_id')->from('tbl_articles_category_index')->where(['category_index_id'=>$page_id]);
       $records = $this::find()->where(['id'=>$subquery])->limit('20')->all();
       return $records;
  }
    
    public static function saveEventsCategory(){        
        $records = ArticlesCategories::find()->where(['id'=>Yii::$app->request->post('id')])->one();
        $rec=1;
        $published=0;
        if(Yii::$app->request->post("published")=="on"){
            $published='1';
        }
        if($records !=null){               
           $id = Yii::$app->request->post('id');
           $records->setAttribute('url',Yii::$app->request->post("url"));
           $records->setAttribute('name',Yii::$app->request->post("name"));
           $records->setAttribute('description',Yii::$app->request->post("description"));
           $records->setAttribute('display_order',Yii::$app->request->post("display_order"));
           $records->setAttribute('published',Yii::$app->request->post("published"));
           $records->setAttribute('display_image_id',Yii::$app->request->post("display_image_id"));
           $records->setAttribute('master_content','1');
           $records->setAttribute('icon',Yii::$app->request->post("icon"));
           $records->save();
            
           ArticlesCategoryIndex::deleteAll(['category_id'=>Yii::$app->request->post('id')]);
           
           $pages = Yii::$app->request->post("category");
           if(!empty($pages)){
               $counter=0;
                // Loop to store and display values of individual checked checkbox.
                foreach($pages as $selected){  
                    $c = new ArticlesCategoryIndex();
                    $c->setAttribute('category_id',$id);
                    $c->setAttribute('category_index_id',$selected);
                    $c->save();
                }
                
            }
            
            return "Updates successfully made";
        }else{           
            
           $id = md5(date('Ymdis'));
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
           $records = new ArticlesCategories();
           $records->setAttribute('id',$id);
           $records->setAttribute('url',$url);
           $records->setAttribute('name',Yii::$app->request->post("name"));
           $records->setAttribute('description',Yii::$app->request->post("description"));
           $records->setAttribute('display_order',Yii::$app->request->post("display_order"));
           $records->setAttribute('published',Yii::$app->request->post("published"));
           $records->setAttribute('display_image_id',Yii::$app->request->post("display_image_id"));
           $records->setAttribute('icon',Yii::$app->request->post("icon"));
           $records->setAttribute('master_content','1');
           $records->save();
           
           $pages = Yii::$app->request->post("category");
           if(!empty($pages)){
               $counter=0;
                // Loop to store and display values of individual checked checkbox.
                foreach($pages as $selected){                    
                    $c = new ArticlesCategoryIndex();
                    $c->setAttribute('category_id',$id);
                    $c->setAttribute('category_index_id',$selected);
                    $c->save();
                
                
                }
                
            }
            return "New blog category successfully created";
        }
    }
    public static function getMyEventsCategories(){
        return ArticlesCategories::find()->orderBy('name')->all();
    }
     
    public static function getCategoryIndex($id){
        return ArticlesCategoryIndex::find()->where(['category_id'=>$id])->all();
    }
}