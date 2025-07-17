<?php

namespace app\modules\api\controllers\swagger;

use yii\helpers\Url;
use Yii;

class DefaultController extends \yii\web\Controller
{
    public function actions()
    {
        return [
            'doc' => [
                'class' => 'light\swagger\SwaggerAction',
                'restUrl' => Url::to(['/docs/swagger.json'], true),
            ],
            'api' => [
                'class' => 'light\swagger\SwaggerApiAction',
                'scanDir' => [
                    Yii::getAlias('@app/modules/api/controllers'),
                ],
            ],
        ];
    }
}
