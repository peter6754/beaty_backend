<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * @SWG\Definition(required={"phone", "name", "email", "password", "password_repeat"})
 *
 * @SWG\Property(property="phone", type="string", description="User phone number in format +7(999) 999-99-99")
 * @SWG\Property(property="name", type="string", description="User name")
 * @SWG\Property(property="email", type="string", format="email", description="User email address")
 * @SWG\Property(property="password", type="string", description="User password")
 * @SWG\Property(property="password_repeat", type="string", description="Password confirmation")
 */
class RegisterForm extends Model
{
    public $phone;
    public $name;
    public $email;
    public $password;
    public $password_repeat;

    private $_user = false;

    public function rules()
    {
        return [
            [['name', 'phone', 'email', 'password', 'password_repeat'], 'required'],
            ['email', 'email'],
            ['phone', 'match', 'pattern' => '/^\+7\([0-9]{3}\)\s[0-9]{3}\-[0-9]{2}\-[0-9]{2}$/', 'message' => 'Пример: +7(999) 999-99-99'],
            [['name'], 'string', 'max' => 255],
            ['phone', 'validatePhone'],
            ['email', 'validateEmail'],
            ['password_repeat', 'validatePassword'],
        ];
    }

    public function validatePhone($attribute, $params)
    {
        if (! $this->hasErrors()) {
            $user = $this->getUser();

            if ($user) {
                $this->addError($attribute, 'Телефон уже занят');
            }
        }
    }

    public function validateEmail($attribute, $params)
    {
        if (! $this->hasErrors()) {
            $user = User::findOne(['email' => $this->email]);

            if ($user) {
                $this->addError($attribute, 'Email уже используется');
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

    public function register()
    {
        if ($this->validate()) {
            $user = new User([
                "name" => $this->name,
                "phone" => $this->phone,
                "email" => $this->email,
                "password" => Yii::$app->getSecurity()->generatePasswordHash($this->password),
                "token" => Yii::$app->getSecurity()->generateRandomString()
            ]);

            if (! $user->save()) {
                // Добавляем ошибки пользователя в форму
                foreach ($user->errors as $attribute => $errors) {
                    foreach ($errors as $error) {
                        $this->addError($attribute, $error);
                    }
                }
                return false;
            }

            $auth = Yii::$app->authManager;
            $role = $auth->getRole("client");

            if ($role) {
                // Проверяем, не назначена ли уже роль этому пользователю
                $assignment = $auth->getAssignment("client", $user->id);
                if (! $assignment) {
                    $auth->assign($role, $user->id);
                }
            }

            return Yii::$app->user->login($user, 3600 * 24 * 30);
        }
        return false;
    }

    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findOne(["phone" => preg_replace('/[^0-9]/', '', $this->phone)]);
        }

        return $this->_user;
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Имя',
            'phone' => 'Телефон',
            'email' => 'Электронная почта',
            'password' => 'Пароль',
            'password_repeat' => 'Подтверждение пароля',
        ];
    }
}