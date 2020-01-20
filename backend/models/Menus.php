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
use backend\models\Pages;

class Menus extends \yii\db\ActiveRecord
{
   public static function tableName()
    {
        return 'tbl_page';
    }
    
    public static function getActiveMenus($menu_profile=null){
        $session = Yii::$app->session;
        $mydatabase = $session['mydatabase'];
        $query = new \yii\db\Query;
        if($menu_profile!=null):
                return Yii::$app->db->createCommand("SELECT x.menu_id as id,y.menu_title FROM tbl_menu_page x,tbl_page y WHERE x.menu_id=y.id AND profile ='".$menu_profile."' ORDER BY x.sort_order")->queryAll();       
            else:
                return Yii::$app->db->createCommand("SELECT id,menu_title FROM tbl_page WHERE show_in_menu='1' AND sort_order > 0 ORDER BY sort_order")->queryAll();       
        endif;
        
    }
    public static function getProfileMenus($menu_profile){ 
            
            $subquery = (new \yii\db\Query())->select('menu_id')->from('tbl_menu_page')->where(['profile'=>$menu_profile]);
            
            if (Yii::$app->user->isGuest) {
                if($subquery==null):                        
                        return Pages::find()->where(['require_login'=>'Y'])->orderBy('sort_order')->all();                
                    else:
                        
                        return Pages::find()->where(['in',"id",$subquery])->andWhere('require_login<>"Y"')->orderBy('sort_order')->all();
                endif;        
            }else{
                $username = \Yii::$app->user->identity->username;
                
                $user_rec = Users::find()->where(['username'=>$username])->one();
                $subquery = (new \yii\db\Query())->select('menu_id')->from('tbl_menu_page')->where(['profile'=>$user_rec->role->menu_id]);
                $header_menus = Pages::find()->where(['in',"id",$subquery])->andWhere('hideon_login<>"Y"')->orderBy('sort_order')->all();
                if($header_menus==null):
                    $subquery = (new \yii\db\Query())->select('menu_id')->from('tbl_menu_page')->where(['profile'=>$menu_profile]);
                
                    if($menu_profile==0):                        
                        return Pages::find()->andWhere('hideon_login<>"Y"')->orderBy('sort_order')->all();                
                    else:
                        return Pages::find()->where(['in',"id",$subquery])->andWhere('hideon_login<>"Y"')->orderBy('sort_order')->all();
                    endif;
                else:                    
                    return $header_menus;
                endif;
            }
       
    }
    public static function saveMenus($menu_profile=null){
        $session = Yii::$app->session;
        $mydatabase = $session['mydatabase'];
        $query = new \yii\db\Query;
        $active_menus = Yii::$app->request->post('enabled_menus');
        $inactive_menus = Yii::$app->request->post('disabled_menus');
        $arr_act_menu = explode("\r\n",$active_menus);
        $order_count=0;
        if($menu_profile!=null):
            MenuPage::deleteAll(['profile'=>$menu_profile]);
        endif;
        for($i=0;$i < count($arr_act_menu);$i++){
            echo $arr_act_menu[$i]."-";
            $order_count= $order_count + 10;
            if($menu_profile!=null):
                    $menu_page = Pages::find()->where(['menu_title'=>$arr_act_menu[$i]])->one();
                    if($menu_page!=null):
                        $c = new MenuPage();
                        $c->setAttribute('menu_id',$menu_page['id']);
                        $c->setAttribute('profile',$menu_profile);
                        $c->setAttribute('sort_order',$order_count);
                        $c->save();
                    else:
                        $menu_page = Pages::find()->where(['LIKE','menu_title',$arr_act_menu[$i]])->one();
                        if($menu_page!=null):
                            $c = new MenuPage();
                            $c->setAttribute('menu_id',$menu_page['id']);
                            $c->setAttribute('profile',$menu_profile);
                            $c->setAttribute('sort_order',$order_count);
                            $c->save();
                        endif;
                    endif;
                else:            
                    Yii::$app->db->createCommand()->update('tbl_page',[  
                    'sort_order'=>$order_count,
                    'show_in_menu'=>'1'
                    ],'menu_title="'.$arr_act_menu[$i].'"')->execute();
            endif;
        }
        $arr_inact_menu = explode("\r\n",$inactive_menus);
        $order_count=0;
        for($i=0;$i < count($arr_inact_menu);$i++){
            $order_count+=10;
            if($menu_profile==null):
            Yii::$app->db->createCommand()->update('tbl_page',[  
               'sort_order'=>$order_count,
                'show_in_menu'=>'0'
           ],'menu_title="'.$arr_inact_menu[$i].'"')->execute();
           endif;
        }
        return "Menus updated";
    }
    public static function saveFooterMenus(){
        $session = Yii::$app->session;
        $mydatabase = $session['mydatabase'];
        $query = new \yii\db\Query;
        $active_menus = Yii::$app->request->post('footer_enabled_menus');
        $inactive_menus = Yii::$app->request->post('footer_disabled_menus');
        $arr_act_menu = explode("\r\n",$active_menus);
        $order_count=0;
        for($i=0;$i < count($arr_act_menu);$i++){
            $order_count= $order_count + 10;
            Yii::$app->db->createCommand()->update('tbl_page',[  
               
                'show_in_footer_menu'=>'1'
           ],'menu_title="'.$arr_act_menu[$i].'"')->execute();
        }
        $arr_inact_menu = explode("\r\n",$inactive_menus);
        $order_count=0;
        for($i=0;$i < count($arr_inact_menu);$i++){
            $order_count+=10;
            Yii::$app->db->createCommand()->update('tbl_page',[  
               
                'show_in_footer_menu'=>'0'
           ],'menu_title="'.$arr_inact_menu[$i].'"')->execute();
        }
        return "Menus updated";
    }
    public static function getInActiveMenus($menu_profile=null){
        $session = Yii::$app->session;
        $mydatabase = $session['mydatabase'];
        $query = new \yii\db\Query;
        if($menu_profile!=null):
                return Yii::$app->db->createCommand("SELECT id,menu_title FROM tbl_page WHERE id NOT IN (SELECT menu_id FROM tbl_menu_page WHERE profile='".$menu_profile."') ORDER BY sort_order")->queryAll();
            else:
                return Yii::$app->db->createCommand("SELECT id,menu_title FROM tbl_page WHERE show_in_menu='0' ORDER BY sort_order")->queryAll();
        endif;
        
        
    }
    public static function getFooterActiveMenus(){        
        return Pages::find()->where(['show_in_footer_menu'=>'1'])->all();
        
    }
    public static function getFooterInActiveMenus(){        
        return Pages::find()->where(['show_in_footer_menu'=>'0'])->all();
        
    }
}