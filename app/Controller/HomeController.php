<?php

namespace Tatas\Belajar\PHP\MVC\Controller;


use Tatas\Belajar\PHP\MVC\App\View;
use Tatas\Belajar\PHP\MVC\Config\Database;
use Tatas\Belajar\PHP\MVC\Exception\ValidationException;
use Tatas\Belajar\PHP\MVC\Model\UserLoginRequest;
use Tatas\Belajar\PHP\MVC\Model\UserRegisterRequest;
use Tatas\Belajar\PHP\MVC\Model\UserUpdatePasswordRequest;
use Tatas\Belajar\PHP\MVC\Model\UserUpdateProfileRequest;
use Tatas\Belajar\PHP\MVC\Repository\SessionRepository;
use Tatas\Belajar\PHP\MVC\Repository\UserRepository;
use Tatas\Belajar\PHP\MVC\Service\SessionService;
use Tatas\Belajar\PHP\MVC\Service\UserService;

class HomeController
{
    private UserService $userService;
    private SessionService $sessionService;
    public function __construct()
    {
        $connection=Database::getConnection();
        $userRepository=new UserRepository($connection);
        $this->userService=new UserService($userRepository);
        $sessionRepository=new SessionRepository($connection);
        $this->sessionService=new SessionService($sessionRepository,$userRepository);

    }

    function register(): void
    {
        $model=[
            "title"=>"Register"
        ];
        View::render('register',$model);
    }

    function login(): void
    {
        $model=[
            "title"=>"Login"
        ];
        View::render('login',$model);
    }
    public function postRegister(){
        $request=new UserRegisterRequest();
        $request->id=$_POST['id'];
        $request->name=$_POST['name'];
        $request->password=$_POST['password'];
        try{
            $this->userService->register($request);
            View::redirect('/users/login');
        }catch(ValidationException $exception){
            View::render('register',[
                "title"=>"register",
                "error"=>$exception->getMessage()
            ]);
        }
    }
    public function postLogin(){
        $request=new UserLoginRequest();
        $request->id=$_POST['id'];
        $request->password=$_POST['password'];
        try{
            $response=$this->userService->login($request);
            $this->sessionService->create($response->user->id);
            View::redirect('/');
        }catch(ValidationException $exception){
            View::render('login',[
                "title"=>"login",
                "error"=>$exception->getMessage()
            ]);
        }
    }
    public function logout(){
        $this->sessionService->destroy();
        View::redirect('/');
    }
    public function updateProfile(){
        $user=$this->sessionService->current();
        view::render('updateProfile',[
            "title"=>"Update profile",
            "user"=>[
                "id"=>$user->id,
                "name"=>$user->name
            ]
        ]);
    }
    public function postUpdateProfile(){
        $user=$this->sessionService->current();
        $request=new UserUpdateProfileRequest();
        $request->id=$user->id;
        $request->name=$_POST['name'];
        try{
            $this->userService->updateProfile($request);
            View::redirect('/');
        }catch(ValidationException $exception){
            view::render('updateProfile',[
                "title"=>"Update profile",
                "user"=>[
                    "id"=>$user->id,
                    "name"=>$_POST['name']
                ],
                "error"=>$exception->getMessage()
            ]);
        }

    }
    public function updatePassword(){
        $user=$this->sessionService->current();
        view::render('password',[
            "title"=>"Update password",
            "user"=>[
                "id"=>$user->id
            ]
        ]);
    }
    public function postUpdatePassword(){
        $user=$this->sessionService->current();
        $request=new UserUpdatePasswordRequest();
        $request->id=$user->id;
        $request->oldPassword=$_POST['oldPassword'];
        $request->newPassword=$_POST['newPassword'];
        try{
            $this->userService->updatePassword($request);
            View::redirect('/');
        }catch(ValidationException $exception){
            view::render('password',[
                "title"=>"Update password",
                "user"=>[
                    "id"=>$user->id,
                    "oldPassword"=>$_POST['oldPassword']
                ],
                "error"=>$exception->getMessage()
            ]);
    }
    }
}