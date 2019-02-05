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
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use backend\models\Forms;
use frontend\components\ContentBuilder;
use backend\models\CustomSettings;
use backend\models\Pages;


/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        //manage custom log in if turned on
        $page_id= ContentBuilder::getSetting("backend_home_page");
        if($page_id!="0"):
                    $form_arr = Pages::find()->where(['id'=>$page_id])->one();
                    if($form_arr!=null):
                        $form_id = $form_arr->form_id;
                        $metadata['saveURL'] = \Yii::$app->getUrlManager()->createUrl('ajaxform/save');                        
                        $metadata['param'] = Yii::$app->request->csrfParam;
                        $metadata['token'] = Yii::$app->request->csrfToken;
                        $settings = CustomSettings::find()->all();
                        $form = Forms::find()->where(['id'=>$form_id])->one();
                        if($form!=null):
                            $codebase=\frontend\models\Twig::find()->where(['renderer'=>$form->id])->one();
                        
                            if(($codebase!=null)&& ($codebase['code']<>"")):
                                $loader = new \frontend\models\Twig();
                                $twig = new \Twig_Environment($loader);
                                $content= $twig->render($codebase['filename'], ['form'=>$form,'metadata'=>$metadata,'app'=>Yii::$app,'settings'=>$settings]);
                                return $this->render('@frontend/views/layouts/html',['data'=>$content]);
                            endif;
                        endif;
                    endif;
        endif;
        
        
        
        return $this->render('index');
        
        
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
