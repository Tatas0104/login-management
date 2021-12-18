<?php
namespace Tatas\Belajar\PHP\MVC\Middleware;
require_once __DIR__.'/../Helper/helper.php';
use PHPUnit\Framework\TestCase;
use Tatas\Belajar\PHP\MVC\Config\Database;
use Tatas\Belajar\PHP\MVC\Domain\Session;
use Tatas\Belajar\PHP\MVC\Domain\User;
use Tatas\Belajar\PHP\MVC\Repository\SessionRepository;
use Tatas\Belajar\PHP\MVC\Repository\UserRepository;
use Tatas\Belajar\PHP\MVC\Service\SessionService;

class MustLoginMiddlewareTest extends TestCase{
    private MustLoginMiddleware $middleware;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;
    function __construct()
    {
        $this->middleware=new MustLoginMiddleware();
        putenv("mode=test");
        $this->userRepository=new UserRepository(Database::getConnection());
        $this->sessionRepository=new SessionRepository(Database::getConnection());
        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }
    function testBeforeGuest(){
        $this->middleware->before();
        $this->expectOutputRegex("[Location: /users/login]");
    }
    function testBeforeLoginUser(){
        $user=new User();
        $user->id="tatas";
        $user->name="Tatas";
        $user->password="rahasia";
        $this->userRepository->save($user);
        $session=new Session();
        $session->id=uniqid();
        $session->user_id=$user->id;
        $this->sessionRepository->save($session);
        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
        $this->middleware->before();
        $this->expectOutputString("");
    }
}