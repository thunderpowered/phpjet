<?php

namespace CloudStore\App\MVC\Admin\Models;

use CloudStore\App\Engine\ActiveRecord\Tables\Authority;
use CloudStore\App\Engine\Core\Model;
use CloudStore\CloudStore;

/**
 * Class ModelAdmin
 * @package CloudStore\App\MVC\Admin\Models
 */
class ModelAdmin extends Model
{
    /**
     * @var int
     */
    private $defaultHashingAlgorithm = PASSWORD_DEFAULT;
    /**
     * @var string
     */
    private $sessionAuthorizedKey = 'admin_authorized';
    /**
     * @var string
     */
    private $sessionAdminID = 'admin_id';
    /**
     * @var string
     */
    private $sessionFingerprint = 'admin_fingerprint';

    /**
     * ModelAdmin constructor.
     * @param string $name
     */
    public function __construct(string $name = "")
    {
        parent::__construct($name);
    }

    /**
     * @return bool
     */
    public function isAdminAuthorized(): bool
    {
        // basic auth check
        $isAdminAuthorized = CloudStore::$app->system->request->getSESSION($this->sessionAuthorizedKey);
        if (!$isAdminAuthorized) {
            return false;
        }

        // extended auth check
        $fingerprint = $this->getFingerprint();
        $fingerprintSession = CloudStore::$app->system->request->getSESSION($this->sessionFingerprint);
        if (!$fingerprintSession || !$fingerprint || $fingerprintSession !== $fingerprint) {
            return false;
        }

        // everything is fine
        return true;
    }

    /**
     * @param string $password
     * @return string
     */
    private function generatePasswordHash(string $password): string
    {
        return password_hash($password, $this->defaultHashingAlgorithm);
    }

    /**
     * @return string
     */
    private function getFingerprint(): string
    {
        // pretty basic, but i'll extend it later
        $userAgent = CloudStore::$app->system->request->getSERVER('HTTP_USER_AGENT');
        $remoteAddr = CloudStore::$app->system->request->getSERVER('REMOTE_ADDR');
        $adminID = CloudStore::$app->system->request->getSESSION($this->sessionAdminID);
        $adminSessionToken = $this->getAdminByID($adminID)->session_token;

        $fingerprint = $userAgent . $remoteAddr . $adminID . $adminSessionToken;
        return CloudStore::$app->system->token->hashString($fingerprint);
    }

    /**
     * @param int $adminID
     * @return Authority|void
     */
    private function getAdminByID(int $adminID)
    {
        return Authority::getOne(['id' => $adminID]);
    }
}