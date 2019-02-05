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
use frontend\components\ContentBuilder;
use backend\models\ArticleMedia;
use backend\models\ArticlesBlogIndex;
use backend\models\ArticlesCategoryRelated;
use backend\models\ArticlesTag;
use common\models\ArticleDetails;
use backend\models\Feedback;

class Articles extends \yii\db\ActiveRecord
{
   public $image = 'hello world';
   public static function tableName()
    {
        return 'tbl_articles';
    }
    
    public function rules()
    {
        return [
            [['id', 'url','title','lead_content','body_content','date','published','show_header_image','usrname'], 'required'],
            [['id','no_of_views','thumbnail_image_id','display_image_id'],'safe'],
            
        ];
    }
    public function getDisplayImage(){
        //this gets the Display object array from the Media class
        return $this->hasOne(Media::className(),['id'=>'display_image_id']);
        
    }
    public function getDocuments(){
        return $this->hasMany(ArticleMedia::className(),['article_id'=>'id']);
    }
    
    public function getRatingCount(){
        return $this->hasMany(RatingDetails::className(),['target_id'=>'id'])->where(['target_type'=>'A'])->count();
    }
    public function getWidgetImage(){
        //this gets the whole image URL object array from the Media class. This function should probably not be used as themes will render images themselves
        return ContentBuilder::getImage($this->display_image_id,"logo");
    }
    public function getThumbnailImage(){
        //this gets the whole thumbnail URL object array from the Media class
        return ContentBuilder::getImage($this->thumbnail_image_id,"thumbnail");
    }
    public function getThumbnail(){
        return $this->hasOne(Media::className(),['id'=>'thumbnail_image_id']);
    }
    public function getFeedbacks(){
        return $this->hasMany(Feedback::className(),['target_id'=>'id'])->where(['feedback_type'=>'articles']);       
        
    }
    
    public function getAuthor(){
        return $this->hasOne(Users::className(),['username'=>'usrname']);
    }
    public function getRating(){
        
    }
    public function getDetails(){
        return $this->hasMany(ArticleDetails::className(),['article_id'=>'id']);
    }
    
    public function getAuthorsDirectory(){
        //this returns the directory of authors. You can append the authors username to get information about the author
        $record = Templates::find()->where(['route'=>'tags/authors'])->one();
        return Yii::$app->request->getBaseUrl()."/".$record->url;
    }
    public function getArchivesDirectory(){
     $record = Templates::find()->where(['route'=>'tags/archives'])->one();
     return Yii::$app->request->getBaseUrl()."/".$record->url; 
    }
    
    public function getDetailImage(){
      return ContentBuilder::getImage($this->display_image_id);
    }
    public function getFormattedIndexURL(){
      return Yii::$app->request->getBaseUrl()."/".ContentBuilder::getURLByRoute("blog/index");
  }
    protected function getIndexURL(){
      
      $index_blog = ArticlesBlogIndex::find()->where(['articles_id'=>$this->id])->one();
      if($index_blog!=null):
          $article_id = $index_blog['blog_index_id'];
		if($article_id!=null):
			return Pages::find()->where(['id'=>$article_id])->one()['url'];
		else:
			return "";
		endif;
      else:
          return "";
      endif;
  }
  public function getIndexItemsCount($related_id,$query_type){
      $query= new \yii\db\Query;
       if($query_type=="blog"):
           $subquery = (new \yii\db\Query())->select('articles_id')->from('tbl_articles_blog_index')->where(['blog_index_id'=>$related_id]);
       else:
           $subquery = (new \yii\db\Query())->select('articles_id')->from('tbl_articles_category_related')->where(['category_id'=>$related_id]);
       endif;
       return $this::find()->where(['id'=>$subquery,'published'=>'1'])->count();
  }
  
  public function getIndexItems($related_id,$query_type,$page='1'){
      $query= new \yii\db\Query;
       if($query_type=="blog"):
           $subquery = (new \yii\db\Query())->select('articles_id')->from('tbl_articles_blog_index')->where(['blog_index_id'=>$related_id]);
       else:
           $subquery = (new \yii\db\Query())->select('articles_id')->from('tbl_articles_category_related')->where(['category_id'=>$related_id]);
       endif;
       $page_size = ContentBuilder::getSetting("page_size");
       if($page_size==0):
           return $this::find()->where(['id'=>$subquery,'published'=>'1'])->all();
       endif;
      $offset = ($page - 1) * $page_size;
      return $this::find()->where(['id'=>$subquery,'published'=>'1'])->offset($offset)->limit($page_size)->all();
      
  }
  public function getArchiveDate(){
        $archive = $this->archive;
      if(strlen($this->archive) < 6):
          $archive = "0".$this->archive;
      endif;
      return  date('M', mktime(0, 0, 0, substr($archive,0,2), 10))." ".substr($archive,2); // March
  }
  public function getPublishDate(){
      //we get the date format type from settings and then use it to return the Publish Date
      $date_obj = Settings::findOne(['setting_name'=>'date_format']);
      return Yii::$app->formatter->asDate($this->date, 'php:'.$date_obj->setting_value);
      
  }
  public function getApprovedComments(){
      return $this->hasMany(Comments::className(),['target_id'=>'id'])->where(['status'=>'Y'])->orderBy('parent_id');
  }
  public function getComments(){
      return $this->hasMany(Comments::className(),['target_id'=>'id']);
  }
  public function getArticleCategories(){
      
      $subquery = (new \yii\db\Query())->select('category_id')->from('tbl_articles_category_related')->where(['articles_id'=>$this->id]);
      return ArticlesCategories::find()->where(['IN','id',$subquery])->all();
  }
  
