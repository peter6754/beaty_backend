<?php

namespace app\widgets;

use Yii;
use yii\base\Widget;
use app\models\ForgotForm;

class ForgotWidget extends Widget {

    public function run() {
        if (Yii::$app->user->isGuest) {
            $model = new ForgotForm();
            return $this->render('forgotWidget', [
                'model' => $model,
            ]);
        } else {
            return;
        }
    }

}