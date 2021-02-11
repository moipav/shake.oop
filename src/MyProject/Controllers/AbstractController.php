<?php


namespace MyProject\Controllers;




use MyProject\Services\UsersAuthService;
use MyProject\View\View;

class AbstractController
{
    protected $view;

    protected $user;

    public function __construct()
    {
        $this->user = UsersAuthService::getUserByToken();
        $this->view = new View(__DIR__ . '/../../templates');
        $this->view->setVar('user', $this->user);
    }

    public static function isAuth($user)
    {
        if(!empty($user)){
            echo 'Привет '. $user->getNickname() . ' <a href="/users/logout">выход</a>';
        }else echo '<a href="/users/login">Войти | </a> <a href="/users/register">Зарегестрироваться</a>';
        return;
    }
}