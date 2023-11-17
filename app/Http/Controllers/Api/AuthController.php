<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Request;

class AuthController extends Controller
{
    public function register(RegisterRequest $request){
        $uid = Str::uuid();
        $user = User::create([
            "uid"=>$uid,
            "name"=>$request->name,
            "email"=>$request->email,
            "username"=>$request->username,
            "password"=>Hash::make($request->password),
            "photo"=>""
        ]);
        return response()->json(["success"=>"true","user"=>$user],200);
    }

    public function login(Request $request){
        $form_data = $request->only(["email","password"]);
        
        if(Auth::attempt($form_data)){
            $user = Auth::user();
            // if($user->email_verified_at == null)
            //     return response()->json(['success'=>"false",'errros'=>"The Email is Not Verified"]);
            $token = $user->createToken($user->uid)->plainTextToken;
            $responce = [
                'success'=>true,
                'token'=>$token,
                'user'=>$user,
                'message'=> 'Login success'
            ];
            return response()->json($responce,200);
        }
        $errors = ["Error in Email or Password"];
        return response()->json(["success"=>"false" ,'errors' => $errors], 401);
        
    }

    public function logout(){
       if(Auth::user()){
            auth()->user()->tokens()->delete();
            $responce = [
                'success'=> 'true',
                'message'=> 'Logout Success'
            ];
            return response()->json($responce,200);
       }
       return response()->json(['error'=>"Error"],404);
    
    }
}
