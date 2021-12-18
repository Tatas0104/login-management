<?php
namespace Tatas\Belajar\PHP\MVC\Controller;

use PHPUnit\Framework\TestCase;
use Tatas\Belajar\PHP\MVC\Config\Database;
use Tatas\Belajar\PHP\MVC\Domain\Session;
use Tatas\Belajar\PHP\MVC\Domain\User;
use Tatas\Belajar\PHP\MVC\Repository\SessionRepository;
use Tatas\Belajar\PHP\MVC\Repository\UserRepository;
use Tatas\Belajar\PHP\MVC\Service\SessionService;

class DashboardControllerTest extends TestCase{
    private DashboardController $dashboardController;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;
    protected function setUp():void{
        $this->dashboardController=new DashboardController();
        $this->sessionRepository=new SessionRepository(Database::getConnection());
        $this->userRepository=new UserRepository(Database::getConnection());
        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll(); 
    }
    public function testGuest(){
        $this->dashboardController->index();
        $this->expectOutputRegex("[Page Index]");
    }
    function testUserLogin(){
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
        $this->dashboardController->index();
        $this->expectOutputRegex("[Hello Tatas]");
    }
}