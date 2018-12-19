<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
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
