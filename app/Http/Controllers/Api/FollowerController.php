<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Follower;
use App\Models\User;
use Illuminate\Http\Request;

class FollowerController extends Controller
{
    public function followe($username){

        $user = User::where("username",$username)->first();
        if(!$user)
            return response()->json(["success"=>"false","error"=>"user not found"]);
            
        if($username == auth()->user()->username)
          return response()->json(["success"=>"false","error"=>"Cannot Follow Your Self"]);

          $isFollowed = auth()->user()->followers()
          ->where('user_id',auth()->user()->id)
          ->where("followed",$user->id)
          ->exists();

          if(!$isFollowed){
                Follower::create([
                    "user_id"=>auth()->user()->id,
                    "followed"=>$user->id
                ]);
             return response()->json(["success"=>"true","state"=>"follow"]);
          }
          else{
            Follower::where('user_id',auth()->user()->id)
            ->where("followed",$user->id)->delete();
            return response()->json(["success"=>"true","state"=>"unfollow"]);

          }

        
    }
}
