<?php
namespace app\controllers;
/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\rest\ActiveController;

class ResourceController extends ActiveController{
    
    public function actionIndex(){
        return "Error";
    }
}