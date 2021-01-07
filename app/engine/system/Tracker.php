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
     * @param string $type
     * @param string $explanation
     * @return bool
     */
    public function trackEverythingYouFind(string $type = '', string $explanation = ''): bool
    {
        $tracker = new \CloudStore\App\Engine\ActiveRecord\Tables\Tracker();
        $tracker->url = CloudStore::$app->router->getURL();
        $tracker->method = CloudStore::$app->system->request->getSERVER('REQUEST_METHOD');
        $tracker->referer = CloudStore::$app->system->request->getSERVER('HTTP_REFERER');
        $tracker->user_agent = CloudStore::$app->system->request->getSERVER('HTTP_USER_AGENT');
        $tracker->ip = CloudStore::$app->system->request->getUserIP();
        $tracker->type = $type;
        $tracker->details = $explanation;

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
    public function trackAdminActions(int $adminID, string $action, bool $status, string $details = ''): bool
    {
        $tracker = new Tracker_Authority();
        $tracker->url = CloudStore::$app->router->getURL();
        $tracker->authority_id = $adminID;
        $tracker->action = $action;
        $tracker->status = $status;
        $tracker->details = $details;
        $tracker->ip = CloudStore::$app->system->request->getUserIP();
        $tracker->user_agent = CloudStore::$app->system->request->getSERVER('HTTP_USER_AGENT');
        return $tracker->save();
    }

    /**
     * @param string $explanation
     * @return bool
     */
    public function recordSuspiciousAction(string $explanation): bool
    {
        return $this->trackEverythingYouFind('Suspicious', $explanation);
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