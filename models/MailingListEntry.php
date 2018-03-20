<?php

namespace humhub\modules\mailinglists\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Url;

use humhub\widgets\MarkdownView;

use humhub\modules\custom_pages\models\Page;
use humhub\modules\custom_pages\models\ContainerPage;

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
        return $entry;
    }

    /**
     *  Return the instance of page related to this entry.
     *  @return ContainerPage or Page
     */
    public function getPage()
    {
        if($this->container_page_id)
            return $this->hasOne(ContainerPage::className(),
                                 ['id' => 'container_page_id'])->one();
        return $this->hasOne(Page::className(),
                             ['id' => 'page_id'])->one();
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
    public static function renderMail($content, $page, $member) {
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
     *  Send mails to given targets using this entry. Update `is_sent`
     *  attribute.
     *  @return count of mails sent
     */
    public function sendMails($space, $members, $includePage = null)
    {
        if(!count($members))
            return 0;

        $settings = new Settings(['contentContainer' => $space]);
        $page = $this->page;

        $content = join('<br>\n', [
            $settings->mailHeader,
            $includePage ? $page->pageContent: $settings->mailBody,
            $settings->mailSignature,
        ]);

        //-- send mails
        $done = [];
        foreach($members as $member) {
            //--- only once per email
            if(in_array($member->email, $done))
                continue;
            $done[] = $member->email;

            $body = $content;
            if($member instanceof MemberShip)
                $body = $body . '\n<br>' . $settings->mailMention;

            $body = MailingListEntry::renderMail($body, $page, $member);
            Yii::$app->mailer->compose()
                ->setTo($member->email)
                ->setSubject($page->title)
                ->setHtmlBody($body)
                ->setTextBody(strip_tags($body))
                ;
        }

        $this->is_sent = true;
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


