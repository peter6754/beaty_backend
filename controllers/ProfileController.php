<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\data\Pagination;
use app\models\Coupon;
use app\models\Category;
use app\models\OrderApplication;
use app\models\OrderCoupon;
use YooKassa\Client;

class ProfileController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // только авторизованные пользователи
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $user = Yii::$app->user->identity;

        if (Yii::$app->request->isPost && $user->load(Yii::$app->request->post())) {
            if ($user->save()) {
                Yii::$app->session->setFlash('success', 'Профиль успешно обновлен.');
                return $this->refresh();
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка при сохранении профиля.');
            }
        }

        return $this->render('index', [
            'user' => $user
        ]);
    }
}