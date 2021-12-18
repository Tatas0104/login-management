<?php

namespace Tatas\Belajar\PHP\MVC\App {

    function header(string $value){
        echo $value;
    }

}

namespace Tatas\Belajar\PHP\MVC\Service {

    function setcookie(string $name, string $value){
        echo "$name: $value";
    }

}