<?php

namespace humhub\modules\mailinglists\models;

use Yii;
use yii\base\Model;
use humhub\modules\mailinglists\components\SubscriptionValidator;
use humhub\modules\mailinglists\models\Subscriber;
use humhub\modules\user\models\User;

/**
 * ContactForm is the model behind the contact form.
 */
class SubscribeForm extends Model
{

    public $email;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['email'], 'required'],
            ['email', 'trim'],
            // email has to be a valid email address
            ['email', 'email'],
            // verifyCode needs to be entered correctly
            ['email', SubscriptionValidator::className()],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
        ];
    }

    public function save()
    {
        $model = new Subscriber(['email' => $this->email]);
        $model->is_member = (NULL !== User::findOne(['email' => $this->email]));
        return $model->save();
    }

}