    public static function saveArticle($save_as_new=""){        
        $model = Articles::find()->where(['id'=>Yii::$app->request->post('id')])->one();
        $rec=1;
        $published=0;
        if(Yii::$app->request->post("published")=="1"){
            $published='1';
        }else{
            $published='0';
        }
        if(Yii::$app->request->post("published_by_stat")=="1"){
            $published_by_stat='1';
        }else{
            $published_by_stat='0';
        }
        $permissions = Yii::$app->request->post("permissions");
            $perm_val="";
            if(!empty($permissions)){
               $counter=0;
                // Loop to store and display values of individual checked checkbox.
                foreach($permissions as $selected){                    
                    $perm_val = $perm_val." ".$selected;       
                }
            }
        
        if($model!=null && $save_as_new==""){         
           $id = Yii::$app->request->post('id');
           $model->setAttribute('url',Yii::$app->request->post("url"));
           $model->setAttribute('title',Yii::$app->request->post("title"));
           $model->setAttribute('lead_content',str_replace("../../uploads",Yii::getAlias("@image_dir"),Yii::$app->request->post("lead_content")));
           $model->setAttribute('body_content',str_replace("../../uploads",Yii::getAlias("@image_dir"),Yii::$app->request->post("body_content")));
           $model->setAttribute('date',Yii::$app->request->post("date"));
           $model->setAttribute('updated',date("Y-m-d H:i:s"));
           $model->setAttribute('master_content','1');
           $model->setAttribute('published',$published);
           $model->setAttribute('alternate_header_content',Yii::$app->request->post("alternate_header_content"));
           $model->setAttribute('show_header_image','1');
           $model->setAttribute('published_by_stat',$published_by_stat);           
           $model->setAttribute('display_image_id',Yii::$app->request->post("display_image_id"));
           $model->setAttribute('thumbnail_image_id',Yii::$app->request->post("thumbnail_image_id"));
           $model->setAttribute('article_type',Yii::$app->request->post("article_type"));
           $model->setAttribute('feedback',Yii::$app->request->post("feedback"));
           $model->setAttribute('featured_media',Yii::$app->request->post("featured_media"));
           $model->setAttribute('require_login',Yii::$app->request->post("require_login"));
           $model->setAttribute('disable_comments',Yii::$app->request->post("disable_comments"));    
           $model->setAttribute('render_template',Yii::$app->request->post("render_template"));
           $model->setAttribute('sort_order',Yii::$app->request->post("sort_order"));
           $model->setAttribute('permissions',$perm_val);
           $model->save();          
            
            
           
           ArticlesCategoryRelated::deleteAll(['articles_id'=>$id]);
           $pages = Yii::$app->request->post("category");
           
           //add the page relation to blocks
           if(!empty($pages)){
               $counter=0;
                // Loop to store and display values of individual checked checkbox.
                foreach($pages as $selected){
                    $insert_id = md5(date("Hims").$counter);
                    $counter++;                
                    $c = new ArticlesCategoryRelated();
                    $c->setAttribute('articles_id',$id);
                    $c->setAttribute('category_id',$selected);
                    $c->save(); 
                
                }
                
            }
        
        Yii::$app->db->createCommand()->delete('tbl_articles_blog_index','articles_id="'.$id.'"')->execute();
        $pages = Yii::$app->request->post("blog_index");
           if(!empty($pages)){
               $counter=0;
                // Loop to store and display values of individual checked checkbox.
                foreach($pages as $selected){
                    $insert_id = md5(date("Hims").$counter);
                    $counter++;
                    $c = new ArticlesBlogIndex();
                    $c->setAttribute('articles_id',$id);
                    $c->setAttribute('blog_index_id',$selected);
                    $c->save();                  
                }                
            }
        ArticlesTag::deleteAll(['articles_id'=>$id]);
        $tag_array = Yii::$app->request->post("tag_array");            
        $ev_arr = explode(" ",$tag_array);           
        $counter=0;
                       
        for($i=0; $i < count($ev_arr);$i++):            
               $ic = md5(date('YmdHi').$counter);
               $counter++;
               if(trim($ev_arr[$i])!=""):                    
                    $c = new ArticlesTag();
                    $c->setAttribute('tags_id',$ev_arr[$i]);
                    $c->setAttribute('articles_id',$id);
                    $c->save();   
               endif;
        endfor;        
            return "Updates successfully made";
        }else{           
            $id=md5(date('Ymdis'));
            if(Yii::$app->request->post("url")==""):
                $url=strtolower(str_replace(" ","-",Yii::$app->request->post("title")));
            else:
                $url=strtolower(str_replace(" ","-",Yii::$app->request->post("url")));
            endif;
            if($save_as_new!=""){
                $url=$url.substr(md5(date('si')),2,5);
            }
           //we need to check if a url already exist
           $url_exist = Articles::find()->where(['url'=>$url])->one();
           if($url_exist!=null):
               $url=$url."_".md5(date("YmdHis").rand(10000,1000000));
           endif;
           $archive = date("mY");
           $model = new Articles();
           $model->setAttribute('id',$id);
           $model->setAttribute('url',$url);
           $model->setAttribute('title',Yii::$app->request->post("title"));
           $model->setAttribute('lead_content',str_replace("../../uploads",Yii::getAlias("@image_dir"),Yii::$app->request->post("lead_content")));
           $model->setAttribute('body_content',str_replace("../../uploads",Yii::getAlias("@image_dir"),Yii::$app->request->post("body_content")));
           $model->setAttribute('date',date("Y-m-d"));
           $model->setAttribute('master_content','1');
           $model->setAttribute('published',$published);
           $model->setAttribute('alternate_header_content',Yii::$app->request->post("alternate_header_content"));
           $model->setAttribute('show_header_image','1');
           $model->setAttribute('published_by_stat',$published_by_stat);
           $model->setAttribute('usrname',Yii::$app->user->identity->username);
           $model->setAttribute('display_image_id',Yii::$app->request->post("display_image_id"));
           $model->setAttribute('thumbnail_image_id',Yii::$app->request->post("thumbnail_image_id"));
           $model->setAttribute('article_type',Yii::$app->request->post("article_type"));
           $model->setAttribute('feedback',Yii::$app->request->post("feedback"));
           $model->setAttribute('featured_media',Yii::$app->request->post("featured_media"));
           $model->setAttribute('require_login',Yii::$app->request->post("require_login"));
           $model->setAttribute('disable_comments',Yii::$app->request->post("disable_comments"));
           $model->setAttribute('render_template',Yii::$app->request->post("render_template"));
           $model->setAttribute('archive',$archive);
           $model->setAttribute('sort_order',Yii::$app->request->post("sort_order"));
           $model->setAttribute('permissions',$perm_val);
           $model->save();          
           
           
        ArticlesTag::deleteAll(['articles_id'=>$id]);
        $tag_array = Yii::$app->request->post("tag_array");            
        $ev_arr = explode(" ",$tag_array);           
        $counter=0;
                       
        for($i=0; $i < count($ev_arr);$i++):            
               $ic = md5(date('YmdHi').$counter);
               $counter++;
               if(trim($ev_arr[$i])!=""):
                    $c = new ArticlesTag();
                    $c->setAttribute('tags_id',$ev_arr[$i]);
                    $c->setAttribute('articles_id',$id);
                    $c->save();                    
               endif;
        endfor;
        
        $pages = Yii::$app->request->post("category");
           if(!empty($pages)){
               $counter=0;
                // Loop to store and display values of individual checked checkbox.
                foreach($pages as $selected){
                    $insert_id = md5(date("Hims").$counter);
                    $counter++;
                    $c = new ArticlesCategoryRelated();
                    $c->setAttribute('articles_id',$id);
                    $c->setAttribute('category_id',$selected);
                    $c->save(); 
                }
                
            }
            
        $pages = Yii::$app->request->post("blog_index");
           if(!empty($pages)){
               $counter=0;
                // Loop to store and display values of individual checked checkbox.
                foreach($pages as $selected){
                    $insert_id = md5(date("Hims").$counter);
                    $counter++;
                    $c = new ArticlesBlogIndex();
                    $c->setAttribute('articles_id',$id);
                    $c->setAttribute('blog_index_id',$selected);
                    $c->save();     
                }
                
            }
            
            return "Articles successfully created";
        }
    }
    
    
    protected function getSelectedTags(){
        $session = Yii::$app->session;
        $mydatabase = $session['mydatabase'];
        $query = new \yii\db\Query;
        return Yii::$app->db->createCommand("SELECT x.tags_id as id,y.name as name FROM tbl_articles_tag x,tbl_tags y WHERE x.tags_id=y.id AND x.articles_id='".$this->id."'")->queryAll();
    }
     protected function getCategories(){        
        return ArticlesCategoryRelated::find()->where(['articles_id'=>$this->id])->all();
     }
     public function getBlogIndex(){         
        return ArticlesBlogIndex::find()->where(['articles_id'=>$this->id])->all();
     }
     
}
