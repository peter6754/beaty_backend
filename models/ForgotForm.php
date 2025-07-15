<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ForgotForm is the model behind the forgot password form.
 */
class ForgotForm extends Model
{
    public $email;

    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            ['email', 'email'],
            ['password', 'validateEmail'],
        ];
    }

    public function validateEmail($attribute, $params)
    {
        if (! $this->hasErrors()) {
            $user = $this->getUser();

            if (! $user) {
                $this->addError($attribute, 'Пользователь не найден');
            }
        }
    }

    public function forgot()
    {
        if ($this->validate()) {
            return true;
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
            $this->_user = User::findOne(["email" => $this->email]);
        }

        return $this->_user;
    }

    public function attributeLabels()
    {
        return [
            'email' => 'Электронная почта',
        ];
    }
}
