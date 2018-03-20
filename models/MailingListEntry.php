<?php

namespace humhub\modules\mailinglists\models;

use DateTime;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Url;

use humhub\modules\user\models\User;
use humhub\widgets\MarkdownView;

use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\models\ContainerPage;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;

use humhub\modules\mailinglists\models\Settings;

/**
 * This is the model class for table "mailing_list_entry".
 *
 * @property integer $id
 * @property integer $page_id
 * @property integer $container_page_id
 * @property bool $is_sent
 */
class MailingListEntry extends ActiveRecord
{
    /**
     *  Create a new page and an entry, then return them.
     *  @return [MailingListEntry, Page]
     */
    public static function create($space, $templateId, $title)
    {
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
        $page->type = \humhub\modules\custom_pages\components\Container::TYPE_TEMPLATE;
        $page->templateId = $templateId;
        $page->save();
        if(!$page->id)
            return null;

        $entry = MailingListEntry::fromPage($page);
        $entry->save();
        return $entry;
    }


    /**
     *  Create an entry for the given page.
     *  @return MailingListEntry
     */
    public static function fromPage($page)
    {
        $entry = new MailingListEntry();
        if($page instanceof ContainerPage)
            $entry->container_page_id = $page->id;
        else
            $entry->page_id = $page->id;
        // pre-cache
        $entry->_page = $page;
        return $entry;
    }


    private $_page = null;

    /**
     *  Return the instance of page related to this entry. Value is cached.
     *  @return ContainerPage or Page
     */
    public function getPage()
    {
        if(!$this->_page) {
            if($this->container_page_id)
                $this->_page = $this->hasOne(
                    ContainerPage::className(), ['id' => 'container_page_id'])->one();
            else
                $this->_page = $this->hasOne(
                    Page::className(), ['id' => 'page_id'])->one();
        }
        return $this->_page;
    }


    private $_instance;

    /**
     *  Return TemplateInstance related to this entry
     */
    public function getInstance()
    {
        if(!$this->_instance) {
            \humhub\modules\custom_pages\Module::loadTwig();
            $this->_instance = TemplateInstance::findOne([
                'object_model' => $this->page->className(),
                'object_id' => $this->page->id,
            ]);
        }
        return $this->_instance;
    }


    private $_pageContent;


    /**
     *  Return rendered page content
     */
    public function getPageContent($edit = false)
    {
        $page = $this->page;
        if($edit)
            return $this->instance->render(true);

        if($this->_pageContent)
            return $this>_pageContent;
        $this->_pageContent = $this->instance->render(false);
        return $this->_pageContent;
    }

    /**
     *  Mapping of values that can be used in mail content, such as:
     *  `{{ member.unsubscribe }}`;
     */
    public static function valuesMap() {
        return [
            "member.token" => function($p, $m) {
                if($m instanceof Membership)
                    return $m->token;
                return "";
            },
            "member.unsubscribe" => function($p, $m) {
                if($m instanceof Membership)
                    return Url::toRoute(['member/unsubscribe', 'token' => $m->token],true);
                return "";
            },
            "page.url" => function ($p, $m) {
                return Url::toRoute(['/custom_pages/view', 'id' => $p->id], true);
            }
        ];
    }

    /**
     *  Render given content as email content: handles values mapped &
     *  markdown.
     */
    function mapContent($content, $member) {
        $page = $this->page;
        foreach(MailingListEntry::valuesMap() as $key => $map) {
            $content = preg_replace(
                '{{{\s*' . str_replace('.','\\.', $key) . '\s*}}}',
                $map($page, $member),
                $content
            );
        }
        return MarkdownView::widget(['markdown' => $content]);
    }

    /**
     *  Render a mail for the given member. When no member is
     *  given, does not map values in "{{ }}".
     */
    public function renderMail($member = null, $settings = null, $includePage = false)
    {
        $page = $this->page;
        $space = $page instanceof Page ? null : $page->content->container;
        $settings = new Settings(['space' => $space]);

        $body = $includePage ? $this->pageContent : $settings->mailBody;
        if($member instanceof MemberShip)
            $body .= '<br>br>' . $settings->mailMention;
        if($member)
            $body = MailingListEntry::mapContent($body, $member);
        return $body;
    }

    /**
     *  Send mails to given targets using this entry. Update `is_sent`
     *  attribute.
     *  @return count of mails sent
     */
    public function sendMails($members, $includePage = null)
    {
        if(!count($members))
            return 0;

        $page = $this->page;

        //-- send mails
        $done = [];
        foreach($members as $member) {
            //--- only once per email
            if(in_array($member->email, $done))
                continue;
            $done[] = $member->email;

            $body = $this->renderMail($member, $includePage);
            Yii::$app->mailer->compose()
                ->setTo($member->email)
                ->setSubject($page->title)
                ->setHtmlBody($body)
                ->setTextBody(strip_tags($body))
                ;
        }

        $this->sent_at = (new DateTime())->format("Y-m-d H:i:s");
        $this->save();
        return count($done);
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mailing_list_entry';
    }
}


