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
        $entry = intval($request->get('entry'));
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


    public function runEntries()
    {
        if(!$this->hasPerms())
            return "";

        $space = $this->getSpace();
        return $this->controller->render('@mailinglists/views/admin/list', [
            'entries' => $this->getEntries()->all(),
            'space' => $space,
        ]);
    }

    public function runSettings()
    {
        if(!$this->hasPerms())
            return "";

        $model = new Settings();
        if($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->controller->view->saved();
        }

        $space = $this->getSpace();
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
            return "";

        $space = $this->getSpace();
        $page = null;
        if($space) {
            $page = new ContainerPage();
            $page->content->container = $space;
        }
        else {
            $page = new Page();
            $page->navigation_class = Page::NAV_CLASS_EMPTY;
        }

        $page->title = $title;
        $page->icon = 'fa-envelope';
        $page->type = \humhub\modules\custom_pages\components\Container::TYPE_MARKDOWN;
        $page->save();

        $entry = MailingListEntry::fromPage($page);
        $entry->is_sent = false;
        $entry->save();

        // FIXME: container page
        return $this->controller->redirect($space ?
            $space->createUrl('/custom_pages/container/edit', ['id' => $page->id]) :
            Url::to(['/custom_pages/admin/edit', 'id' => $page->id, ])
        );
    }

    /**
     *  Page editor into a modal window
     */
    public function runEditPage()
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
            'content' => $page->pageContent,
        ]);

        // send
        if($model->load(Yii::$app->request->post()) && $model->save()) {
            $space = $this->space;
            return $this->controller->redirect($space ?
                $space->createUrl('container/', ['id' => $page->id]) :
                Url::to(['admin/', 'id' => $page->id, ])
            );
        }

        return EditPageModal::widget([
            'model' => $model,
            'entry' => $entry,
            'space' => $this->getSpace(),
        ]);
    }


    /**
     *  Display send options form (before sending)
     */
    function sendSettings($request, $model, $entry) {
        $space = $this->getSpace();
        $model->entry = $entry->id;
        return SendSettingsModal::widget([
            'model' => $model,
            'entry' => $entry,
            'space' => $space,
        ]);
    }

    /**
     *  Actually send mails for the given entry
     */
    function sendMails($request, $model, $entry) {
        $space = $this->getSpace();
        $members = [];

        if($space) {
            $members = $space->getMemberShipUser()->all();
        }
        else {
            if($model->toMembers)
                $members = array_merge(
                    $members, User::find()->all()
                );
            if($model->toNewsletter)
                $members = array_merge(
                    $members, MemberShip::find()->all()
                );
        }

        $entry->sendMails($space, $members, $model->includePage);

        return $this->controller->render('@mailinglists/views/admin/list', [
            'space' => $space,
            'entries' => $this->getEntries()->all(),
            'message' => 'Mails have been successfully sent!'
        ]);
    }


    /**
     *  Send email
     */
    public function runSend()
    {
        if(!$this->hasPerms())
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

}


