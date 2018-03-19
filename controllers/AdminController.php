<?php

namespace humhub\modules\mailinglists\controllers;

use Yii;
use humhub\modules\admin\components\Controller;
use humhub\modules\mailinglists\components\AdminControllerBase;


/**
 *  Mailing-Lists admin controller
 */
class AdminController extends Controller
{
    public function behaviors()
    {
        return [
            [
                'class' => AdminControllerBase::className(),
                'controller' => $this,
            ]
        ];
    }

    public function actionIndex()
    {
        return $this->runEntries();
    }

    /**
     *  Show list of entries to manage
     */
    public function actionEntries()
    {
        return $this->runEntries();
    }

    /**
     *  Module settings
     */
    public function actionSettings()
    {
        return $this->runSettings();
    }

    /**
     *  Create a new page and go for it
     */
    public function actionAddPage()
    {
        return $this->runAddPage();
    }

    /**
     *  Page editor into a modal window
     */
    /*public function actionPageEditModal()
    {
    }*/

    /**
     *  Send email
     */
    public function actionSend()
    {
        return $this->runSend();
    }
}



