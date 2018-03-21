<?php
namespace humhub\modules\mailinglists\components;

use Yii;
use yii\base\Behavior;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

use humhub\modules\user\models\User;
use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\models\ContainerPage;

use humhub\modules\mailinglists\models\EditPageForm;
use humhub\modules\mailinglists\models\MailingListEntry;
use humhub\modules\mailinglists\models\Membership;
use humhub\modules\mailinglists\models\SendSettingsForm;
use humhub\modules\mailinglists\models\Settings;
use humhub\modules\mailinglists\widgets\EditPageModal;
use humhub\modules\mailinglists\widgets\SendSettingsModal;


class AdminControllerBase extends Behavior
{
    public $controller;

    /**
     *  Return controller's space if any or null
     */
    function getSpace() {
        return isset($this->controller->contentContainer) ?
            $this->controller->contentContainer : null;
    }

    /**
     *  Return entry from request' get
     */
    function getEntry($post = false) {
        // FIXME: ensure correspondance between entry id and space's
        $request = Yii::$app->request;
        $entry = intval($request->get('id'));
        return MailingListEntry::findOne($entry);
    }

    /**
     *  Return true if user has the right on the current ML.
     *  By default: global
     */
    public function hasPerms()
    {
        $space = $this->getSpace();
        if($space)
            return $space->isAdmin();

        $user = Yii::$app->user;
        return $user->isAdmin();
    }

    public function getEntries()
    {
        $entries = MailingListEntry::find();
        $space = $this->getSpace();

        if($space) {
            $pages = ContainerPage::find()->contentContainer($space)
                ->select('custom_pages_container_page.id')->column();
            return $entries->where([
                'container_page_id' => $pages
            ]);
        }

        // global
        return $entries->where(['not', ['page_id' => null]])
            ->andWhere(['container_page_id' => null]);
    }


    public function runEntries($message = '')
    {
        if(!$this->hasPerms())
            return "";

        $space = $this->getSpace();
        return $this->controller->render('@mailinglists/views/admin/list', [
            'entries' => $this->entries->orderBy('id DESC')->all(),
            'space' => $space,
            'message' => $message,
        ]);
    }

    public function runSettings()
    {
        if(!$this->hasPerms())
            return "";

        $space = $this->getSpace();
        $model = new Settings(['space' => $space]);
        if($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->controller->view->saved();
        }

        return $this->controller->render('@mailinglists/views/admin/settings', [
            'model' => $model,
            'space' => $space,
        ]);
    }

    public function runAddPage()
    {
        if(!$this->hasPerms())
            return "";

        $request = Yii::$app->request;
        $title = $request->post('title');
        if(!$title)
            return $this->runEntries("You forgot to give a subject to the mail.");

        $space = $this->getSpace();
        $settings = new Settings(['space' => $space]);
        $entry = MailingListEntry::create($space, $settings->mailTemplate, $title);

        if(!$entry) {
            return $this->runEntries(
                'Could not create mail. Did set a mail template in settings?'
            );
        }

        return $this->controller->redirect($space ?
            $space->createUrl('container/edit', ['id' => $entry->id]) :
            Url::to(['admin/edit', 'id' => $entry->id, ])
        );
    }

    /**
     *  Edit a mail
     */
    public function runEdit()
    {
        if(!$this->hasPerms())
            return "";

        $entry = $this->entry;
        if(!$entry)
            return "";

        $page = $entry->page;
        $model = new EditPageForm([
            'entry' => $entry->id,
            'subject' => $page->title,
        ]);

        // send
        if($model->load(Yii::$app->request->post()) && $model->save()) {
            /*$space = $this->space;
            return $this->controller->redirect($space ?
                $space->createUrl('container/', ['id' => $page->id]) :
                Url::to(['admin/', 'id' => $page->id, ])
            );*/
        }

        return $this->controller->render('@mailinglists/views/admin/edit', [
            'model' => $model,
            'entry' => $entry,
            'space' => $this->space,
        ]);
    }



    /**
     *  Return a list of members that can be mails target.
     *  @return [members,subscribers]
     */
    function sendMembers() {
        $space = $this->space;
        if($space) {
            return [ $space->getMemberShipUser()->all(),
                     [] ];
        }
        return [ User::find()->all(),
                 MemberShip::find()->all() ];
    }

    /**
     *  Display send options form (before sending)
     */
    function sendSettings($request, $model) {
        $entry = $this->entry;
        [$members,$subscribers] = $this->sendMembers();

        $model->entry = $entry->id;
        return SendSettingsModal::widget([
            'model' => $model,
            'entry' => $entry,
            'space' => $this->space,
            'members' => $members,
            'subscribers' => $subscribers,
        ]);
    }

    /**
     *  Actually send mails for the given entry
     */
    function sendMails($request, $model) {
        $entry = $this->entry;

        $ids = [$model->members, $model->subscribers];
        $targets = $this->sendMembers();

        // remember: targets is an array of arrays; we need index
        foreach(array_values($targets) as $i => $list) {
            // filter out
            $selected = $ids[$i];
            if(!$selected) {
                $targets[$i] = [];
                continue;
            }

            $list = array_filter(
                $list,
                function($m) use (&$selected)  {
                    return in_array($m->id, $selected);
                }
            );
            $targets[$i] = $list;
        }

        // we merge here so sendMails handle duplicates
        $members = $targets[0] + $targets[1];

        // FIXME: wrong $model->includePage value (should be boolean, no str)
        $count = $entry->sendMails($members, $model->includePage == "1");
        return $this->runEntries($count . ($count > 1 ? ' mails' : ' mail')  . ' have been sent.');
    }


    /**
     *  Send email
     */
    public function runSend()
    {
        if(!$this->hasPerms() || !$this->entry)
            return "";

        $request = Yii::$app->request;
        $model = new SendSettingsForm();

        // send
        if($model->load(Yii::$app->request->post())) {
            if($model->validate())
                return $this->sendMails($request, $model);
            else
                return $this->runEntries("Invalid configuration given");
        }

        // settings
        return $this->sendSettings($request, $model);
    }

}


