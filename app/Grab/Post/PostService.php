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
        // $this->facebook->setDefaultAccessToken(session('fb_user_access_token'));
    }

    public function get() {
        $feed = $this->facebook->get('/me/feed' . $this->postConfig->getConfig());
        return $feed->getGraphEdge();
    }

    public function all() {
        $response = $this->get();
        $posts = $response->asArray();
            echo '<pre>';
            var_dump($posts);
            exit();
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
                $response = $this->facebook->get($postId . '?fields=' . $requiredPostFields);
                $getPost = $response->getGraphNode()->asArray();
                $grabPosts[] = [
                    'id'          => $getPost['id'],
                    'title'       => $getPost['message'],
                    'description' => (isset($getPost['description'])) ? $getPost['description'] : 'No Description',
                    'date'        => date_format($getPost['created_time'], "(D) M d, Y")
                ];
            }
        }
        return $grabPosts;
    }


    public function getPosts($page) {
        $faker = \Faker\Factory::create();
        $getAllPosts = [];
        $limit = config('grab.post.limit');
        $totalPosts = 100;

        $start = $totalPosts - ($limit * $page) + $limit;
        $end = $totalPosts - ($limit * $page) + 1;

        if ($totalPosts >= ($limit * $page)) {
            for ($i = $start; $i >= $end; $i--) {
                $date = $faker->dateTimeBetween($startDate = '-30 days', $endDate = 'now')->format("(D) M d, Y");
                $getAllPosts[] = [
                    'id'          => $i,
                    'post_id'     => $faker->randomNumber(9),
                    'title'       => $faker->sentence(6),
                    'description' => $faker->text(100),
                    'date'        => $date
                ];
            }
        }
        return $getAllPosts;

    }

}