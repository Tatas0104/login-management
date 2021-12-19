<?php
namespace Tatas\Belajar\PHP\MVC\Service;

use Exception;
use Tatas\Belajar\PHP\MVC\Config\Database;
use Tatas\Belajar\PHP\MVC\Domain\User;
use Tatas\Belajar\PHP\MVC\Exception\ValidationException;
use Tatas\Belajar\PHP\MVC\Model\UserLoginRequest;
use Tatas\Belajar\PHP\MVC\Model\UserLoginResponse;
use Tatas\Belajar\PHP\MVC\Model\UserRegisterRequest;
use Tatas\Belajar\PHP\MVC\Model\UserRegisterResponse;
use Tatas\Belajar\PHP\MVC\Model\UserUpdatePasswordRequest;
use Tatas\Belajar\PHP\MVC\Model\UserUpdatePasswordResponse;
use Tatas\Belajar\PHP\MVC\Model\UserUpdateProfileRequest;
use Tatas\Belajar\PHP\MVC\Model\UserUpdateProfileResponse;
use Tatas\Belajar\PHP\MVC\Repository\UserRepository;

class UserService{
    private UserRepository $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository=$userRepository;
    }
    public function register(UserRegisterRequest $request):UserRegisterResponse{
        $this->validateUserRegisterationRequest($request);
        try{
            Database::beginTransaction();
            $user=$this->userRepository->findById($request->id);
        if($user !=null){
            throw new ValidationException("user is already exist");
        }
        $user=new User();
        $user->id=$request->id;
        $user->name=$request->name;
        $user->password=password_hash($request->password,PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $response=new UserRegisterResponse();
        $response->user=$user;
        Database::commitTransaction();
        return $response;
        }catch(Exception $e){
            Database::rollbackTransaction();
            throw $e;
        }
        
    }
    private function validateUserRegisterationRequest(UserRegisterRequest $request):void{
        if($request->id==null ||$request->name==null ||$request->password==null||
        trim($request->id)==""||trim($request->name)==""||trim($request->password)==""){
            throw new ValidationException("id,name and password cannot blank");
        }
    }
    public function login(UserLoginRequest $request):UserLoginResponse{
        $this->validateUserLoginRequest($request);
        $user=$this->userRepository->findById($request->id);
        if($user==null){
            throw new ValidationException("id or password is wrong");
        }
        if(password_verify($request->password,$user->password)){
            $response=new UserLoginResponse();
            $response->user=$user;
            return $response;
        }else{
            throw new ValidationException("id or password is wrong");
        }
    }
    private function validateUserLoginRequest(UserLoginRequest $request):void{
        if($request->id==null||$request->password==null||
        trim($request->id)==""||trim($request->password)==""){
            throw new ValidationException("id and password cannot blank");
        }
    }
    public function updateProfile(UserUpdateProfileRequest $request):UserUpdateProfileResponse {
        $this->validateUserProfileUpdateRequest($request);
        try{
            Database::beginTransaction();
            $user=$this->userRepository->findById($request->id);
            if($user==null){
                throw new ValidationException("User is not found");
            }
            $user->name=$request->name;
            $this->userRepository->update($user);
            Database::commitTransaction();
            $response=new UserUpdateProfileResponse();
            $response->user=$user;
            return $response;
        }catch(Exception $exception){
            Database::rollbackTransaction();
            throw $exception;
        }
    }
    private function validateUserProfileUpdateRequest(UserUpdateProfileRequest $request)
    {
        if ($request->id == null || $request->name == null ||
            trim($request->id) == "" || trim($request->name) == "") {
            throw new ValidationException("Id, Name can not blank");
        }
    }
    public function updatePassword(UserUpdatePasswordRequest $request):UserUpdatePasswordResponse{
        $this->validateUserPasswordUpdateRequest($request);
        try{
            Database::beginTransaction();
            $user=$this->userRepository->findById($request->id);
            if($user==null){
                throw new ValidationException("User is not found");
            }
            if(!password_verify($request->oldPassword,$user->password)){
                throw new ValidationException("Old password is wrong");
            }
            $user->password=password_hash($request->newPassword,PASSWORD_BCRYPT);
            $this->userRepository->update($user);
            Database::commitTransaction();
            $response=new UserUpdatePasswordResponse();
            $response->user=$user;
            return $response;
        }catch(ValidationException $exception){
            Database::rollbackTransaction();
            throw $exception;
        }
    }
    private function validateUserPasswordUpdateRequest(UserUpdatePasswordRequest $request)
    {
        if ($request->id == null || $request->oldPassword == null || $request->newPassword == null ||
            trim($request->id) == "" || trim($request->oldPassword) == "" || trim($request->newPassword) == "") {
            throw new ValidationException("Id, Old Password, New Password can not blank");
        }
    }
    
}