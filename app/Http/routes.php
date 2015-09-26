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
                $response = $fb->get('1892530984305640_1896491710576234?fields=' . $requiredPostFields);
                $getPost = $response->getGraphNode()->asArray();
                $grabPosts[] = $getPost;
            }
        }
        return response()->json($grabPosts);
    } else {
        return redirect('/');
    }
});

Route::get('/facebook/login', function (SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb) {
    // Send an array of permissions to request
    $permissions = ['email', 'user_posts'];
    $login_url = $fb->getLoginUrl($permissions);

    // Obviously you'd do this in blade :)
    echo '<a href="' . $login_url . '">Login with Facebook</a>';
});

// Endpoint that is redirected to after an authentication attempt
Route::get('/facebook/callback', function(SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb)
{
    // Obtain an access token.
    try {
        $token = $fb->getAccessTokenFromRedirect();
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        dd($e->getMessage());
    }

    // Access token will be null if the user denied the request
    // or if someone just hit this URL outside of the OAuth flow.
    if (! $token) {
        // Get the redirect helper
        $helper = $fb->getRedirectLoginHelper();

        if (! $helper->getError()) {
            abort(403, 'Unauthorized action.');
        }

        // User denied the request
        dd(
            $helper->getError(),
            $helper->getErrorCode(),
            $helper->getErrorReason(),
            $helper->getErrorDescription()
        );
    }

    if (! $token->isLongLived()) {
        // OAuth 2.0 client handler
        $oauth_client = $fb->getOAuth2Client();

        // Extend the access token.
        try {
            $token = $oauth_client->getLongLivedAccessToken($token);
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            dd($e->getMessage());
        }
    }

    $fb->setDefaultAccessToken($token);

    // Save for later
    Session::put('fb_user_access_token', (string) $token);

    // // Get basic info on the user from Facebook.
    // try {
    //     $response = $fb->get('/me?fields=id,name,email');
    // } catch (Facebook\Exceptions\FacebookSDKException $e) {
    //     dd($e->getMessage());
    // }

    // // Convert the response to a `Facebook/GraphNodes/GraphUser` collection
    // $facebook_user = $response->getGraphUser();

    // // Create the user if it does not exist or update the existing entry.
    // // This will only work if you've added the SyncableGraphNodeTrait to your User model.
    // $user = App\User::createOrUpdateGraphNode($facebook_user);

    // // Log the user into Laravel
    // Auth::login($user);

    return redirect('/#/');
});

Route::get('/users', function () {
    $userLists = [[
        'name' => 'aldren reales terante',
        'role' => 'father'
    ],[
        'name' => 'marlissa perlas terante',
        'role' => 'mother'
    ],[
        'name' => 'jax perlas terante',
        'role' => 'eldest son'
    ],[
        'name' => 'zoe perlas terante',
        'role' => 'youngest daughter'
    ]];
    return response()->json($userLists);
});
