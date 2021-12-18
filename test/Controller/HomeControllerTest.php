<?php

namespace Tatas\Belajar\PHP\MVC\Controller{
    require_once __DIR__.'/../Helper/helper.php';
use PHPUnit\Framework\TestCase;
use Tatas\Belajar\PHP\MVC\Config\Database;
    use Tatas\Belajar\PHP\MVC\Domain\Session;
    use Tatas\Belajar\PHP\MVC\Domain\User;
    use Tatas\Belajar\PHP\MVC\Repository\SessionRepository;
    use Tatas\Belajar\PHP\MVC\Repository\UserRepository;
    use Tatas\Belajar\PHP\MVC\Service\SessionService;

class HomeControllerTest extends TestCase{
    private HomeController $homeController;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;
    protected function setUp(): void
    {
        $this->homeController=new HomeController;
        $this->sessionRepository=new SessionRepository(Database::getConnection());
        $this->sessionRepository->deleteAll();
        $this->userRepository=new UserRepository(Database::getConnection());
        $this->userRepository->deleteAll();
        putenv("mode=test");
    }
    function testRegister(){
        $this->homeController->register();
        $this->expectOutputRegex("[Register]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[Name]");
        $this->expectOutputRegex("[Password]");
    }
    function testRegisterSuccess(){
        $_POST['id']="tatas";
        $_POST['name']="Tatas";
        $_POST['password']="rahasia";

        $this->homeController->postRegister();
        $this->expectOutputRegex("[Location:/users/login]");
    }
    function testRegisterFailed(){
        $_POST['id']="";
        $_POST['name']="Tatas";
        $_POST['password']="rahasia";
        $this->homeController->postRegister();
        $this->expectOutputRegex("[Register]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[Name]");
        $this->expectOutputRegex("[Password]");
        $this->expectOutputRegex("[id,name and password cannot blank]");
    }
    function testRegisterDulicate(){
        $user=new User();
        $user->id="tatas";
        $user->name="Tatas";
        $user->password="rahasia";
        $this->userRepository->save($user);
        $_POST['id']="tatas";
        $_POST['name']="Tatas";
        $_POST['password']="rahasia";
        $this->homeController->postRegister();
        $this->expectOutputRegex("[Register]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[Name]");
        $this->expectOutputRegex("[Password]");
        $this->expectOutputRegex("[user is already exist]");
    }
    function testLogin(){
        $this->homeController->login();
        $this->expectOutputRegex("[login]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[password]");
    }
    function testLoginSuccess(){
        $user=new User();
        $user->id="tatas";
        $user->name="Tatas";
        $user->password=password_hash("rahasia",PASSWORD_BCRYPT);
        $this->userRepository->save($user);
        $_POST['id']="tatas";
        $_POST['password']="rahasia";
        $this->homeController->postLogin();
        $this->expectOutputRegex("[Location:/]");
    }
    function testLoginValidationError(){
        $_POST['id']="";
        $_POST['password']="rahasia";
        $this->homeController->postLogin();
        $this->expectOutputRegex("[login]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[password]");
        $this->expectOutputRegex("[id and password cannot blank]");
    }
    function testLoginUserNotFound(){
        $_POST['id']="notFound";
        $_POST['password']="NotFound";
        $this->homeController->postLogin();
        $this->expectOutputRegex("[login]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[password]");
        $this->expectOutputRegex("[id or password is wrong]");
    }
    function testLoginWrongPassword(){
        $user=new User();
        $user->id="tatas";
        $user->name="Tatas";
        $user->password=password_hash("rahasia",PASSWORD_BCRYPT);
        $this->userRepository->save($user);
        $_POST['id']="tatas";
        $_POST['password']="salah";
        $this->homeController->postLogin();
        $this->expectOutputRegex("[login]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[password]");
        $this->expectOutputRegex("[id or password is wrong]");
    }
    function testLogout(){
        $user=new User();
        $user->id="tatas";
        $user->name="Tatas";
        $user->password="rahasia";
        $this->userRepository->save($user);
        $session=new Session();
        $session->id=uniqid();
        $session->user_id=$user->id;
        $this->sessionRepository->save($session );
        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
        $this->homeController->logout();
        $this->expectOutputRegex("[Location: /]");
        $this->expectOutputRegex("[X-Tatas-COOKIE: ]");
    }
}
};

