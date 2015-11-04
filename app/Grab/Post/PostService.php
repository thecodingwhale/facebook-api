<?php

namespace App\Grab\Post;

use SammyK\LaravelFacebookSdk\LaravelFacebookSdk as Facebook;
use App\Grab\Core\BaseService as BaseService;
use App\Grab\Post\PostConfig as PostConfig;

class PostService extends BaseService {

    protected $facebook;
    protected $postConfig;

    public function __construct(
        Facebook $facebook,
        PostConfig $postConfig
    )
    {
        parent::__construct();
        $this->facebook = $facebook;
        $this->postConfig = $postConfig;
        $this->facebook->setDefaultAccessToken(session('fb_user_access_token'));
    }

    public function all() {
        $feeds = $this->facebook->get('/me/feed' . '?limit=10fields=id');
        $feeds = $feeds->getGraphEdge();
        $rawPosts = $feeds->asArray();
        // $params = 'id,caption,description,created_time,link,message,picture,source,story,story_tags,type,updated_time';
        $params = 'id,caption,description,created_time,link,message,source,updated_time';
        foreach ($rawPosts as $post) {
            if (isset($post['story'])) {
                $title = $post['story'];
            }
            if (isset($post['message'])) {
                $title = $post['message'];
            }
            if (strpos($title,'[grab]') !== false) {
                $id = $post['id'];
                $fields = '?fields=' . $params;
                $relative_url = $id . $fields;

                $filteredPosts[] = [
                    'method' => 'GET',
                    'relative_url' => $relative_url
                ];
            }
        }

        $posts = $this->facebook->post('?batch=' . urlencode(json_encode($filteredPosts)));
        $posts = $posts->getGraphObject()->asArray();

        foreach ($posts as $key => $value) {
            $body = json_decode($posts[$key]['body'], true);
            $body['created_time'] = date('Y-m-d', strtotime($body['created_time']));
            $body['updated_time'] = date('Y-m-d', strtotime($body['updated_time']));
            $grabPosts[] = $body;
        }

    }


    public function getPosts($page) {
        $offset = $page - 1;
        $feeds = $this->facebook->get('/me/feed' . '?limit=10&offset=' . $offset . '&fields=id,story,message');
        $feeds = $feeds->getGraphEdge();
        $rawPosts = $feeds->asArray();
        $params = 'id,description,created_time,link,message,source';
        foreach ($rawPosts as $post) {
            if (isset($post['story'])) {
                $title = $post['story'];
            }
            if (isset($post['message'])) {
                $title = $post['message'];
            }
            if (strpos($title,'[grab]') !== false) {
                $id = $post['id'];
                $fields = '?fields=' . $params;
                $relative_url = $id . $fields;

                $filteredPosts[] = [
                    'method' => 'GET',
                    'relative_url' => $relative_url
                ];
            }
        }

        $posts = $this->facebook->post('?batch=' . urlencode(json_encode($filteredPosts)));
        $posts = $posts->getGraphObject()->asArray();

        foreach ($posts as $key => $value) {
            $body = json_decode($posts[$key]['body'], true);
            $body['created_time'] = date('Y-m-d', strtotime($body['created_time']));
            if (isset($body['description'])) {
                $body['description'] = substr($body['description'], 0, 100) . '...';
            }
            $grabPosts[] = $body;
        }

        return $grabPosts;

    }

}