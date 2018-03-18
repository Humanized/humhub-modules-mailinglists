<?php

namespace humhub\modules\mailing_lists\controllers;

use humhub\modules\admin\components\Controller;
use humhub\modules\mailing_lists\models\SubscribeForm;
use humhub\modules\mailing_lists\models\Membership;
use Yii;

/**
 *  Mailing-Lists admin controller
 */
class MemberController extends \humhub\modules\content\controllers\ContentController
{

    public function actionSubscribe()
    {
        $model = new SubscribeForm;

        if ($model->load(Yii::$app->request->post())) {
            $model->save();
        }
        return;
    }

    function actionUnsubscribe($token)
    {
        $model = Membership::findOne(['token' => $token]);
        if (isset($model)) {
            $model->delete();
        }
    }

}
