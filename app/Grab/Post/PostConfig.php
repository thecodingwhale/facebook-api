<?php

namespace App\Grab\Post;

class PostConfig {

    public function getConfig() {
        return $this->getLimit() . $this->getFields();
    }

    private function getLimit() {
        $limit = config('grab.post.limit');
        return '?limit=' . $limit;
    }

    private function getFields() {
        $fields = config('grab.post.fields');
        return '?fields=' . $fields;
    }
}