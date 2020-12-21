<?php

namespace CloudStore\App\Engine\Controllers;

use CloudStore\App\Engine\Components\S;
use CloudStore\App\Engine\Core\Controller;
use CloudStore\App\Engine\Core\System;

class ControllerBlog extends Controller
{

    private $descr;

    public function actionBasic()
    {

        // WILL BE UPDATED

        $action = Router::getAction();

        // If post
        $post = CloudStore::$app->store->loadOne("blog", ["url" => $action], false);
        if ($post) {
            $this->title = $post["title"];
            $this->descr = $post["snippet"];

            $tags = $this->model->getTags($post);

            $category = CloudStore::$app->store->loadOne("taxonomy", ["type" => "b_cat", "id" => $post["category"]]);
            $categories = CloudStore::$app->store->load("taxonomy", ["type" => "b_cat"]);

            return $this->view->render("blog_post", ["post" => $post, "tags" => $tags, "category" => $category, "categories" => $categories]);
        }

        // Show all list
        // getModel()->getPosts();
        $posts = $this->model->getPosts();

        $categories = CloudStore::$app->store->load("taxonomy", ["type" => "b_cat"]);

        return $this->view->render("blog", ["posts" => $posts, "categories" => $categories]);
    }

    public function actionCategory()
    {

        $action = Router::getRoute()[3];

        $posts = $this->model->getPosts($action);

        $categories = CloudStore::$app->store->load("taxonomy", ["type" => "b_cat"]);
        $category = CloudStore::$app->store->loadOne("taxonomy", ["id" => $action]);

        return $this->view->render("blog", ["posts" => $posts, "categories" => $categories, "category" => $category]);
    }

    public function actionTag()
    {

        $action = Router::getRoute()[3];

        $posts = $this->model->getPostsByTag($action);

        $categories = CloudStore::$app->store->load("taxonomy", ["type" => "b_cat"]);

        $tag = CloudStore::$app->store->loadOne("taxonomy", ["id" => $action]);

        return $this->view->render("blog", ["posts" => $posts, "categories" => $categories, "tag" => $tag]);
    }

    public function SEO()
    {
        return [
            'property' => [
                'og:title' => $this->title,
                'og:description' => $this->descr
            ],
            'name' => [
                'description' => $this->descr
            ]
        ];
    }
}
