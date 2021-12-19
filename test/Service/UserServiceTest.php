<?php
namespace Tatas\Belajar\PHP\MVC\Service;

use PHPUnit\Framework\TestCase;
use Tatas\Belajar\PHP\MVC\Config\Database;
use Tatas\Belajar\PHP\MVC\Domain\User;
use Tatas\Belajar\PHP\MVC\Exception\ValidationException;
use Tatas\Belajar\PHP\MVC\Model\UserLoginRequest;
use Tatas\Belajar\PHP\MVC\Model\UserRegisterRequest;
use Tatas\Belajar\PHP\MVC\Model\UserUpdatePasswordRequest;
use Tatas\Belajar\PHP\MVC\Model\UserUpdateProfileRequest;
use Tatas\Belajar\PHP\MVC\Repository\UserRepository;
use Tatas\Belajar\PHP\MVC\Repository\SessionRepository;

class UserServiceTest extends TestCase{
    private UserService $userService;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;
    protected function setUp():void{
        $this->userRepository=new UserRepository(Database::getConnection());
        $this->userService=new UserService($this->userRepository);
        $this->sessionRepository=new SessionRepository(Database::getConnection());
        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }
    function testRegisterSuccess(){
        $request=new UserRegisterRequest();
        $request->id="tatas";
        $request->name="Tatas";
        $request->password="rahasia";
        $response=$this->userService->register($request);
        self::assertEquals($response->user->id,$request->id);
        self::assertEquals($response->user->name,$request->name);
        self::assertNotEquals($response->user->password,$request->password);
        self::assertTrue(password_verify($request->password,$response->user->password));
    }
    function testRegisterFailed(){
        $this->expectException(ValidationException::class);
        $request=new UserRegisterRequest();
        $request->id="";
        $request->name="";
        $request->password="";
        $this->userService->register($request);
    }
    function testRegisterDuplicate(){
        $user=new User();
        $user->id="tatas";
        $user->name="Tatas";
        $user->password="rahasia";
        $this->userRepository->save($user);
        $this->expectException(ValidationException::class);
        $request=new UserRegisterRequest();
        $request->id="tatas";
        $request->name="Tatas";
        $request->password="rahasia";
        $this->userService->register($request);
    }
    function testLoginNotFound(){
        $this->expectException(ValidationException::class);
        $request=new UserLoginRequest();
        $request->id="tatas";
        $request->password="Tatas";
        $this->userService->login($request);
    }
    function testLoginWrongPassword(){
        $user=new User();
        $user->id="tatas";
        $user->name="Tatas";
        $user->password=password_hash("rahasia",PASSWORD_BCRYPT);
        $this->userRepository->save($user);
        $this->expectException(ValidationException::class);
        $request=new UserLoginRequest();
        $request->id="tatas";
        $request->password="Tatas";
        $this->userService->login($request);
    }
    function testLoginSuccess(){
        $user=new User();
        $user->id="tatas";
        $user->name="Tatas";
        $user->password=password_hash("rahasia",PASSWORD_BCRYPT);
        $this->userRepository->save($user);
        $request=new UserLoginRequest();
        $request->id="tatas";
        $request->password="rahasia";
        $response=$this->userService->login($request);
        self::assertEquals($request->id,$response->user->id);
        self::assertTrue(password_verify($request->password,$response->user->password));
    }
    function testUpdateSuccess(){
        $user=new User();
        $user->id="tatas";
        $user->name="Tatas";
        $user->password="rahasia";
        $this->userRepository->save($user);
        $request=new UserUpdateProfileRequest();
        $request->id="tatas";
        $request->name="Tuhu";
        $this->userService->updateProfile($request);
        $result=$this->userRepository->findById($user->id);
        self::assertEquals($request->name,$result->name);
    }
    function testUpdateValidationError(){
        $this->expectException(ValidationException::class);
        $request=new UserUpdateProfileRequest();
        $request->id="";
        $request->name="";
        $this->userService->updateProfile($request);
    }
    function testUpdateNotFound(){
        $this->expectException(ValidationException::class);
        $request=new UserUpdateProfileRequest();
        $request->id="notFound";
        $request->name="notFound";
        $this->userService->updateProfile($request);
    }
    function testUpdatePasswordSuccess(){
        $user=new User();
        $user->id="tatas";
        $user->name="Tatas";
        $user->password=password_hash("rahasia",PASSWORD_BCRYPT);
        $this->userRepository->save($user);
        $request=new UserUpdatePasswordRequest();
        $request->id="tatas";
        $request->oldPassword="rahasia";
        $request->newPassword="new";
        $this->userService->updatePassword($request);
        $result=$this->userRepository->findById($user->id);
        self::assertTrue(password_verify($request->newPassword,$result->password));
    }
    function testUpdatePasswordVlidationError(){
        $this->expectException(ValidationException::class);
        $request=new UserUpdatePasswordRequest();
        $request->id="tatas";
        $request->oldPassword="salah";
        $request->newPassword="new";
        $this->userService->updatePassword($request);
    }
    function testUpdatePasswordWrongOldPassword(){
        $this->expectException(ValidationException::class);
        $user=new User();
        $user->id="tatas";
        $user->name="Tatas";
        $user->password=password_hash("rahasia",PASSWORD_BCRYPT);
        $this->userRepository->save($user);
        $request=new UserUpdatePasswordRequest();
        $request->id="tatas";
        $request->oldPassword="salah";
        $request->newPassword="new";
        $this->userService->updatePassword($request);
    }
    function testUpdatePasswordNotFound(){
        $this->expectException(ValidationException::class);
        $request=new UserUpdatePasswordRequest();
        $request->id="notFound";
        $request->oldPassword="notFound";
        $request->newPassword="rahasia";
        $this->userService->updatePassword($request);
    }
}