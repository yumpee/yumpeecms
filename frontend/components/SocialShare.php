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

namespace frontend\components;
use frontend\components\ContentBuilder;

class SocialShare extends \yii\base\Widget
{
    /**
	 * @var string box alignment - horizontal, vertical
	 */
	public $style;
	
	
	/**
	 * @var 
	 */
	public $data_via;


	/**
	 * @var array available social media share buttons 
	 * like - facebook, googleplus, linkedin, twitter
	 */
	
	public $networks = ['facebook','googleplus','linkedin','twitter'];


	/**
	 * The extension initialisation
	 *
	 * @return nothing
	 */
     
    public function init(){
		parent::init();      
	}
     
     
    public function run()
    {            
        $rendered = '';
		foreach($this->networks as $params):
			$rendered .= $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/social/'.$params, ['style' => $this->style, 'data_via' => $this->data_via]);
        endforeach;
        
		return $this->render('@frontend/themes/'.ContentBuilder::getThemeFolder().'/views/social/sharebutton', ['rendered'=>$rendered]);
    }
}
