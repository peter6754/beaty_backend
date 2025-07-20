<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\RegisterForm;
use app\models\ForgotForm;
use app\models\Category;
use app\models\Product;
use app\models\MasterForm;
use app\models\MasterProceedForm;
use app\models\Master;
use app\models\User;
use app\models\Coupon;
use app\models\OrderApplication;
use app\models\Orders;
use yii\web\UploadedFile;
use yii\web\HttpException;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $category = Category::find()->where(["active" => 1])->all();
        $coupons = Coupon::find()->all();

        return $this->render('index', [
            'category' => $category,
            'coupons' => $coupons,
            'masters' => Master::find()->all()
        ]);
    }

    public function actionLogin()
    {
        if (Yii::$app->request->isAjax) {
            $model = new LoginForm();
            if ($model->load(Yii::$app->request->post())) {
                if ($model->login()) {
                    return $this->goHome();
                } else {
                    Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
                    return \yii\widgets\ActiveForm::validate($model);
                }
            }
        } else {
            throw new HttpException(404, 'Страница не найдена');
        }
    }

    public function actionRegister()
    {
        $model = new RegisterForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->register()) {
                Yii::$app->session->setFlash('success', 'Регистрация прошла успешно!');
                return $this->goHome();
            } else {
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
                    return \yii\widgets\ActiveForm::validate($model);
                }
            }
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('register', [
                'model' => $model,
            ]);
        }

        return $this->render('register', [
            'model' => $model,
        ]);
    }

    public function actionFinishMaster()
    {
        return $this->render('finish-master');
    }

    public function actionRegisterMaster()
    {

        $user_id = Yii::$app->user->isGuest ? 0 : Yii::$app->user->identity->id;
        $master = Master::findOne(["user_id" => $user_id]);

        if ($master) {
            $products = [];
            $product_query = Product::find()->all();
            foreach ($product_query as $product_item) {
                $products[$product_item->id] = $product_item->name;
            }

            $masterProceedForm = new MasterProceedForm([
                "hash" => Yii::$app->security->generateRandomString(8),
                "client_gender" => 2
            ]);
            if ($masterProceedForm->load($this->request->post()) && $masterProceedForm->validate()) {
                $master->setAttributes($masterProceedForm->attributes);
                $master->save();

                return $this->redirect(["site/finish-master"]);
            }

            return $this->render('register-master', [
                'masterProceedForm' => $masterProceedForm,
                'products' => $products
            ]);
        }


        $masterForm = new MasterForm(["gender" => 1]);
        if ($masterForm->load($this->request->post()) && $masterForm->validate()) {
            try {
                $user = Yii::$app->user->isGuest ? User::findOne(["phone" => preg_replace('/[^0-9]/', '',
                    $masterForm->phone)]) : Yii::$app->user->identity;
                if (empty($user)) {
                    $user = new User([
                        "name" => $masterForm->middlename,
                        "email" => $masterForm->email,
                        "phone" => $masterForm->phone,
                        "password" => Yii::$app->getSecurity()->generatePasswordHash($masterForm->password),
                        "token" => Yii::$app->getSecurity()->generateRandomString()
                    ]);
                    if (! $user->save()) {
                        throw new \Exception('Ошибка при создании пользователя');
                    }

                    $auth = Yii::$app->authManager;
                    $role = $auth->getRole("master");
                    if ($role) {
                        $auth->assign($role, $user->id);
                    }

                    Yii::$app->user->login($user, 3600 * 24 * 30);
                }

                $master = new Master(["user_id" => $user->id, "date" => time()]);
                $master->setAttributes($masterForm->attributes);

                if ($master->save()) {
                    // Создание ссылки на оплату через Robokassa для регистрации мастера
                    $mrh_login = Yii::$app->params['robokassa_login'];
                    $mrh_pass1 = Yii::$app->params['robokassa_pass1'];
                    $amount = 199; // 199 рублей при регистрации мастером

                    $master->order_id = (string) $master->id;
                    if (!$master->save()) {
                        throw new HttpException(500, 'Ошибка при обновлении мастера');
                    }

                    $crc = md5("$mrh_login:$amount:$master->order_id:$mrh_pass1");

                    $resultUrl = urlencode('https://www.beautyms.ru/api/payment/result');
                    $successUrl = urlencode('https://www.beautyms.ru/success');
                    $failUrl = urlencode('https://www.beautyms.ru/fail');

                    $url = "https://auth.robokassa.ru/Merchant/Index.aspx?MerchantLogin=$mrh_login&OutSum=$amount&InvId=$master->order_id&Description=Пошлина регистрации мастером&SignatureValue=$crc&IsTest=" . Yii::$app->params['robokassa_test'] . "&ResultURL=$resultUrl&SuccessURL=$successUrl&FailURL=$failUrl";

                    $master->birthday = date("d.m.Y", $master->birthday);
                    if (! $master->save()) {
                        throw new \Exception('Ошибка при сохранении данных мастера');
                    }

                    return $this->redirect($url);
                } else {
                    throw new \Exception('Ошибка при создании записи мастера');
                }
            } catch (\Exception $e) {
                // Логируем детальную информацию об ошибке
                $errorMessage = $e->getMessage();
                if (method_exists($e, 'getCode')) {
                    $errorMessage .= ' (Code: '.$e->getCode().')';
                }

                Yii::error('Ошибка регистрации мастера: '.$errorMessage);
                Yii::$app->session->setFlash('error', 'Произошла ошибка при регистрации: '.$errorMessage);
                return $this->render('register-master', [
                    'masterForm' => $masterForm
                ]);
            }
        }

        return $this->render('register-master', [
            'masterForm' => $masterForm
        ]);
    }

    public function actionForgot()
    {
        if (Yii::$app->request->isAjax) {
            $model = new ForgotForm();
            if ($model->load(Yii::$app->request->post())) {
                if ($model->forgot()) {
                    return $this->goBack();
                } else {
                    Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
                    return \yii\widgets\ActiveForm::validate($model);
                }
            }
        } else {
            throw new HttpException(404, 'Страница не найдена');
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        return $this->render('contact');
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionPayment()
    {
        return $this->render('payment');
    }
}
