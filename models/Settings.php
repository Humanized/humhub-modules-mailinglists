<?php

namespace humhub\modules\mailinglists\models;

use humhub\modules\custom_pages\modules\template\models\Template;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 *  Module settings. Also be used for as a model form.
 */
class Settings extends Model
{
    public $settings;
    public $contentContainer;

    /**
     *  Email header
     */
    public $mailHeader;
    /**
     *  Email body (when page content is not included directly)
     */
    public $mailBody;
    /**
     *  Email signature
     */
    public $mailSignature;
    /**
     *  Unsubscribe/Legal mention
     */
    public $mailMention;

    public $defaultHeader = 'Dear people,';
    public $defaultSignature = 'Regards,';
    public $defaultBody =
        "<p>" .
        "We just published our last informational mail. Why don't you " .
        'come out and take a look? <a href="{{ page.url }}">{{ page.url }}</a>?' .
        "</p>"
         ;
    public $defaultMention =
        '--<p style="font-size: 0.9em">' .
        'You can unsubscribe from this mailing-list following this link: ' .
        '<a href="{{ member.unsubscribe }}">{{ member.unsubscribe }}</a>' .
        '</p>'
    ;


    /**
     * @inheritdoc
     */
    public function init()
    {
        $settings = $this->getSettings();
        $this->mailHeader = $settings->get('globalHeader', $this->defaultHeader);
        $this->mailBody = $settings->get('globalBody', $this->defaultBody);
        $this->mailSignature = $settings->get('globalSignature', $this->defaultSignature);
        $this->mailMention = $settings->get('globalMention', $this->defaultMention);
    }


    function getSettings()
    {
        if(!$this->settings) {
            $module = Yii::$app->getModule('mailinglists');
            $this->settings = ($this->contentContainer) ?
                $module->settings->contentContainer($this->contentContainer) :
                $module->settings;
        }
        return $this->settings;
    }

    /**
     *  Update a given module setting using this instance
     *  attributes.
     */
    function updateSetting($name, $defaultValue)
    {
        $settings = $this->getSettings();
        if(empty($this->$name)) {
            $settings->delete($name);
            $this->$name = $defaultValue;
        } else {
            $settings->set($name, $this->$name);
        }
    }

    /**
     * Saves the settings in case the validation succeeds.
     */
    public function save()
    {
        if(!$this->validate()) {
            return false;
        }
        $this->updateSetting('mailHeader', $this->defaultHeader);
        $this->updateSetting('mailSignature', $this->defaultSignature);
        $this->updateSetting('mailMention', $this->defaultMention);
        $this->updateSetting('mailBody', $this->defaultBody);
        return true;
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['mailHeader', 'string'],
            ['mailBody', 'string'],
            ['mailSignature', 'string'],
            ['mailMention', 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mailHeader' => 'Emails Header',
            'mailBody' => 'Emails Body',
            'mailSignature' => 'Emails Signature',
            'mailMention' => 'Legal Mention',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'mailHeader' => '',
            'mailBody' => 'body of emails when they don\'t include the page contnt',
            'mailSignature' => '',
            'mailMention' => 'legal mention such as unsubscribe etc.',
        ];
    }
}
