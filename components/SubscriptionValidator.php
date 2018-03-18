<?php

namespace humhub\modules\mailinglists\components;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\validators\Validator;
use humhub\modules\mailinglists\models\Membership;

/**
 * Description of SubscriptionValidator
 *
 * @author Jeffrey Geyssens <jeffrey@humanized.be>
 */
class SubscriptionValidator extends Validator
{

    public function validateAttribute($model, $attribute)
    {
        $m = Membership::findOne(['email' => $model->$attribute]);
        if (isset($m)) {
            $this->addError($model, $attribute, 'Email address already subscribed to mailinglists');
        }
    }

}
