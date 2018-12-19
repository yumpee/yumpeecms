<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace backend\models;

use Yii;
use backend\models\Widgets;
use backend\models\TemplateWidget;

class Templates extends \yii\db\ActiveRecord
{
   public static function tableName()
    {
        return 'tbl_templates';
    }
    public function getHasContents(){
       if(Yii::$app->request->get("reload")=="true"):
            return $this->hasOne(Twig::className(),['renderer'=>'route'])->andWhere('theme_id="'.Yii::$app->request->get("theme").'"');
        else:
            return $this->hasOne(Twig::className(),['renderer'=>'route'])->andWhere('theme_id="'.\frontend\components\ContentBuilder::getSetting("current_theme").'"');;
        endif;
    }
    public function getParent(){
        return Templates::find()->where(['id'=>$this->parent_id])->one();
    }
    public static function saveTemplates(){
        $records = Templates::find()->where(['id'=>Yii::$app->request->post('id')])->one();
        $rec=1;
        $url="";
            
        if($records!=null){               
            $id = Yii::$app->request->post("id");      
            $records->setAttribute('name',Yii::$app->request->post("name"));
            $records->setAttribute('route',Yii::$app->request->post("route"));
            $records->setAttribute('url',Yii::$app->request->post("url"));
            $records->save();     
            
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
           
            $id = md5(date('Ymdis').rand(1000,10000));
            $c = new Templates();
            $c->setAttribute('id',$id);
            $c->setAttribute('name',Yii::$app->request->post("name"));
            $c->save();
           
          
        }   
        //we now add the widget info here
        
        $widget_list = Widgets::find()->all();
        foreach($widget_list as $rec){
            $checkbox = Yii::$app->request->post("chk".$rec['short_name']);
            if($checkbox=="on"){
                $display = Yii::$app->request->post($rec['short_name']);
                $position = Yii::$app->request->post("pos_".$rec['short_name']);
                if($display==""):
                    return "Please ensure you fill in the display order for selected tags";
                endif;                
                $records = TemplateWidget::find()->where(['page_id'=>$id])->andWhere('widget="'.$rec['short_name'].'"')->one();
                if($records==null):
                    $records = new TemplateWidget();
                    $records->setAttribute('page_id',$id);
                    $records->setAttribute('widget',$rec['short_name']);
                    $records->setAttribute('display_order',$display);
                    $records->setAttribute('position',$position);
                    $records->save();
                else:
                    $records->setAttribute('page_id',$id);
                    $records->setAttribute('widget',$rec['short_name']);
                    $records->setAttribute('display_order',$display);
                    $records->setAttribute('position',$position);
                    $records->save();
                endif;
            }else{                
                TemplateWidget::deleteAll(['page_id'=>$id,'widget'=>$rec['short_name']]);
            }
        }
            return "Templates update successful";
        
    }
    public static function getTemplates(){        
        return Templates::find()->where(['internal_route_stat'=>'N'])->orderBy('name')->all();
    }
    public static function getMyWidgets($id,$position=""){
        $query = TemplateWidget::find()->where(['page_id'=>$id]);
        if($position!=""):            
            $query->andWhere('position="'.$position.'"');
        else:
            //$query->andWhere('position<>"bottom"')->andWhere('position<>"side"');
        endif;
        return $query->orderBy("display_order")->all();
        
        
    }

}
