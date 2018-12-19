<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\models;
use Yii;
use frontend\components\ContentBuilder;
use backend\models\PageTags;
use backend\models\PageTagIndex;
use backend\models\MenuPage;
class Pages extends \yii\db\ActiveRecord
{
   public $image = '';
   public static function tableName()
    {
        return 'tbl_page';
    }
    public function getRoles(){
        return $this->hasOne(Roles::className(),['id'=>'role_id']);
    }
    public function getDisplayImage(){
        return $this->hasOne(Media::className(),['id'=>'display_image_id']);
    }
    public function getIsTopMenu(){
        $record_count = Pages::find()->where(['id'=>$this->parent_id])->count();
        if($record_count > 0):
            return true;
        else:
            return false;
        endif;
        return $return;
    }
    public function getForms(){
        return $this->hasOne(Forms::className(),['id'=>$this->form_id]);
    }
    public function getTopMenus(){
        if($this->id!=null):
            return $this->find()->where(['show_in_menu'=>'1','parent_id'=>$this->id])->orderBy('sort_order')->all();
        else:
            return $this->find()->where(['show_in_menu'=>'1'])->orderBy('sort_order')->all();
        endif;
    }
    public function getPagePath(){
        return ContentBuilder::getActionURL(Yii::$app->request->getAbsoluteUrl());
    }
    public function getHasContents(){
        if(Yii::$app->request->get("reload")=="true"):
            return $this->hasOne(Twig::className(),['renderer'=>'id'])->andWhere('theme_id="'.Yii::$app->request->get("theme").'"');
        else:
            return $this->hasOne(Twig::className(),['renderer'=>'id'])->andWhere('theme_id="'.\frontend\components\ContentBuilder::getSetting("current_theme").'"');;
        endif;
    }
    public static function savePages($save_as_new=""){
        $session = Yii::$app->session;
        $mydatabase = $session['mydatabase'];
        $query = new \yii\db\Query;
        
        $records = Pages::find()->where(['id'=>Yii::$app->request->post('id')])->one();
        $rec=1;
        
        if($records!=null && $save_as_new==""){               
            $id = Yii::$app->request->post("id");  
            $records->setAttribute('title',Yii::$app->request->post("title"));
            $records->setAttribute('menu_title',Yii::$app->request->post("menu_title"));
            $records->setAttribute('breadcrumb_title',Yii::$app->request->post("breadcrumb_title"));
            $records->setAttribute('url',Yii::$app->request->post("url"));
            $records->setAttribute('description',str_replace("../../uploads",Yii::getAlias("@image_dir"),Yii::$app->request->post("description")));
            $records->setAttribute('robots',Yii::$app->request->post("robots"));
            $records->setAttribute('template',Yii::$app->request->post("template"));
            $records->setAttribute('layout',Yii::$app->request->post("layout"));
            $records->setAttribute('show_in_footer_menu',"0");
            $records->setAttribute('sort_order',Yii::$app->request->post("sort_order"));
            $records->setAttribute('master_content','1');
            $records->setAttribute('sort_order_footer','0');
            $records->setAttribute('show_header_image','1');
            $records->setAttribute('sidebar',Yii::$app->request->post("sidebar"));
            $records->setAttribute('updated',date("Y-m-d H:i:s"));
            $records->setAttribute('tab_menu_title',Yii::$app->request->post("tab_menu_title"));
            $records->setAttribute('editable',Yii::$app->request->post("editable"));
            $records->setAttribute('tag_id',Yii::$app->request->post("tag_id"));
            $records->setAttribute('meta_description',Yii::$app->request->post("meta_description"));
            $records->setAttribute('parent_id',Yii::$app->request->post("parent_id"));
            $records->setAttribute('display_image_id',Yii::$app->request->post("display_image_id"));
            $records->setAttribute('css',Yii::$app->request->post("css"));
            $records->setAttribute('menu_profile',Yii::$app->request->post("menu_profile"));
            $records->setAttribute('require_login',Yii::$app->request->post("require_login"));
            $records->setAttribute('hideon_login',Yii::$app->request->post("hideon_login"));
            $records->setAttribute('form_id',Yii::$app->request->post("forms"));
            $records->setAttribute('role_id',Yii::$app->request->post("roles"));
            $records->setAttribute('renderer',Yii::$app->request->post("renderer"));
            $records->save(false);
        
            PageTags::deleteAll(['page_id'=>$id]);
        $tag_array = Yii::$app->request->post("tag_array");            
        $ev_arr = explode(" ",$tag_array);           
        $counter=0;
                       
        for($i=0; $i < count($ev_arr);$i++):            
               $ic = md5(date('YmdHi').$counter);
               $counter++;
               if(trim($ev_arr[$i])!=""):                    
                    $c = new PageTags();
                    $c->setAttribute('tags_id',$ev_arr[$i]);
                    $c->setAttribute('page_id',$id);
                    $c->save();
               endif;
        endfor;
        PageTagIndex::deleteAll(['page_id'=>$id]);
        
        $index_tags = Yii::$app->request->post("index_tags");
           if(!empty($index_tags)){
               $counter=0;
                // Loop to store and display values of individual checked checkbox.
                foreach($index_tags as $selected){
                    $counter++;                    
                    $c = new PageTagIndex();
                    $c->setAttribute('index_tag_id',$selected);
                    $c->setAttribute('page_id',$id);
                    $c->save();
                }
                
            }
            
        
        }else{ 
            
          $id = md5(date('Ymdis').rand(1000,100000));
            if(Yii::$app->request->post("url")==""):
                $url=strtolower(str_replace(" ","-",Yii::$app->request->post("title")));
            else:
                $url=strtolower(str_replace(" ","-",Yii::$app->request->post("url")));
            endif;
            if($save_as_new!=""){
                $url=$url.substr(md5(date('si')),2,5);
            }
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
            $records = new Pages();  
            $records->setAttribute('id',$id);
            $records->setAttribute('title',Yii::$app->request->post("title"));
            $records->setAttribute('menu_title',Yii::$app->request->post("menu_title"));
            $records->setAttribute('breadcrumb_title',Yii::$app->request->post("breadcrumb_title"));
            $records->setAttribute('url',$url);
            $records->setAttribute('description',str_replace("../../uploads",Yii::getAlias("@image_dir"),Yii::$app->request->post("description")));
            $records->setAttribute('robots',Yii::$app->request->post("robots"));
            $records->setAttribute('template',Yii::$app->request->post("template"));
            $records->setAttribute('layout',Yii::$app->request->post("layout"));
            $records->setAttribute('published','1');
            $records->setAttribute('show_in_footer_menu','0');
            $records->setAttribute('sort_order','0');
            $records->setAttribute('master_content','1');
            $records->setAttribute('sort_order_footer','0');
            $records->setAttribute('show_header_image','1');
            $records->setAttribute('sidebar',Yii::$app->request->post("sidebar"));
            $records->setAttribute('updated',date("Y-m-d H:i:s"));
            $records->setAttribute('tab_menu_title',Yii::$app->request->post("tab_menu_title"));
            $records->setAttribute('editable',Yii::$app->request->post("editable"));
            $records->setAttribute('tag_id',Yii::$app->request->post("tag_id"));
            $records->setAttribute('meta_description',Yii::$app->request->post("meta_description"));
            $records->setAttribute('parent_id',Yii::$app->request->post("parent_id"));
            $records->setAttribute('display_image_id',Yii::$app->request->post("display_image_id"));
            $records->setAttribute('css',Yii::$app->request->post("css"));
            $records->setAttribute('menu_profile',Yii::$app->request->post("menu_profile"));
            $records->setAttribute('require_login',Yii::$app->request->post("require_login"));
            $records->setAttribute('hideon_login',Yii::$app->request->post("hideon_login"));
            $records->setAttribute('form_id',Yii::$app->request->post("forms"));
            $records->setAttribute('role_id',Yii::$app->request->post("roles"));
            $records->setAttribute('renderer',Yii::$app->request->post("renderer"));
            $records->save();
           
           
           
        PageTags::deleteAll(['page_id'=>$id]);
        $tag_array = Yii::$app->request->post("tag_array");            
        $ev_arr = explode(" ",$tag_array);           
        $counter=0;
                       
        for($i=0; $i < count($ev_arr);$i++):            
               $ic = md5(date('YmdHi').$counter);
               $counter++;
               if(trim($ev_arr[$i])!=""):
                    $c = new PageTags();
                    $c->setAttribute('tags_id',$ev_arr[$i]);
                    $c->setAttribute('page_id',$id);
                    $c->save();
               endif;
        endfor;
        }
        
        PageTagIndex::deleteAll(['page_id'=>$id]);
        $index_tags = Yii::$app->request->post("index_tags");
           if(!empty($index_tags)){
               $counter=0;
                // Loop to store and display values of individual checked checkbox.
                foreach($index_tags as $selected){
                    $counter++;
                    $c = new PageTagIndex();
                    $c->setAttribute('index_tag_id',$selected);
                    $c->setAttribute('page_id',$id);
                    $c->save();;
                }
                
            }
        
        return "Update successful";
    }
    public static function getMyPages(){
        $session = Yii::$app->session;
        $mydatabase = $session['mydatabase'];
        $query = new \yii\db\Query;
        return Yii::$app->db->createCommand("SELECT  tbl_page.template,tbl_page.display_image_id,tbl_page.title,tbl_page.menu_title,tbl_page.url,tbl_page.description,tbl_page.published,tbl_page.show_in_menu,tbl_page.master_content,tbl_page.sort_order,tbl_page.id,tbl_tags.name as tag,tbl_page.parent_id from tbl_page left join tbl_tags ON tbl_page.tag_id = tbl_tags.ID")->queryAll();
    }
    
