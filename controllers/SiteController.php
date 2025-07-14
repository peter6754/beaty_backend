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
use YooKassa\Client;
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
                    // В режиме разработки пропускаем оплату
                    if (YII_ENV_DEV) {
                        Yii::$app->session->setFlash('success', 'Регистрация мастера завершена (тестовый режим без оплаты)');
                        return $this->redirect(['site/index']);
                    }

                    $client = new Client();
                    $client->setAuth(Yii::$app->params['yoomoney_shopid'], Yii::$app->params['yoomoney_secret']);

                    $returnUrl = YII_ENV_DEV ?
                        'http://localhost:8080/site/register-master' :
                        'https://beautyms.ru/site/register-master';

                    $response = $client->createPayment(
                        array(
                            'amount' => array(
                                'value' => 199, // 199 рублей при регистрации мастером, пропускаем оплату в тестовом режиме
                                'currency' => 'RUB',
                            ),
                            'confirmation' => array(
                                'type' => 'redirect',
                                'locale' => 'ru_RU',
                                'return_url' => $returnUrl,
                            ),
                            'capture' => true,
                            'description' => "Пошлина регистрации мастером"
                        ),
                        "M".$master->id
                    );

                    $master->birthday = date("d.m.Y", $master->birthday);
                    $master->order_id = $response->getId();
                    if (! $master->save()) {
                        throw new \Exception('Ошибка при сохранении данных мастера');
                    }

                    return $this->redirect($response->getConfirmation()->getConfirmationUrl());
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
