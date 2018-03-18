<?php

namespace humhub\modules\mailing_lists\controllers;

use Yii;
use humhub\modules\admin\components\Controller;

use humhub\modules\custom_pages\modules\template\widgets\TemplatePage;

use humhub\modules\mailing_lists\models\MailingListEntry;
use humhub\modules\mailing_lists\models\Membership;


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


    /**
     *  Send email
     */
    public function actionSend()
    {
        $user = Yii::$app->user;
        if(!$user->isAdmin())
            return "";

        $request = Yii::$app->request;
        $entry = MailingListEntry::findOne($request->post('entry'));
        if(!$entry)
            return "";

        $page = $entry->page;
        $content = TemplatePage::widget(['page' => $page, 'canEdit' => false, 'editMode' => false ]);
        return $page->title . "\n<br>" . $content;

        $members = Membership::findAll()->all();
        foreach($members as $member) {
            Yii::$app->mailer->compose()
                ->setTo($member->email)
                ->setSubject($page->title)
                ->setHtmlBody($page->entry)
                ;
        }
    }
}



