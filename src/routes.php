<?php

use MyProject\Controllers\MainController;

return [
    '~^articles/(\d+)$~' => [\MyProject\Controllers\ArticlesController::class, 'view'],
    '~^articles/(\d+)/edit$~' => [\MyProject\Controllers\ArticlesController::class, 'edit'],
    '~^articles/add$~' => [\MyProject\Controllers\ArticlesController::class, 'add'],
    '~^articles/(\d+)/delete$~' => [\MyProject\Controllers\ArticlesController::class, 'delete'],
    '~^users/register$~' =>[MyProject\Controllers\UsersController::class, 'signUp'],
    '~^users/login$~' =>[MyProject\Controllers\UsersController::class, 'login'],
    '~^users/logout$~' =>[MyProject\Controllers\UsersController::class, 'logout'],
    '~^users/(\d+)/activate/(.+)$~'=>[\MyProject\Controllers\UsersController::class, 'activate'],
    '~^$~' => [\MyProject\Controllers\MainController::class, 'main'],
];
