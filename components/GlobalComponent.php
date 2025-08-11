<?php
namespace app\components;

use Yii;
use yii\base\Component;
use app\models\Users;
use DateTime;

class GlobalComponent extends Component
{

    static function convertDateToTime($date)
    {
        $dateTime = DateTime::createFromFormat('d.m.Y', $date);
        return $dateTime ? $dateTime->getTimestamp() : false;
    }

    static function translit($text)
    {
        $name = (string) $text;
        $name = strip_tags($name);
        $name = str_replace(array("\n", "\r"), "", $name);
        $name = preg_replace("/\s+/", '', $name);
        $name = trim($name);
        $name = function_exists('mb_strtolower') ? mb_strtolower($name) : strtolower($name);
        $name = strtr($name, array('а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e', 'ж' => 'j', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch', 'ы' => 'y', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya', 'ъ' => '', 'ь' => ''));
        return preg_replace("/[^a-z]/i", "", $name);
    }

}
