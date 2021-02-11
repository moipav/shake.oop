<?php


namespace MyProject\Controllers;

use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Exceptions\UserActivationException;
use MyProject\Models\Users\User;
use MyProject\Services\UserActivationService;
use MyProject\Services\UsersAuthService;
use MyProject\Services\EmailSender;


class UsersController extends AbstractController
{


    public function signUp()
    {
        if (!empty($_POST)) {
            try {
                $user = User::signUp($_POST);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('users/signUp.php', ['error' => $e->getMessage()]);
                return;
            }
            if ($user instanceof User) {
                $code = UserActivationService::createActivationCode($user);

                EmailSender::send($user, 'Активация', 'userActivation.php',
                    [
                        'userId' => $user->getId(),
                        'code' => $code
                    ]);
                $this->view->renderHtml('users/signUpSuccessful.php');
                return;
            }
        }
        $this->view->renderHtml('users/signUp.php');
    }

    public function activate(int $userId, string $activationCode)
    {

        $user = User::getById($userId);
        if($user === null){
            throw new UserActivationException('Пользователь с таким ID не начден');
        }
        $isCodeValid = UserActivationService::checkActivationCode($user, $activationCode);
        if ($isCodeValid) {
            $user->activate();
            var_dump($user);
            echo ' OK';
            UserActivationService::deleteActivationCode($user, $activationCode);
        }
        if(!$isCodeValid){
            throw new UserActivationException('нЕ СОВПадает код активации');
        }


    }

    public function login()
    {
        if (!empty($_POST)){
            try{
                $user = User::login($_POST);
                UsersAuthService::createToken($user);
                header('Location: /');
                exit();
            }catch (InvalidArgumentException $e){
                $this->view->renderHtml('users/login.php', ['error'=>$e->getMessage()]);
                return;
            }
        }
        $this->view->renderHtml('users/login.php');
    }

    public function logout()
    {
        if(!empty($_COOKIE['token'])){
           User::logout();
           header('Location: /');
           exit;
        }
    }

}