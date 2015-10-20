<?php

namespace App\Http\Controllers\Post;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Grab\Post\PostService as PostService;

class PostController extends Controller
{
    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function all() {
        if (session('fb_user_access_token')) {
            $posts = $this->postService->all();
            return response()->json($posts);
        } else {
            return redirect('/');
        }
    }

    public function getPosts($page = 1) {
        $posts = $this->postService->getPosts($page);
        return response()->json($posts);
    }
}
