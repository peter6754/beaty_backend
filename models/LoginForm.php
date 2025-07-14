<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * @SWG\Definition(required={"phone", "password"})
 *
 * @SWG\Property(property="phone", type="string", description="User phone number in format +7(999) 999-99-99")
 * @SWG\Property(property="password", type="string", description="User password")
 * @SWG\Property(property="rememberMe", type="boolean", description="Remember me flag")
 *
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model
{
    public $phone;
    public $password;
    public $rememberMe = true;

    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['phone', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (! $this->hasErrors()) {
            $user = $this->getUser();

            if (! $user || ! $user->validatePassword($this->password)) {
                $this->addError($attribute, 'Неверный телефон или пароль.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
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
            'phone' => 'Телефон',
            'password' => 'Пароль',
            'rememberMe' => 'Запомнить меня',
        ];
    }
}
