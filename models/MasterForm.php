<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * @SWG\Definition(required={"phone", "email", "lastname", "firstname", "middlename", "birthday", "password", "password_repeat"})
 *
 * @SWG\Property(property="phone", type="string", description="Master phone number in format +7(999) 999-99-99")
 * @SWG\Property(property="email", type="string", format="email", description="Master email address")
 * @SWG\Property(property="lastname", type="string", description="Master last name")
 * @SWG\Property(property="firstname", type="string", description="Master first name")
 * @SWG\Property(property="middlename", type="string", description="Master middle name")
 * @SWG\Property(property="gender", type="integer", description="Master gender (1 - male, 0 - female)")
 * @SWG\Property(property="birthday", type="string", format="date", description="Master birthday in format d.m.Y")
 * @SWG\Property(property="password", type="string", description="Master password")
 * @SWG\Property(property="password_repeat", type="string", description="Password confirmation")
 */
class MasterForm extends Model
{
    public $phone;
    public $email;
    public $lastname;
    public $firstname;
    public $middlename;
    public $gender;
    public $birthday;
    public $password;
    public $password_repeat;

    private $_user = false;

    public function rules()
    {
        return [
            [['phone', 'email', 'password', 'password_repeat', 'lastname', 'firstname', 'middlename', 'birthday'], 'required'],
            [['birthday'], 'date', 'format' => 'php:d.m.Y'],
            [['gender'], 'integer'],
            [['lastname', 'firstname', 'middlename'], 'string', 'max' => 255],
            ['email', 'email'],
            ['phone', 'match', 'pattern' => '/^\+7\([0-9]{3}\)\s[0-9]{3}\-[0-9]{2}\-[0-9]{2}$/', 'message' => 'Пример: +7(999) 999-99-99'],
            ['phone', 'validatePhone'],
            ['password_repeat', 'validatePassword'],
        ];
    }

    public function validatePhone($attribute, $params)
    {
        if (! $this->hasErrors()) {
            $user = $this->getUser();

            if ($user && Yii::$app->user->isGuest) {
                $this->addError($attribute, 'Телефон уже занят');
            }
        }
    }

    public function validatePassword($attribute, $params)
    {
        if (! $this->hasErrors()) {
            if ($this->password != $this->password_repeat) {
                $this->addError($attribute, 'Пароли не совпадают');
            }
        }
    }

    public function getUser()
    {
        if ($this->_user === false) {
            if (! Yii::$app->user->isGuest) {
                $this->_user = Yii::$app->user->identity;
            } else {
                $this->_user = User::findOne(["phone" => preg_replace('/[^0-9]/', '', $this->phone)]);
            }
        }

        return $this->_user;
    }

    public function attributeLabels()
    {
        return [
            'phone' => 'Телефон',
            'email' => 'Электронная почта',
            'password' => 'Пароль',
            'password_repeat' => 'Подтверждение пароля',
            'lastname' => 'Отчество',
            'firstname' => 'Фамилия',
            'middlename' => 'Имя',
            'birthday' => 'День рождение',
        ];
    }
}