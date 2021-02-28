<?php


namespace Jet\App\Engine\System;

use Jet\App\Engine\Core\Tables\Tracker_Authority;
use Jet\PHPJet;

/**
 * Class Tracker
 * @package Jet\App\Engine\System
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
        $tracker = new \Jet\App\Engine\Core\Tables\Tracker();
        $tracker->url = PHPJet::$app->router->getURL();
        $tracker->method = PHPJet::$app->system->request->getSERVER('REQUEST_METHOD');
        $tracker->referer = PHPJet::$app->system->request->getSERVER('HTTP_REFERER');
        $tracker->user_agent = PHPJet::$app->system->request->getSERVER('HTTP_USER_AGENT');
        $tracker->ip = PHPJet::$app->system->request->getUserIP();
        $tracker->type = $type;
        $tracker->details = $explanation;

        $post = PHPJet::$app->system->request->getPOST();
        if ($post) {
            $tracker->post = json_encode($post);
        }

        return $tracker->save();
    }

    /**
     * @param int $adminID
     * @param string $action
     * @param bool $status
     * @param string $details
     * @return bool
     */
    public function trackAdminActions(int $adminID, string $action, bool $status, string $details = ''): bool
    {
        $tracker = new Tracker_Authority();
        $tracker->url = PHPJet::$app->router->getURL();
        $tracker->authority_id = $adminID;
        $tracker->action = $action;
        $tracker->status = $status;
        $tracker->details = $details;
        $tracker->ip = PHPJet::$app->system->request->getUserIP();
        $tracker->user_agent = PHPJet::$app->system->request->getSERVER('HTTP_USER_AGENT');

        $post = PHPJet::$app->system->request->getPOST();
        if ($post) {
            $tracker->post = json_encode($post);
        }
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
        return PHPJet::$app->store->dangerouslySendQueryWithoutPreparation($SQLString);
    }
}