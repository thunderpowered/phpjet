<?php


namespace CloudStore\App\Engine\System;

use CloudStore\App\Engine\ActiveRecord\Tables\Tracker_Authority;
use CloudStore\CloudStore;

/**
 * Class Tracker
 * @package CloudStore\App\Engine\System
 */
class Tracker
{
    /**
     * Tracker constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return bool
     */
    public function trackEverythingYouFind()
    {
        $tracker = new \CloudStore\App\Engine\ActiveRecord\Tables\Tracker();
        $tracker->url = CloudStore::$app->router->getURL();
        $tracker->referer = CloudStore::$app->system->request->getSERVER('HTTP_REFERER');
        $tracker->user_agent = CloudStore::$app->system->request->getSERVER('HTTP_USER_AGENT');
        $tracker->ip = CloudStore::$app->system->request->getUserIP();

        // i'm still not sure should i store post-data, we'll see
        $post = CloudStore::$app->system->request->getPOST();
        if ($post) {
            $tracker->post = json_encode($post);
        }
        return $tracker->save();
    }

    /**
     * @param int $adminID
     * @param string $action
     * @param bool $status
     * @param string $explanation
     * @return bool
     */
    public function trackAdminActions(int $adminID, string $action, bool $status, string $explanation = ''): bool
    {
        $tracker = new Tracker_Authority();
        $tracker->url = CloudStore::$app->router->getURL();
        $tracker->authority_id = $adminID;
        $tracker->action = $action;
        $tracker->status = $status;
        $tracker->explanation = $explanation;
        $tracker->ip = CloudStore::$app->system->request->getUserIP();
        $tracker->user_agent = CloudStore::$app->system->request->getSERVER('HTTP_USER_AGENT');
        return $tracker->save();
    }

    /**
     * @return bool
     */
    public function manageTable(): bool
    {
        $SQLString = 'delete from tracker where `datetime` < DATE_SUB(NOW(), interval 3 month)';
        return CloudStore::$app->store->dangerouslySendQueryWithoutPreparation($SQLString);
    }
}