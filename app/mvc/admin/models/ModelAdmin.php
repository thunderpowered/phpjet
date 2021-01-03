<?php

namespace CloudStore\App\MVC\Admin\Models;

use CloudStore\App\Engine\ActiveRecord\ActiveRecord;
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
     * @var string
     */
    private $session2FAuthenticationCode = 'admin_2f_authentication_code';

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
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function authorizeAdmin(string $email, string $password): array
    {
        $emailValid = CloudStore::$app->tool->formatter->validateEmail($email);
        if (!$emailValid) {

            $this->recordActions('Auth', false, 'attempt failed - invalid email.');
            return ['valid' => false];
        }

        $admin = Authority::getOne(['email' => $email], [], [], false);
        if (!$admin) {

            $this->recordActions('Auth', false, 'attempt failed - no such email in Database.');
            return ['valid' => false];
        }

        $passwordCorrect = password_verify($password, $admin->password);
        if (!$passwordCorrect) {

            $this->recordActions('Auth', false, 'attempt failed - wrong password.');
            return ['valid' => false];
        }

        // Everything is fine
        if (!$admin->two_factor_auth) {
            $this->grantAccess($admin);

            $this->recordActions('Auth', true, 'attempt successful - no 2F auth needed.');
            return ['valid' => true, '2F' => false];
        } else {
            $code = $this->start2FAuthentication($admin);
            $this->sendEmailWith2FAuthenticationData($code, $admin);

            $this->recordActions('Auth', true, 'attempt successful - waiting for 2F auth.');
            return ['valid' => true, '2F' => true];
        }
    }


    /**
     * @param Authority $admin
     * @return int
     */
    private function start2FAuthentication(Authority $admin): int
    {
        CloudStore::$app->system->request->setSESSION($this->sessionAdminID, $admin->id);
        CloudStore::$app->system->request->setSESSION($this->sessionFingerprint, $this->getFingerprint());

        $code = $this->generate2FAuthenticationCode();
        $codeHashed = $this->generatePasswordHash($code);
        $admin->session_token = $codeHashed;
        $admin->save();

        return $code;
    }

    /**
     * @param string $verificationCode
     * @return bool
     */
    public function validate2FAuthentication(string $verificationCode): bool
    {
        $adminID = CloudStore::$app->system->request->getSESSION($this->sessionAdminID);
        if (!$adminID) {

            $this->recordActions('Auth', false, '2F verification failed - no such admin in Session.');
            return false;
        }

        $fingerPrint = $this->getFingerprint();
        $fingerPrintSession = CloudStore::$app->system->request->getSESSION($this->sessionFingerprint);
        if (!$fingerPrintSession || $fingerPrint !== $fingerPrintSession) {

            $this->recordActions('Auth', false, '2F verification failed - fingerprint incorrect.');
            return false;
        }

        $admin = Authority::getOne(['id' => $adminID], [], [], false);
        if (!$admin) {

            $this->recordActions('Auth', false, '2F verification failed - no such admin in Database.');
            return false;
        }

        if (!password_verify($verificationCode, $admin->session_token)) {

            $this->recordActions('Auth', false, '2F verification failed - invalid verification code.');
            return false;
        }

        // seems like everything is ok
        $this->recordActions('Auth', true, '2F verification successful - 2f auth completed.');
        $this->grantAccess($admin);
        return true;
    }

    /**
     * @return int
     */
    public function getAdminID(): int
    {
        return (int)CloudStore::$app->system->request->getSESSION($this->sessionAdminID);
    }

    public function logout(): bool
    {
        $adminID = $this->getAdminID();
        if (!$adminID) {

            $this->recordActions('Logout', false, 'attempt failed - admin is already signed off.');
            return false;
        }

        $admin = Authority::getOne(['id' => $adminID], [], [], false);
        if (!$admin) {

            $this->recordActions('Logout', false, 'attempt failed - no such admin in Database.');
            return false;
        }

        $this->forbidAccess($admin);

        $this->recordActions('Logout', true, 'attempt successful - admin signing off completed.');
        return true;
    }

    /**
     * @param string $contextName
     * @return string
     */
    public function getAdminContext(string $contextName): string
    {
        $adminID = $this->getAdminID();
        if (!$adminID || !$contextName) {
            return '';
        }

        return CloudStore::$app->system->settings->getContext($this->getAdminContextKey($contextName, $adminID));
    }

    /**
     * @param string $contextName
     * @param string $data
     * @return bool
     */
    public function setAdminContext(string $contextName, string $data): bool
    {
        $adminID = $this->getAdminID();
        if (!$adminID || !$contextName || $data) {
            return false;
        }

        return CloudStore::$app->system->settings->setContext($this->getAdminContextKey($contextName, $adminID), $data);
    }

    /**
     * @param string $action
     * @param bool $status
     * @param string $explanation
     * @return bool
     */
    public function recordActions(string $action, bool $status, string $explanation = ''): bool
    {
        $adminID = (int)$this->getAdminID();
        return CloudStore::$app->system->tracker->trackAdminActions($adminID, $action, $status, $explanation);
    }

    /**
     * @param string $contextName
     * @param int $adminID
     * @return string
     */
    private function getAdminContextKey(string $contextName, int $adminID): string
    {
        return 'admin' . $adminID . '_context__' . $contextName;
    }

    /**
     * @param int $code
     * @param Authority $admin
     * @return bool
     */
    private function sendEmailWith2FAuthenticationData(int $code, Authority $admin): bool
    {
        // todo
        // it's temporary just to see
        $filename = ENGINE . 'emailCode.txt';
        file_put_contents($filename, $admin->email . ' - ' . $code);
        return true;
    }

    /**
     * @param Authority $admin
     */
    private function forbidAccess(Authority $admin)
    {
        CloudStore::$app->system->request->unsetSESSION($this->sessionAuthorizedKey);
        CloudStore::$app->system->request->unsetSESSION($this->sessionAdminID);
        CloudStore::$app->system->request->unsetSESSION($this->sessionFingerprint);
    }

    /**
     * @param Authority $admin
     */
    private function grantAccess(Authority $admin)
    {
        CloudStore::$app->system->request->setSESSION($this->sessionAuthorizedKey, true);
        CloudStore::$app->system->request->setSESSION($this->sessionAdminID, $admin->id);
        CloudStore::$app->system->request->setSESSION($this->sessionFingerprint, $this->getFingerprint());

        $admin->last_login = CloudStore::$app->store->now();
        $admin->session_token = '';
        $admin->save();
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

        $fingerprint = $userAgent . $remoteAddr . $adminID;
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

    /**
     * @return int
     */
    private function generate2FAuthenticationCode(): int
    {
        return rand(111111, 999999);
    }
}