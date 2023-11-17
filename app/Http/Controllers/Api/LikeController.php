<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Photo;

class LikeController extends Controller
{
    public function likeDislike($id)
    {
        $photo = Photo::find($id);
        if(!$photo)
        {
            $responce = [
                "success" => false,
                'photo' => "Photo Not Found."
            ];
            return response()->json($responce, 400);
        }

        $like = $photo->likes()->where('user_id', auth()->user()->id)->first();

        //like
        if(!$like)
        {
            Like::create([
                'photo_id' => $id,
                'user_id' => auth()->user()->id
            ]);

            $responce = [
                "success" => true,
                'photo' => "Liked"
            ];
            return response()->json($responce, 200);
        }
        //dislike
        $like->delete();

        $responce = [
            "success" => true,
            'photo' => "Dislike"
        ];
        return response()->json($responce, 200);
    }
}
