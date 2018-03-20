<?php

namespace humhub\modules\mailinglists\controllers;

use Yii;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\mailinglists\components\AdminControllerBase;

/**
 *  Mailing-Lists admin controller
 */
class ContainerController extends ContentContainerController
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
     *  Edit a page and go for it
     */
    public function actionEditPage()
    {
        return $this->runEditPage();
    }

    /**
     *  Send email
     */
    public function actionSend()
    {
        return $this->runSend();
    }
}



