<?php

namespace app\widgets;

use Yii;
use yii\base\Widget;
use app\models\CouponForm;

class CouponWidget extends Widget
{

    public function run()
    {
        $model = new CouponForm();
        return $this->render('couponWidget', [
            'model' => $model,
        ]);
    }

}