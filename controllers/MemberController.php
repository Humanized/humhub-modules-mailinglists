<?php

namespace humhub\modules\mailinglists\controllers;

use humhub\modules\mailinglists\models\SubscribeForm;
use humhub\modules\mailinglists\models\Membership;
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
            if ($model->save()) {
                $this->view->success('Newsletter subscription successful!');
            } else {
                $this->view->error('Already subscribed to newsletter!');
            }
        }
        return $this->redirect(Yii::$app->homeUrl);
    }

    function actionUnsubscribe($token)
    {
        $model = Membership::findOne(['token' => $token]);
        if (isset($model) && $model->delete()) {
            $this->view->success('Newsletter unsubscription successful!');
        }
        return $this->redirect(Yii::$app->homeUrl);
    }

}
