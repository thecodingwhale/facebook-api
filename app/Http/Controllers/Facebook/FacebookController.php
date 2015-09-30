<?php

namespace App\Http\Controllers\Facebook;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use SammyK\LaravelFacebookSdk\LaravelFacebookSdk as Facebook;
use Session;

class FacebookController extends Controller
{
    protected $facebook;

    public function __construct(Facebook $facebook)
    {
        $this->facebook = $facebook;   
    }

    public function login()
    {
        // Send an array of permissions to request
        $permissions = ['email', 'user_posts'];
        $login_url = $this->facebook->getLoginUrl($permissions);

        // Obviously you'd do this in blade :)
        echo '<a href="' . $login_url . '">Login with Facebook</a>';
    }

    public function callback()
    {

        // Obtain an access token.
        try {
            $token = $this->facebook->getAccessTokenFromRedirect();
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            dd($e->getMessage());
        }

        // Access token will be null if the user denied the request
        // or if someone just hit this URL outside of the OAuth flow.
        if (! $token) {
            // Get the redirect helper
            $helper = $this->facebook->getRedirectLoginHelper();

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
            $oauth_client = $this->facebook->getOAuth2Client();

            // Extend the access token.
            try {
                $token = $oauth_client->getLongLivedAccessToken($token);
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                dd($e->getMessage());
            }
        }

        $this->facebook->setDefaultAccessToken($token);

        // Save for later
        Session::put('fb_user_access_token', (string) $token);

        return redirect('/#/');
    }

}