<?php

namespace app\widgets;

use Yii;
use yii\base\Widget;
use app\models\RegisterForm;

class RegisterWidget extends Widget {

    public function run() {
        if (Yii::$app->user->isGuest) {
            $model = new RegisterForm();
            return $this->render('registerWidget', [
                'model' => $model,
            ]);
        } else {
            return;
        }
    }

}