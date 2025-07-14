<?php
namespace app\components;

use Yii;
use yii\base\Component;
use app\models\Users;

class GlobalComponent extends Component {

    static function convertDateToTime($date)
    {
        $array = strptime($date, '%d.%m.%Y');
        return mktime(0, 0, 0, $array['tm_mon']+1, $array['tm_mday'], $array['tm_year']+1900);
    }

    static function translit($text)
    {
        $name = (string) $text; // преобразуем в строковое значение
        $name = strip_tags($name); // убираем HTML-теги
        $name = str_replace(array("\n", "\r"), "", $name); // убираем перевод каретки
        $name = preg_replace("/\s+/", '', $name); // удаляем повторяющие пробелы
        $name = trim($name); // убираем пробелы в начале и конце строки
        $name = function_exists('mb_strtolower') ? mb_strtolower($name) : strtolower($name); // переводим строку в нижний регистр (иногда надо задать локаль)
        $name = strtr($name, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>''));
        return preg_replace("/[^a-z]/i", "", $name); // очищаем строку от недопустимых символов
    }

}
