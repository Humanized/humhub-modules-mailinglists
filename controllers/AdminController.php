<?php

namespace humhub\modules\mailinglists\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

use humhub\modules\admin\components\Controller;
use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\modules\template\components\TemplateCache;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;

use humhub\modules\mailinglists\models\MailingListEntry;
use humhub\modules\mailinglists\models\Membership;
use humhub\modules\mailinglists\models\SendSettingsForm;
use humhub\modules\mailinglists\models\Settings;
use humhub\modules\mailinglists\widgets\SendSettingsModal;


/**
 *  Mailing-Lists admin controller
 */
class AdminController extends Controller
{
    public function actionIndex()
    {
        return $this->actionEntries();
    }

    /**
     *  Show list of entries to manage
     */
    public function actionEntries()
    {
        return $this->render('list', [
            'entries' => MailingListEntry::find()->orderBy('is_sent')->all(),
        ]);
    }

    /**
     *  Module settings
     */
    public function actionSettings()
    {
        $model = new Settings();

        if($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->saved();
        }

        return $this->render('settings', [
            'model' => $model,
        ]);
    }

    /**
     *  Create a new page and go for it
     */
    public function actionNewPage()
    {
        $user = Yii::$app->user;
        if(!$user->isAdmin())
            return "";

        $request = Yii::$app->request;
        $title = $request->post('title');
        if(!$title)
            return "";

        $page = new Page();
        $page->title = $title;
        $page->icon = 'fa-envelope';
        $page->type = \humhub\modules\custom_pages\components\Container::TYPE_TEMPLATE;
        $page->navigation_class = Page::NAV_CLASS_EMPTY;
        $page->templateId = (new Settings())->globalTemplate;
        $page->save();

        return $this->redirect(Url::to([
            '/custom_pages/view/view',
            'id' => $page->id,
            'editMode' => 1
        ]));
    }



    /**
     *  Display send options form (before sending)
     */
    function sendSettings($request, $model, $entry) {
        $model->entry = $entry->id;
        return SendSettingsModal::widget([
            'model' => $model,
            'entry' => $entry,
        ]);
    }

    /**
     *  Actually send mails for the given entry
     */
    function sendMails($request, $model, $entry) {
        $members = [];
        if($model->toMembers)
            $members = array_merge(
                $members, User::find()->all()
            );
        if($model->toNewsletter)
            $members = array_merge(
                $members, MemberShip::find()->all()
            );

        $members = array_unique($members);

        if($members) {
            $page = $entry->page;
            $content = $this->renderPage($entry);
            $signature = (new Settings())->globalSignature;

            // fuck-it
            $done = [];
            foreach($members as $member) {
                if(in_array($member->email, $done))
                    continue;
                $done[] = $member->email;

                $body = $content;
                if($member instanceof MemberShip)
                    $body = $body . '\n<br>' .
                            $this->renderSignature($signature, $page, $member);

                Yii::$app->mailer->compose()
                    ->setTo($member->email)
                    ->setSubject($page->title)
                    ->setHtmlBody($body)
                    ;
            }

            $entry->is_sent = true;
            $entry->save();
        }

        return $this->render('@mailinglists/views/admin/list', [
            'entries' => MailingListEntry::find()->all(),
            'message' => 'Mails have been successfully sent!'
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
        $model = new SendSettingsForm();

        // send
        if($model->load(Yii::$app->request->post()) && $model->validate()) {
            $entry = MailingListEntry::findOne($model->entry);
            return $this->sendMails($request, $model, $entry);
        }

        // settings
        $entry = MailingListEntry::findOne($request->get('entry'));
        return $this->sendSettings($request, $model, $entry);
    }

    /**
     *  Mapping of items that can be used in signature, such as:
     *  `{{ member.unsubscribe }}`;
     */
    public function signatureMap() {
        return [
            "member.token" => function($p, $m) { return $m->token; },
            "member.unsubscribe" => function($p, $m) {
                return Url::toRoute(['member/unsubscribe', 'token' => $m->token],true);
            },
            "page.url" => function ($p, $m) {
                return Url::toRoute(['/custom_pages/view', 'id' => $p->id], true);
            }
        ];
    }

    function renderSignature($signature, $page, $member) {
        foreach($this->signatureMap() as $key => $mapping) {
            $signature = preg_replace(
                '{{{\s*' . str_replace('.','\\.', $key) . '\s*}}}',
                $mapping($page, $member),
                $signature
            );
        }
        return $signature;
    }

    function renderPage($entry, $editMode = false)
    {
        \humhub\modules\custom_pages\Module::loadTwig();
        $instance = $entry->instance;
        if(!$editMode && TemplateCache::exists($instance))
            return TemplateCache::get($instance);

        $html = $instance->render(false);
        if(!$editMode)
            TemplateCache::set($instance, $html);
        return $html;
    }
}



