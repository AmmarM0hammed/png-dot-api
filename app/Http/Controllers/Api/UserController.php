<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests\UserRequest;
use App\Models\Follower;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function profile($username)
    {
        $user = User::where("username", $username)->first();
        if (!$user) 
            return response()->json(["success" => "false", "error" => "User Not Found"], 400);
        
        $user = User::where("username", $username)
            ->with([
                'photos' => function ($query) use ($username) {
                    $query->withCount("likes");
                    if (auth()->user()->username != $username) {
                        $query->where('privacy', 1);
                    }
                },
                'photos.likes' => function ($query) {
                    $query->where('user_id', auth()->user()->id)
                        ->select('id', 'user_id', 'photo_id');
                },
                
            ])
            ->get();
    
        return response()->json(["success" => "true", "user" => $user], 200);
    }
    
    public function followers($username) {
        $user = User::where("username", $username)->first();
        if (!$user) 
            return response()->json(["success" => "false", "error" => "User Not Found"], 400);
        
        $followersCount = Follower::where('followed', $user->id)->count();
    
        $isFollowedByUser = Follower::where('followed', $user->id)
        ->where('user_id', auth()->user()->id)
        ->exists();
        
        return response()->json(["success" => "true", "followers" => [
            "isFollowed"=>$isFollowedByUser,'followers_count'=>$followersCount
            ]], 200);
        
    }
    public function update(UserRequest $request)
    {
        if(!Hash::check($request->old_password, auth()->user()->password))
            return response()->json(["success"=>"false","error"=>"Wrong Password"]);

        $image = null;
        if($request->photo){
            $path = "profileimage/";
            $image = $request->photo->storePublicly($path, 'public');
        }
        $user =Auth::user();
        $user->name = $request->name;
        $user->password =Hash::make($request->password);
        $user->photo = ($image)?$image:null;
        $user->save();

        return response([
            'message' => 'User updated.',
            'user' => auth()->user()
        ], 200);
    }

    public function check_follower($username){
        $user = User::where("username",$username)->first();
        if(!$user)
            return response()->json(["success"=>"false","errro"=>"User Not Found"],400);
    
            $isFollowing = auth()->user()->followers()
            ->where('user_id',auth()->user()->id)
            ->where("followed",$user->id)
            ->exists();

        return response()->json(["success" => "true", "isFollowing" => $isFollowing], 200);

    }

    
}
