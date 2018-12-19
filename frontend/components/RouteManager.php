<?php
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


