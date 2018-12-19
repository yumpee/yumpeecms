<?php

namespace app\components;

use yii\web\UrlRuleInterface;
use yii\base\Object;    
use frontend\components\ContentBuilder;


class Resource extends Object implements URLRuleInterface

{
	public function createUrl($manager, $route, $params){
                        return false;
	}
		
	public function parseRequest($manager, $request){
                        $page=[];
                        
			return ["api/index",$page];
                                
			
        }
        public function resolve(){
            
            
        }
}