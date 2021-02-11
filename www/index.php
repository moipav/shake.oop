<?php

function myAutoloader(string $className)
{
   $className = str_replace('\\', '/', $className );

  require __DIR__ . '/../src/' . $className . '.php';
}
try {
    spl_autoload_register('myAutoloader');
    /**
     * обработка GET параметров
     */
    $route = $_GET['route'] ?? '';
    $routes = require_once __DIR__ . '/../src/routes.php';
//var_dump($routes);

    $isRoteFound = false;
    foreach ($routes as $pattern => $controllerAndAction) {
        preg_match($pattern, $route, $matches);
        if (!empty($matches)) {
            $isRoteFound = true;
            break;
        }
    }

    /*if (!$isRoteFound) {
        echo 'Страница не найдена';
        return;
    }*/
    if(!$isRoteFound){
        throw new \MyProject\Exceptions\NotFoundException();
    }
    /*var_dump($controllerAndAction);
    var_dump($matches);*/

    $controllerName = $controllerAndAction[0];
    $actionName = $controllerAndAction[1];
//удвлим не нужный элемент
    unset($matches[0]);

    $controller = new $controllerName();
//чтобы передать в элемент массива в качестве аргумента используем
    $controller->$actionName(...$matches);
}catch (\MyProject\Exceptions\DbException $e){
    $view = new \MyProject\View\View(__DIR__ . '/../src/templates/errors/');
    $view->renderHtml('500.php', ['error'=> $e->getMessage()], 500);
}catch (\MyProject\Exceptions\NotFoundException $e){
    $view = new \MyProject\View\View(__DIR__ . '/../src/templates/errors');
    $view->renderHtml('404.php', ['error'=> $e->getMessage()], 404);
}catch (\MyProject\Exceptions\UserActivationException $e){
    $view = new \MyProject\View\View(__DIR__ . '/../src/templates/errors');
    $view->renderHtml('500.php', ['error'=>$e->getMessage()], 500);
}catch (\MyProject\Exceptions\UnauthorizedException $e){
    $view = new \MyProject\View\View(__DIR__ . '/../src/templates/errors');
    $view->renderHtml('401.php', ['error'=> $e->getMessage()], 401);
}catch (\MyProject\Exceptions\ForbiddenException $e){
    $view = new \MyProject\View\View(__DIR__ . '/../src/templates/errors');
    $view->renderHtml('403.php', ['error'=>$e->getMessage()], 403);
}
