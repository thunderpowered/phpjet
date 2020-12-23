<?php


namespace CloudStore\App\Engine\System;

use CloudStore\CloudStore;

/**
 * Class Tracker
 * @package CloudStore\App\Engine\System
 */
class Tracker
{
    /**
     * @return bool
     */
    public function trackEverythingYouFind()
    {
        $tracker = new \CloudStore\App\Engine\ActiveRecord\Tables\Tracker();
        $tracker->url = CloudStore::$app->router->getURL();
        $tracker->referer = CloudStore::$app->system->request->getSERVER('HTTP_REFERER');
        $tracker->ip = CloudStore::$app->system->request->getUserIP();
        return $tracker->save();
    }
}