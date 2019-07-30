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
namespace app\components;
use yii\web\UrlRuleInterface;
use yii\base\Object;    
use frontend\components\ContentBuilder;


class RouteManager extends Object implements URLRuleInterface

{
	public function createUrl($manager, $route, $params){
                        return false;
	}
		
	public function parseRequest($manager, $request){                        
			$pathInfo = $request->getPathInfo();                        
                        $params=[];  
                        $page=[];
			
                                if($pathInfo==""){
                                    //if there is not path then send to the site index
                                    return false;                                    
                                }else{
                                    list($pathInfo)= explode("/",$pathInfo);
                                    //echo ContentBuilder::getTemplateRouteByURL($pathInfo);
                                    //exit;
                                    if(ContentBuilder::getSetting("maintenance_mode")=="Yes"):
                                            $page['page_id'] = ContentBuilder::getSetting("maintenance_page");                                            
                                            return ["standard/index",$page];
                                            exit;
                                    endif;
                                    return [ContentBuilder::getTemplateRouteByURL($pathInfo),$page];
                                }
				
                                
			
        }
        public function resolve(){
            
            
        }
        
        
	
	
	
	
	
	
	
}


