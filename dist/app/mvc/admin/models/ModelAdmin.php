<?php

namespace Jet\App\MVC\Admin\Models;

use Exception;
use Jet\App\Database\Authority;
use Jet\App\Engine\Core\Model;
use Jet\App\Engine\Interfaces\ModelResponse;
use Jet\PHPJet;
use stdClass;

/**
 * Class ModelAdmin
 * @package Jet\App\MVC\Admin\Models
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
     * @var string
     */
    private $contextKeyWallpaper = 'wallpaper';
    /**
     * @var string
     */
    private $contextPanelState = 'panel_state';
    /**
     * @var string
     */
    private $contextDefaultWindowID = 'default_window_id';
    /**
     * @var array
     */
    private $panelStates = [
        'window', 'classic'
    ];
    /**
     * @var array
     * @todo actually it is possible to automate this
     */
    private $adminAPIUrls = [
        'getTime' => '/misc/getTime',
        'setWallpaper' => '/misc/setWallpaper',
        'getWallpaper' => '/misc/getWallpaper',
        'setPanelMode' => '/misc/setMode',
        'getPanelMode' => '/misc/getMode',
        'setDefaultWindow' => '/misc/setDefaultWindow',
        'getDefaultWindow' => '/misc/getDefaultWindow',
        'loadPages' => '/pages/loadPages',
        'loadPage' => '/pages/loadPage',
        'getAdminActions' => '/statistics/getAdminActions',
        'loadPageBuilder' => '/pages/loadPageBuilder',
        'savePage' => '/pages/savePage'
    ];
    /**
     * @var string
     */
    private $urlTokenURLKey;
    /**
     * @var string
     */
    private $urlTokenSessionKey;

    /**
     * ModelAdmin constructor.
     * @param string $name
     * @param string $urlTokenURLKey
     * @param string $urlTokenSessionKey
     */
    public function __construct(string $name = "", string $urlTokenURLKey = '', string $urlTokenSessionKey = '')
    {
        parent::__construct($name);
        $this->urlTokenURLKey = $urlTokenURLKey;
        $this->urlTokenSessionKey = $urlTokenSessionKey;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function validateAdmin(int $id): bool
    {
        // just check whether or not passed id is current authorized admin id
        $adminId = $this->getAdminID();
        return $id && $adminId && $id === $adminId;
    }

    /**
     * @return ModelResponse
     */
    public function isAdminAuthorized(): ModelResponse
    {
        // basic auth check
        $isAdminAuthorized = PHPJet::$app->system->request->getSESSION($this->sessionAuthorizedKey);
        if (!$isAdminAuthorized) {
            return new ModelResponse(false);
        }

        // extended auth check
        $fingerprint = $this->getFingerprint();
        $fingerprintSession = PHPJet::$app->system->request->getSESSION($this->sessionFingerprint);
        if (!$fingerprintSession || !$fingerprint || $fingerprintSession !== $fingerprint) {
            return new ModelResponse(false);
        }
        // everything is fine
        return new ModelResponse(true, '', [
            'id' => $this->getAdminID()
        ]);
    }

    /**
     * @param string $email
     * @param string $password
     * @return ModelResponse
     */
    public function authorizeAdmin(string $email, string $password): ModelResponse
    {
        $admin = Authority::getOne(['email' => $email], [], [], false);
        if (!$admin) {
            $this->recordActions('Auth', false, 'attempt failed - no such email in Database.');
            return new ModelResponse(false, 'no such email in database');
        }

        $passwordCorrect = password_verify($password, $admin->password);
        if (!$passwordCorrect) {
            $this->recordActions('Auth', false, 'attempt failed - wrong password.');
            return new ModelResponse(false, 'wrong password');
        }

        if (!$admin->two_factor_auth) {
            $urls = $this->grantAccess($admin, $this->urlTokenURLKey, $this->urlTokenSessionKey);
            $this->recordActions('Auth', true, 'attempt successful - no 2F auth needed.');
            return new ModelResponse(true, 'successfully authorized', ['action' => null, 'id' => $admin->id]);
        } else {
            $code = $this->start2FAuthentication($admin);
            $this->sendEmailWith2FAuthenticationData($code, $admin);
            $this->recordActions('Auth', true, 'attempt successful - waiting for 2F auth.');
            return new ModelResponse(true, 'verification required', ['action' => '2F']);
        }
    }


    /**
     * @param Authority $admin
     * @return int
     */
    private function start2FAuthentication(Authority $admin): int
    {
        PHPJet::$app->system->request->setSESSION($this->sessionAdminID, $admin->id);
        PHPJet::$app->system->request->setSESSION($this->sessionFingerprint, $this->getFingerprint());

        $code = $this->generate2FAuthenticationCode();
        $codeHashed = $this->generatePasswordHash($code);
        $admin->session_token = $codeHashed;
        $admin->save();

        return $code;
    }

    /**
     * @param string $verificationCode
     * @return ModelResponse
     */
    public function validate2FAuthentication(string $verificationCode): ModelResponse
    {
        $adminID = PHPJet::$app->system->request->getSESSION($this->sessionAdminID);
        if (!$adminID) {

            $this->recordActions('Auth', false, '2F verification failed - no such admin in Session.');
            return new ModelResponse(false);
        }

        $fingerPrint = $this->getFingerprint();
        $fingerPrintSession = PHPJet::$app->system->request->getSESSION($this->sessionFingerprint);
        if (!$fingerPrintSession || $fingerPrint !== $fingerPrintSession) {
            $this->recordActions('Auth', false, '2F verification failed - fingerprint incorrect.');
            return new ModelResponse(false);
        }

        $admin = Authority::getOne(['id' => $adminID], [], [], false);
        if (!$admin) {
            $this->recordActions('Auth', false, '2F verification failed - no such admin in Database.');
            return new ModelResponse(false);
        }

        if (!password_verify($verificationCode, $admin->session_token)) {
            $this->recordActions('Auth', false, '2F verification failed - invalid verification code.');
            return new ModelResponse(false);
        }

        // seems like everything is ok
        $this->recordActions('Auth', true, '2F verification successful - auth completed.');
        $urls = $this->grantAccess($admin, $this->urlTokenURLKey, $this->urlTokenSessionKey);
        return new ModelResponse(true, '', ['id' => $admin->id]);
    }

    /**
     * @return int
     */
    public function getAdminID(): int
    {
        return (int)PHPJet::$app->system->request->getSESSION($this->sessionAdminID);
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

        $this->recordActions('Logout', true, 'attempt successful - admin signing off completed.');
        $this->forbidAccess();
        return true;
    }

    /**
     * @param int $adminId
     * @param string $settings
     * @return ModelResponse
     */
    public function getAdminSettings(int $adminId, string $settings): ModelResponse
    {
        switch ($settings) {
            // todo maybe store it all together? It'd be easier to work with
            case 'appearance':
                // wallpaper (just because this is cool)
                $wallpaper = $this->getAdminContext($this->contextKeyWallpaper, $adminId);
                if ($wallpaper) {
                    $wallpaper = PHPJet::$app->tool->utils->getImageLink($wallpaper);
                }
                // since
                $panelMode = $this->getAdminContext($this->contextPanelState, $adminId);
                return new ModelResponse(true, '', [
                    'wallpaper' => $wallpaper,
                    'panelMode' => $panelMode
                ]);
            default:
                return new ModelResponse(false, 'unknown settings key');
        }
    }

    /**
     * @param string $contextName
     * @param int $adminID
     * @return string
     */
    public function getAdminContext(string $contextName, int $adminID): string
    {
        return PHPJet::$app->system->settings->getContext($this->getAdminContextKey($contextName, $adminID));
    }

    /**
     * @param string $contextName
     * @param string $data
     * @return bool
     */
    public function setAdminContext(string $contextName, string $data): bool
    {
        $adminID = $this->getAdminID();
        if (!$adminID || !$contextName || !$data) {
            return false;
        }

        return PHPJet::$app->system->settings->setContext($this->getAdminContextKey($contextName, $adminID), $data);
    }

    /**
     * @param string $action
     * @param bool $status
     * @param string $explanation
     * @deprecated
     */
    public function recordActions(string $action, bool $status, string $explanation = ''): void
    {
        return; // disabled until i figure out whether it is necessary or not

//        $adminID = (int)$this->getAdminID();
//        return PHPJet::$app->system->tracker->trackAdminActions($adminID, $action, $status, $explanation);
    }

    /**
     * @return string
     */
    public function getAdminWallpaper(): string
    {
        $wallpaper = $this->getAdminContext($this->contextKeyWallpaper);
        if (!$wallpaper) {
            return '';
        }
        return PHPJet::$app->tool->utils->getImageLink($wallpaper);
    }

    /**
     * @param array $file
     * @return bool
     */
    public function setAdminWallpaper(array $file): string
    {
        $currentWallpaper = $this->getAdminContext($this->contextKeyWallpaper);
        $filePath = PHPJet::$app->tool->fileManager->saveNewFile('images/admin/wallpapers', $file);
        if (!$filePath) {
            return '';
        }

        $result = $this->setAdminContext($this->contextKeyWallpaper, $filePath);
        if (!$result) {
            return '';
        }

        PHPJet::$app->tool->fileManager->deleteFile($currentWallpaper);
        return PHPJet::$app->tool->utils->getImageLink($filePath);
    }

    /**
     * @param string $state
     * @return bool
     */
    public function setPanelState(string $state): bool
    {
        if (!in_array($state, $this->panelStates)) {
            return false;
        }

        $result = $this->setAdminContext($this->contextPanelState, $state);
        if (!$result) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getPanelState(): string
    {
        $panelMode = $this->getAdminContext($this->contextPanelState);
        if (!$panelMode) {
            // default
            $panelMode = $this->panelStates[0];
        }
        return $panelMode;
    }

    /**
     * @param int $defaultWindowID
     * @return bool
     */
    public function setDefaultWindow(int $defaultWindowID): bool
    {
        return $this->setAdminContext($this->contextDefaultWindowID, 'id_' . $defaultWindowID);
    }

    /**
     * @return int
     */
    public function getDefaultWindow(): int
    {
        $defaultWindow = $this->getAdminContext($this->contextDefaultWindowID);
        if (!$defaultWindow) {
            return -1;
        }

        return str_replace('id_', '', $defaultWindow);
    }

    /**
     * @param int $limit
     * @return array
     */
    public function getAdminActions(int $limit = 1000): array
    {
        $actions = Tracker_Authority::get([], ['id' => 'DESC'], [0, $limit]);
        foreach ($actions as $key => $action) {
            $actions[$key]->status = $action->status ? 'Success' : 'Fail';
            $actions[$key]->authority_id = $action->authority_id ? $action->authority_id : 'Not authorized';
            $actions[$key]->datetime = date("d.m.Y H:i:s", strtotime($action->datetime));
            if ($action->post) {
                // since Store automatically removes special chars from data
                $action->post = PHPJet::$app->tool->utils->revertRemoveSpecialCart($action->post);
                $action->post = json_decode($action->post, true);
                $action->post = PHPJet::$app->tool->formatter->arrayToListString($action->post);
                $action->post = PHPJet::$app->tool->utils->removeSpecialChars($action->post);
            }
        }
        return $actions;
    }

    /**
     * @param bool $includeToken
     * @param string $token
     * @param string $tokenURLKey
     * @return array
     * @deprecated
     */
    public function getAdminAPIUrls(bool $includeToken = true, string $token = '', string $tokenURLKey = 'token'): array
    {
        if (!$includeToken) {
            return $this->adminAPIUrls;
        }

        if (!$token) {
            $token = PHPJet::$app->system->request->getSESSION($this->urlTokenSessionKey);
        }

        if (!$tokenURLKey) {
            $tokenURLKey = $this->urlTokenURLKey;
        }

        $result = [];
        foreach ($this->adminAPIUrls as $key => $url) {
            $result[$key] = $url . "/?$tokenURLKey=$token";
        }
        return $result;
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
    private function forbidAccess()
    {
        PHPJet::$app->system->request->unsetSESSION($this->sessionAuthorizedKey);
        PHPJet::$app->system->request->unsetSESSION($this->sessionAdminID);
        PHPJet::$app->system->request->unsetSESSION($this->sessionFingerprint);
    }

    /**
     * @param Authority $admin
     * @param string $urlTokenURLKey
     * @param string $urlTokenSessionKey
     * @return array|string[]
     */
    private function grantAccess(Authority $admin, string $urlTokenURLKey, string $urlTokenSessionKey)
    {
        $token = PHPJet::$app->system->token->generateRandomString();
        PHPJet::$app->system->request->setSESSION($this->sessionAuthorizedKey, true);
        PHPJet::$app->system->request->setSESSION($this->sessionAdminID, $admin->id);
        PHPJet::$app->system->request->setSESSION($this->sessionFingerprint, $this->getFingerprint());
        PHPJet::$app->system->request->setSESSION($urlTokenSessionKey, $token);

        $admin->last_login = PHPJet::$app->store->now();
        $admin->session_token = '';
        $admin->save();

        return $this->getAdminAPIUrls(true, $token, $urlTokenURLKey);
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
        $userAgent = PHPJet::$app->system->request->getSERVER('HTTP_USER_AGENT');
        $remoteAddr = PHPJet::$app->system->request->getSERVER('REMOTE_ADDR');
        $originatedIP = PHPJet::$app->system->request->getSERVER('HTTP_X_FORWARDED_FOR');
        $adminID = $this->getAdminID();

        $fingerprint = $userAgent . $remoteAddr . $originatedIP . $adminID;
        return PHPJet::$app->system->token->hashString($fingerprint);
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