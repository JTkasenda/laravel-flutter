<?php
namespace App\Http\Controllers\Feed;
use App\Models\Feed;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FeedController extends Controller
{
    //

    public function index(){
        $feeds = Feed::with("user")->latest()->get();
        return response([
            "feeds" => $feeds
        ], 200);
    }
    public function store(PostRequest $request)
    {
        $request->validated();

        // dd(auth()->user());

        auth()->user()->feeds()->create([
            "content" => $request->content
        ]);
        return response([
            "message" => "sucess"
        ], 201);
    }

    public function likePost($feed_id)
    {
        $feed = Feed::find($feed_id);

        if (!$feed) {
            return response([
                "message" => "404 Not found",
            ], 404);
        }
        $unlike_post = Like::where("user_id", auth()->user()->id)
                        ->where("feed_id", $feed_id)->delete();
        if($unlike_post){
            return response([
                "message" => "unliked"
            ], 200);
        }
        else{
            Like::create([
                "user_id" =>auth()->user()->id,
                "feed_id" =>$feed_id
            ]);
            return response([
                "message" => "liked"
            ], 201);
        }
    }

    public function CreateComment(Request $request){
      $request->validate([
        "body" => "required",
        "feed_id" => "required|exists:feeds,id"
      ]);
        $comments = Comment::create([
            "user_id" => auth()->user()->id,
            "feed_id" => $request->feed_id,
            "body" => $request->body
        ]);

        return response([
            "message" => "success"
        ], 201);

        
    }

    public function getComments(Request $request){
        $comments = Comment::with(["feed", "user"])->whereFeedId($request->feed_id)->latest()->get();        
        return response([
            "comments" => $comments
        ], 200);
    }
}
