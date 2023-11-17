<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PhotoRequest;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
   public function index(){
    $photo = Photo::orderBy('created_at', 'desc')
            ->where("privacy","1")
            ->with('user:id,name,photo,username')
            ->withCount('likes')
            ->with('likes', function($like){
                return $like->where('user_id', auth()->user()->id)
                  ->select('id', 'user_id', 'photo_id')->get();
            })
            ->get();
        $responce = [
            "success" => true,
            'photos' => $photo
        ];
        return response()->json($responce, 200);
   }

   public function follower(){
   
    $followingIds = auth()->user()->followers->pluck('followed');
    $photo = Photo::orderBy('created_at', 'desc')
        ->whereIn('user_id', $followingIds)
        ->where('privacy', '1')
        ->with(['user:id,name,photo,username'])
        ->withCount('likes')
        ->with(['likes' => function ($query) {
            $query->where('user_id', auth()->user()->id)
                ->select('id', 'user_id', 'photo_id');
        }])
        ->get();
    $response = [
        'success' => true,
        'photos' => $photo
    ];

    return response()->json($response, 200);

   }
   public function create(PhotoRequest $request){
        $path = "photos/".auth()->user()->username;
        $image = $request->photo->storePublicly($path, 'public');


        if($image){
            $photo = Photo::create([
                "user_id"=>auth()->user()->id,
                "tags"=>$request->tags,
                "photo"=>$image,
                "privacy"=>$request->privacy
            ]);
            return response()->json([
                "success"=>"true",
                "photo"=>$photo
            ],200);
        }

        return response()->json(['success'=>"false","errors"=>"Cannot Upload Image"]);
        
   }

   public function show($id){
    $photo = Photo::find($id);
    if(!$photo)
         return response()->json(['success'=>"false","errors"=>"Photo Not Found"]);

    $photo = Photo::where("id",$id)->withCount("likes")->with("user:id,name,photo")->first();
    return response()->json(['success'=>"true","photo"=>$photo]);
 
   }

 
   public function delete($id)
   {
       $photo = Photo::find($id);
       if (!$photo) {
           $responce = [
               'success' => false,
               'message' => "Photo not found"
           ];
           return response()->json($responce, 400);
       }

       if ($photo->user_id != auth()->user()->id) {
           $responce = [
               'success' => false,
               'message' => "Permission denied."
           ];
           return response()->json($responce, 400);
       }


       $photo->likes()->delete();
       $photo->delete();

       $responce = [
           'success' => true,
           'message' => "Photo deleted"
       ];
       return response()->json($responce, 400);

   }
}



