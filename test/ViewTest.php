<?php
namespace Tatas\Belajar\PHP\MVC;

use PHPUnit\Framework\TestCase;
use Tatas\Belajar\PHP\MVC\App\View;

class ViewTest extends TestCase{
    public function testRender(){
        View::render('index',["title"=>"page index"]);
        $this->expectOutputRegex('[Login Management]');
        $this->expectOutputRegex('[</html>]');
        $this->expectOutputRegex('[<h1]');
        $this->expectOutputRegex('[Login]');
        $this->expectOutputRegex('[Register]');
    }
}