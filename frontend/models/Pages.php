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
use Yii;
use common\components\GUIBehavior;
use backend\models\Settings;

class Pages extends \backend\models\Pages
{
    private $fields = array('description','meta_description','title','alternate_header_content','url');
    
    public function behaviors() {        
        return [
                   
            ['class'=>GUIBehavior::className(),                
                'fields'=>$this->fields,
            ],          
            
        ];
    }
    public function rules()
    {
        return [
            [['id', 'url'], 'required'],
            [['id','no_of_views','thumbnail_image_id','display_image_id'],'safe'],
            
        ];
    }
    
	public function getBlocks(){
        return $this->hasMany(\frontend\models\Blocks::className(),['id'=>'block_id'])->viaTable('tbl_block_page',['page_id'=>'id']);
    }	
	public function getSettings(){
		return new Settings();
	}
        public function getParent(){
            return $this->hasOne(Pages::className(),['id'=>'parent_id']);
        }
        public function getChild(){
            return $this->hasMany(Pages::className(),['parent_id'=>'id'])->all();
        }
        public function afterFind(){            
            if($this->published=="N"):
                return false;
            endif;
            
            if($this->require_login=="Y"):
                if(Yii::$app->user->isGuest):
                    //throw new \yii\web\HttpException(404, 'You do not have sufficient rights to view this page. Consult with your administrator.');
                elseif (strpos($this['permissions'],Yii::$app->user->identity->role_id) === false && $this->url==str_replace("/","",Yii::$app->request->url)) :    
                    $this->description="<font color='red'>You do not have access to view this content. Consult the Administrator</font>";                    
                    echo "<center>You do not have permissions to view this page. Consult the Administrator</center>";
                    exit;
                endif;
            endif;
            parent::afterFind();
            
        }
        
}