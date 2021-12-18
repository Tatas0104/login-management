<?php

namespace Tatas\Belajar\PHP\MVC\App;

class View
{

    public static function render(string $view, $model)
    {
        require __DIR__ . '/../View/template/header.php';
        require __DIR__ . '/../View/' . $view . '.php';
        require __DIR__ . '/../View/template/footer.php';
    }
    public static function redirect(string $url){
        header("Location:$url");
        if(getenv("mode")!="test"){
        exit();
        }
    }

}