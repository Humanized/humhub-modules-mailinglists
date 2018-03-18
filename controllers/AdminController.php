<?php

namespace humhub\modules\mailing_lists\controllers;

use Yii;
use humhub\modules\admin\components\Controller;

use humhub\modules\custom_pages\modules\template\components\TemplateCache;

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
        $content = $this->renderPage($entry);

        $members = Membership::find()->all();
        foreach($members as $member) {
            $url = Url::to(['mailing_lists/member/unsubscribe'], [
                'token' => $token
            ]);
            $body =
                $content . '<br><small>' .
                'Unsubscribe to this mailing-list: <a href="'.$url.'">'.$url.'</a>' .
                '</small>';

            Yii::$app->mailer->compose()
                ->setTo($member->email)
                ->setSubject($page->title)
                ->setHtmlBody($page->entry)
                ;
        }

        $entry->is_sent = true;
        $entry->save();

        return $this->render('@mailing_lists/views/admin/list', [
            'entries' => MailingListEntry::find()->all(),
            'message' => 'Mails have been successfully sent!'
        ]);
    }

    function renderPage($entry)
    {
        \humhub\modules\custom_pages\Module::loadTwig();
        $instance = $entry->instance;
        if(TemplateCache::exists($instance))
            return TemplateCache::get($instance);

        $html = $instance->render(false);
        TemplateCache::set($instance, $html);
        return $html;
    }
}



