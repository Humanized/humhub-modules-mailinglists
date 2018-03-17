<?php

namespace humhub\modules\mailing_lists\controllers;

use humhub\modules\admin\components\Controller;
use humhub\modules\mailing_lists\models\forms\SettingsForm;
use humhub\modules\mailing_lists\widgets\AdminMenu;

use Yii;

use humhub\modules\mailing_lists\models\MailingListEntry;


/**
 *  Mailing-Lists admin controller
 */
class AdminController extends Controller
{
    public function actionIndex()
    {
        return $this->actionEntries();
    }

    public function actionSettings()
    {
        $model = new SettingsForm();

        if($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->saved();
        }

        return $this->render('settings', [
            'model' => $model,
            'subNav' => AdminMenu::widget()
        ]);
    }

    /**
     *  Show list of entries to manage
     */
    public function actionEntries()
    {
        return $this->render('@mailing_lists/views/admin/list', [
            'entries' => MailingListEntry::find()->all(),
        ]);
    }
}



