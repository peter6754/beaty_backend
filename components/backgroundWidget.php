<?php

namespace app\components;

use Yii;
use yii\base\Widget;
use app\models\Category;
use yii\helpers\Url;

class backgroundWidget extends Widget {

    public $type = null;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        if($this->type == "category") {
            return '<img class="banner_img" src="/images/banners/category.png" /><img class="banner_img_mobile" src="/images/banners/category_mobile.png" />';
        }
        if($this->type == "contact") {
            return '<img class="banner_img" src="/images/banners/contact.png" /><img class="banner_img_mobile" src="/images/banners/contact_mobile.png" />';
        }
        if($this->type == "about") {
            return '<img class="banner_img" src="/images/banners/about.png" /><img class="banner_img_mobile" src="/images/banners/about_mobile.png" />';
        }
        if($this->type == "payment") {
            return '<img class="banner_img" src="/images/banners/payment.png" /><img class="banner_img_mobile" src="/images/banners/payment_mobile.png" />';
        }


        if (Yii::$app->controller->id == "category" && Yii::$app->controller->action->id == "view") {
            $id = (int)Yii::$app->request->get("id", 0);
            return '<img class="banner_img" src="/images/banners/category_' . $id . '.png" /><img class="banner_img_mobile" src="/images/banners/category_mobile_' . $id . '.png" />';
        }
        return '<img class="banner_img" src="/images/banners/main.png" /><img class="banner_img_mobile" src="/images/banners/main_mobile.png" />';
    }
}
