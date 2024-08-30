<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function signup(Request $request){

        $validateuser  = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password'=> 'required'
            ] 
        );
        if($validateuser->fails()){
            return response([
                'status'=> false,
                'message' => "Validation Error",
                'errors' => $validateuser->errors()->all()
            ],401);
        }
        $user =  User::Create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,

        ]);
        if($user){
            return response([
                'status'=> true,
                'message' => 'User Created Successfully',
                'user' => $user
            ],200);
        }
    }
    public function login(Request $request){
        $validateuser  = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password'=> 'required'
            ]
        );
        if($validateuser->fails()){
            return response([
                'status'=> false,
                'message' => "Authentication  Fails",
                'errors' => $validateuser->errors()->all()
            ],404);
        }
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $authuser = Auth::user();
            return response([
                'status'=> true,
                'message' => 'user looged in successfully',
                'token' => $authuser->createToken("Api Token")->plainTextToken,
                'token_type' => 'bearer'
            ],200);
        }else{
            return response([
                'status'=> false,
                'message' => "Email & Password not macth.",
            ],401);
        }
    }
    public function logout(Request $request){
        $user = $request->user();
        $user->tokens()->delete();
        return response([
            'status'=> true,
            'user' => $user,
            'message' => 'user looged out successfully'
        ],200);
    }
}
