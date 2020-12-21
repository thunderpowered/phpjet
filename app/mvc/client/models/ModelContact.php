<?php

namespace CloudStore\App\MVC\Client\Models;

use CloudStore\App\Engine\Components\S;
use CloudStore\App\Engine\Core\Model;

class ModelContact extends Model
{

    public function validate()
    {
        $post = \CloudStore\App\Engine\Components\Request::post();

        if (!$post) {
            return true;
        }

        // Name
        if (!$post['contact_name'] OR !$post['contact_email'] OR !$post['contact_phone'] OR !$post['contact_body']) {
            return true;
        }

        return false;
    }

    public function feedback()
    {
        $post = \CloudStore\App\Engine\Components\Request::post();

        if (!$post) {

            return false;
        }

        // let's do it
        $result = CloudStore::$app->store->collect("feedback", [
            "feedback_name" => $post["contact_name"],
            "feedback_email" => $post["contact_email"],
            "feedback_phone" => $post["contact_phone"],
            "feedback_body" => $post["contact_body"]
        ]);

        if ($result) {

            return true;
        }

        return false;
    }
}
