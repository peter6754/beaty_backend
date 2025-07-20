<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use app\models\OrderCoupon;
use app\models\OrderApplication;
use app\models\Master;

class OrderController extends Controller
{
    public function actionCoupon()
    {
        // Для Robokassa проверка статуса платежа происходит через callback
        // Этот метод может использоваться для дополнительной синхронизации
        // Пока оставляем пустым, так как статус обновляется через Result URL
        return ExitCode::OK;
    }

    public function actionMaster()
    {
        // Для Robokassa проверка статуса платежа происходит через callback
        // Этот метод может использоваться для дополнительной синхронизации
        // Пока оставляем пустым, так как статус обновляется через Result URL
        return ExitCode::OK;
    }
}
