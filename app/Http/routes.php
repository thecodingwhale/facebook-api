<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('index');
});

Route::group(['prefix' => 'facebook'], function () {
    Route::get('login', 'Facebook\FacebookController@login');
    Route::get('callback', 'Facebook\FacebookController@callback');
});

Route::get('/facebook/posts', function(SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb){
    $token = session('fb_user_access_token');
    if ($token) {
        $fb->setDefaultAccessToken($token);
        $response = $fb->get('/me/feed?limit=99999?fields?id');
        $posts = $response->getGraphEdge()->asArray();
        $grabPosts = [];
        $requiredPostFields = 'id,caption,description,created_time,link,message,picture,source,story,story_tags,type,updated_time';
        foreach ($posts as $key => $post) {
            if (isset($post['story'])) {
                $title = $post['story'];
            }
            if (isset($post['message'])) {
                $title = $post['message'];
            }
            $postId = $post['id'];
            if (strpos($title,'[grab]') !== false) {
                $response = $fb->get($postId . '?fields=' . $requiredPostFields);
                $getPost = $response->getGraphNode()->asArray();
                // $grabPosts[] = $getPost;
                $grabPosts[] = [
                    'id'          => $getPost['id'],
                    'title'       => $getPost['message'],
                    'description' => (isset($getPost['description'])) ? $getPost['description'] : 'No Description',
                    'date'        => date_format($getPost['created_time'], "(D) M d, Y")
                ];
            }           
        }
        return response()->json($grabPosts);
    } else {
        return redirect('/');
    }
});