    public static function getImageList(){
        $id = Yii::$app->request->get("id"); 
        $session = Yii::$app->session;
        $mydatabase = $session['mydatabase'];
        $query = new \yii\db\Query;
        return Yii::$app->db->createCommand("SELECT id,image_id FROM tbl_event_image WHERE event_id='".$id."'")->queryAll();
     }
     public static function saveImage($image_id,$event_id,$myalt){
        $id = md5(date('Ymdis'));
        $session = Yii::$app->session;
        $mydatabase = $session['mydatabase'];
        $query = new \yii\db\Query;
        
        Yii::$app->db->createCommand()->insert('tbl_image',[
               'id'=>$image_id,
               'master_content'=>'1',
               'alt'=>$myalt
           ])->execute();
         
     }
     
     public static function getSelectedTags(){
        $id = Yii::$app->request->get("id");
        $session = Yii::$app->session;
        $mydatabase = $session['mydatabase'];
        $query = new \yii\db\Query;
        return Yii::$app->db->createCommand("SELECT x.tags_id as id,y.name as name FROM tbl_page_tags x,tbl_tags y WHERE x.tags_id=y.id AND x.page_id='$id'")->queryAll();
    }
    public static function getIndexTags($id){
        return PageTagIndex::find()->where(['page_id'=>$id])->all();
    }
    public static function getBlogIndex(){
        $query = new \yii\db\Query;
        $subquery = new \yii\db\Query;
        $line_subquery = $subquery->select('id')->from('tbl_templates')->where(['route'=>'blog/index']);
        return $query->select('id,title')->from('tbl_page')->where(['template'=>$line_subquery])->all();
        
    }
    public static function getcategoryIndex(){
        $query = new \yii\db\Query;
        $subquery = new \yii\db\Query;
        $line_subquery = $subquery->select('id')->from('tbl_templates')->where(['route'=>'blog/category']);
        return $query->select('id,title')->from('tbl_page')->where(['template'=>$line_subquery])->all();
        
    }
    public function getBlocks(){
        return $this->hasMany(Blocks::className(),['id'=>'block_id'])->viaTable('tbl_block_page',['page_id'=>'id']);
    }
    public static function deleteTrace(){
        $id=Yii::$app->request->get("id");
        PageTags::deleteAll(['page_id'=>$id]);
        PageTagIndex::deleteAll(['page_id'=>$id]);
        MenuPage::deleteAll(['menu_id'=>$id]);
        
    }
}