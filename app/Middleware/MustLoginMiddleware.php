<?php
namespace Tatas\Belajar\PHP\MVC\Middleware;

use Tatas\Belajar\PHP\MVC\App\View;
use Tatas\Belajar\PHP\MVC\Config\Database;
use Tatas\Belajar\PHP\MVC\Repository\SessionRepository;
use Tatas\Belajar\PHP\MVC\Repository\UserRepository;
use Tatas\Belajar\PHP\MVC\Service\SessionService;

class MustLoginMiddleware implements Middleware{
    private SessionService $sessionService;
    function __construct()
    {
        $userRepository=new UserRepository(Database::getConnection());
        $sessionRepository=new SessionRepository(Database::getConnection());
        $this->sessionService=new SessionService($sessionRepository,$userRepository);
    }
    function before():void{
        $user=$this->sessionService->current();
        if($user==null){
            View::redirect('/users/login');
        }
    }
}