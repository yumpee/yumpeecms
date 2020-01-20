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

namespace frontend\models;

/**
 * Description of Articles
 *
 * @author Peter
 */
use Yii;
use frontend\components\ContentBuilder;
use common\components\GUIBehavior;
use frontend\models\ArticleFiles;

class Articles  extends \backend\models\Articles{
    //put your code here
  private $fields = array('body_content','lead_content');
    public function behaviors() {
        
        return [
                   
            ['class'=>GUIBehavior::className(),                
                'fields'=>$this->fields,
            ],          
            
        ];
    }  
public function getFeedback(){
    return $this->hasMany(Feedback::className(),['target_id'=>'id'])->where(['feedback_type'=>'articles']);
}
public function getDetails(){
        return $this->hasMany(ArticleDetails::className(),['article_id'=>'id']);
}
public function getFile(){
        return $this->hasMany(ArticleFiles::className(),['article_id'=>'id']);
    }
public function getFormattedIndexURL(){
      return Yii::$app->request->getBaseUrl()."/".ContentBuilder::getURLByRoute("blog/index");
  }
public static function saveArticle($save_as_new=""){
        $session = Yii::$app->session;
        $records = Articles::find()->where(['id'=>Yii::$app->request->post('id')])->andWhere('usrname="'.Yii::$app->user->identity->username.'"')->one();
        if($records==null):
            return 0; //if no records are found
        endif;
        $rec=1;
        $published=0;
        if(ContentBuilder::getSetting("auto_approve_post")=="on"){
            $published='1';
        }else{
            $published='0';
        }
        if(Yii::$app->request->post("published_by_stat")=="1"){
            $published_by_stat='1';
        }else{
            $published_by_stat='0';
        }
        
        if(count($records)>0 && $save_as_new==""){  
        
            $id = Yii::$app->request->post("id");            
            Yii::$app->db->createCommand()->update('tbl_articles',[  
               'url'=>Yii::$app->request->post("url"),
               'title'=>Yii::$app->request->post("title"),
               'lead_content'=>str_replace("../../uploads",Yii::getAlias("@image_dir"),Yii::$app->request->post("lead_content")),
               'body_content'=>str_replace("../../uploads",Yii::getAlias("@image_dir"),Yii::$app->request->post("body_content")),               
               'master_content'=>'1',
               'published'=>$published,
               'updated'=>date("Y-m-d H:i:s"),
               'date'=>Yii::$app->request->post("date"),
               'show_header_image'=>'1',
               'published_by_stat'=>$published_by_stat,
               'usrname'=>Yii::$app->user->identity->username,
               'display_image_id'=>Yii::$app->request->post("display_image_id"),
               'article_type'=>Yii::$app->request->post("article_type"),
               'featured_media'=>Yii::$app->request->post("featured_media"),
               'thumbnail_image_id'=>Yii::$app->request->post("thumbnail_image_id"),
               'require_login'=>Yii::$app->request->post("require_login")
                
           ],'id="'.$id.'"')->execute();
           //we make updates to the Articles information
            $a = ArticleDetails::deleteAll(['article_id'=>$id]);                       
            foreach($_POST as $key => $value){
                                if($value<>""):
                                    $profile_data = new ArticleDetails();
                                    $profile_data->setAttribute("article_id",$id);
                                    $profile_data->setAttribute("param",$key);
                                    $profile_data->setAttribute("param_val",$value);
                                    $profile_data->save();
                                endif;
            }
            
           Yii::$app->db->createCommand()->delete('tbl_articles_category_related','articles_id="'.$id.'"')->execute();
           
           $pages = Yii::$app->request->post("category");
           
           //add the page relation to blocks
           if(!empty($pages)){
               $counter=0;
                // Loop to store and display values of individual checked checkbox.
                foreach($pages as $selected){
                    $insert_id = md5(date("Hims").$counter);
                    $counter++;
                Yii::$app->db->createCommand()->insert('tbl_articles_category_related',['articles_id'=>$id,'category_id'=>$selected])->execute();
                
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
                Yii::$app->db->createCommand()->insert('tbl_articles_blog_index',['articles_id'=>$id,'blog_index_id'=>$selected])->execute();
                
                }
                
            }
           
        Yii::$app->db->createCommand()->delete('tbl_articles_tag','articles_id="'.$id.'"')->execute();
        $tag_array = Yii::$app->request->post("tag_array");            
        $ev_arr = explode(" ",$tag_array);           
        $counter=0;
                       
        for($i=0; $i < count($ev_arr);$i++):            
               $ic = md5(date('YmdHi').$counter);
               $counter++;
               if(trim($ev_arr[$i])!=""):
                    Yii::$app->db->createCommand()->insert('tbl_articles_tag',['tags_id'=>$ev_arr[$i],'articles_id'=>$id])->execute();
               endif;
        endfor;
        
            return $id;
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
            $url = $url.md5($url);
            
           $archive = date("mY");
           Yii::$app->db->createCommand()->insert('tbl_articles',[
               'id'=>$id,
               'url'=>$url,
               'title'=>Yii::$app->request->post("title"),
               'lead_content'=>str_replace("../../uploads",Yii::getAlias("@image_dir"),Yii::$app->request->post("lead_content")),
               'body_content'=>str_replace("../../uploads",Yii::getAlias("@image_dir"),Yii::$app->request->post("body_content")),
               'date'=>date("Y-m-d"),  
               'master_content'=>'1',
               'published'=>$published,
               'alternate_header_content'=>Yii::$app->request->post("alternate_header_content"),
               'show_header_image'=>'1',
               'published_by_stat'=>$published_by_stat,
               'usrname'=>Yii::$app->user->identity->username,
               'display_image_id'=>Yii::$app->request->post("display_image_id"), 
               'thumbnail_image_id'=>Yii::$app->request->post("thumbnail_image_id"),
               'article_type'=>Yii::$app->request->post("article_type"),
               'featured_media'=>Yii::$app->request->post("featured_media"),
               'require_login'=>Yii::$app->request->post("require_login"),
               'archive'=>$archive
               
           ])->execute();
           
        //we make updates to the Articles information
        $a = ArticleDetails::deleteAll(['article_id'=>$id]);                       
            foreach($_POST as $key => $value){
                                if($value<>""):
                                    $profile_data = new ArticleDetails();
                                    $profile_data->setAttribute("article_id",$id);
                                    $profile_data->setAttribute("param",$key);
                                    $profile_data->setAttribute("param_val",$value);
                                    $profile_data->save();
                                endif;
            }
           
        Yii::$app->db->createCommand()->delete('tbl_articles_tag','articles_id="'.$id.'"')->execute();
        $tag_array = Yii::$app->request->post("tag_array");            
        $ev_arr = explode(" ",$tag_array);           
        $counter=0;
                       
        for($i=0; $i < count($ev_arr);$i++):            
               $ic = md5(date('YmdHi').$counter);
               $counter++;
               if(trim($ev_arr[$i])!=""):
                    Yii::$app->db->createCommand()->insert('tbl_articles_tag',['tags_id'=>$ev_arr[$i],'articles_id'=>$id])->execute();
               endif;
        endfor;
        
        $pages = Yii::$app->request->post("category");
           if(!empty($pages)){
               $counter=0;
                // Loop to store and display values of individual checked checkbox.
                foreach($pages as $selected){
                    $insert_id = md5(date("Hims").$counter);
                    $counter++;
                Yii::$app->db->createCommand()->insert('tbl_articles_category_related',['articles_id'=>$id,'category_id'=>$selected])->execute();
                
                }
                
            }
            
        $pages = Yii::$app->request->post("blog_index");
           if(!empty($pages)){
               $counter=0;
                // Loop to store and display values of individual checked checkbox.
                foreach($pages as $selected){
                    $insert_id = md5(date("Hims").$counter);
                    $counter++;
                Yii::$app->db->createCommand()->insert('tbl_articles_blog_index',['articles_id'=>$id,'blog_index_id'=>$selected])->execute();
                
                }
                
            }   
        
            
            return $id;
        }

    }
    public function getRelationData() {
        $ARMethods = get_class_methods('\yii\db\ActiveRecord');
        $modelMethods = get_class_methods('\yii\base\Model');
        $reflection = new \ReflectionClass($this);
        $i = 0;
        $stack = [];
        /* @var $method \ReflectionMethod */
        foreach ($reflection->getMethods() as $method) {
            if (in_array($method->name, $ARMethods) || in_array($method->name, $modelMethods)) {
                continue;
            }
            if($method->name === 'bindModels')  {continue;}
            if($method->name === 'attachBehaviorInternal')  {continue;}
            if($method->name === 'getRelationData')  {continue;}
            if($method->name ==='resetDependentRelations') {continue;}
            if($method->name ==='setRelationDependencies') {continue;}
            try {
                $rel = call_user_func(array($this,$method->name));
                if($rel instanceof \yii\db\ActiveQuery){
                    $stack[$i]['name'] = lcfirst(str_replace('get', '', $method->name));
                    $stack[$i]['method'] = $method->name;
                    $stack[$i]['ismultiple'] = $rel->multiple;
                    $i++;
                }
            } catch (\yii\base\ErrorException $exc) {
//                
            }
        }
        return $stack;
    }
    public function rules()
    {
        return [
            [['id', 'url','title','lead_content','body_content','date','published','show_header_image','usrname'], 'required'],
            [['thumbnail','display_image_id'],'max' => 100],
            [['id','no_of_views'],'safe'],
            [['description'],'string'],
        ];
    }
    public function afterFind(){
        if($this->require_login=="Y"):
                if(Yii::$app->user->isGuest):
                    
                elseif (strpos($this->permissions,Yii::$app->user->identity->role_id) === false) :
                    $this->body_content="<font color='red'>You do not have access to view this content. Consult the Administrator</font>";   
                endif;
        endif;
        parent::afterFind();
    }
}
