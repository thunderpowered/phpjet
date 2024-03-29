<?php

namespace Jet\App\Engine\System;

use Jet\App\Engine\Config\Config;
use Jet\PHPJet;

/**
 * Class Request
 * @package Jet\App\Engine\System
 * @description Pretreatment of POST, GET, SESSION and COOKIE
 */
class Request
{
    /**
     * @var array
     */
    private $post;

    /**
     * @var array
     */
    private $get;

    /**
     * @var array
     */
    private $server;
    /**
     * @var array
     */
    private $session;
    /**
     * @var array
     */
    private $cookie;
    /**
     * @var array
     */
    private $json;
    /**
     * @var array
     */
    private $files;
    /**
     * @var int
     * @description 60*60*24*30*6 = 180 days = ~half a year;
     */
    private $cookieDefaultExpires = 15552000;
    /**
     * @var string
     * todo important note: there's Form class that generates forms, should be agreed with it
     */
    private $CSRFTokenKey = '__csrf';
    /**
     * @var bool
     * @deprecated
     */
    private $CSRFAlreadyChecked = false;
    /**
     * @var string
     */
    private $method;
    /**
     * @var string
     */
    private $sessionIDPrefix = 'pj-';
    /**
     * @var int
     */
    private $sessionAnnihilatedLifeSpan = 60;
    /**
     * @var int
     */
    private $sessionCurrentLifeSpan = 300; // 5 min
    /**
     * @var string
     */
    private $sessionLog = ENGINE . 'session_access.log';

    // There are some problems with clearing post that contains json string
    // Another temporary solution
    // Only for POST

    /**
     * Request constructor.
     * @description The main goal was to preprocess and validate all the data before it gets into Controller
     */
    public function __construct()
    {
        $this->sessionStart();
        // well session and cookie technically available even without this class, but it'd be good to still use it
        $this->session = $_SESSION;
        // don't forget to make sure that this session is active
        $this->checkSessionActive();
        // session has a lifespan, check if it is still alive
        $this->checkSessionAlive();

        $this->cookie = $_COOKIE;

        $this->post = $_POST;
        unset($_POST); // clear $_POST array to force using this class

        $this->get = $_GET;
        unset($_GET); // same

        $this->server = $_SERVER;
        unset($_SERVER); // ...

        $this->files = $_FILES;
        unset($_FILES);

        // Proceed JSON, if JSON exists -> replace POST with it
        $this->json = file_get_contents('php://input');
        $this->json = json_decode($this->json, true);
        if ($this->json) {
            $this->post = $this->json;
        }
        // if JSON is empty, json_decode() returns NULL
        // just to unambiguity convert in to bool
        // and of course if we expect JSON input, use function $this->getJSON()
        $this->json = (bool)$this->json;
    }

    /**
     * Request destructor.
     */
    public function __destruct()
    {
//        $_SESSION = $this->session;
    }

    /**
     * @param string $name
     * @param bool $removeSpecialChars
     * @return array|string|null
     */
    public function getGET(string $name = '', bool $removeSpecialChars = true)
    {
        $result = null;
        if (!$name) {
            $result = $this->get;
        } else {
            if (array_key_exists($name, $this->get)) {
                $result = $this->get[$name];
            }
        }

        if ($removeSpecialChars) {
            $result = PHPJet::$app->tool->utils->removeSpecialChars($result);
        }

        return $result;
    }

    /**
     * @param string $name
     * @param bool $removeSpecialChars
     * @return array|string|null
     */
    public function getPOST(string $name = '', bool $removeSpecialChars = false)
    {
        $result = null;
        if (!$name) {
            $result = $this->post;
        } else {
            if (array_key_exists($name, $this->post)) {
                $result = $this->post[$name];
            }
        }
        if ($result && $removeSpecialChars) {
            $result = PHPJet::$app->tool->utils->removeSpecialChars($result);
        }
        return $result;
    }

    /**
     * @return bool
     * It is also temporary solution
     * @deprecated
     */
    public function testPOST(): bool
    {
        $post = $this->getPOST($this->CSRFTokenKey);
        return (bool)$post;
    }

    /**
     * @param string $name
     * @param bool $removeSpecialChars
     * @return array|string
     */
    public function getJSON(string $name = '', $removeSpecialChars = false)
    {
        if (!$this->json) {
            return [];
        }
        return $this->getPOST($name, $removeSpecialChars);
    }

    /**
     * @param string $fileName
     * @return array
     */
    public function getFile(string $fileName): array
    {
        if (!isset($this->files[$fileName])) {
            return [];
        }
        return $this->files[$fileName];
    }

    /**
     * @param string $name
     * @param bool $removeSpecialChars
     * @return array|mixed|string
     */
    public function getSERVER(string $name = '', bool $removeSpecialChars = true)
    {
        if (!$name) {
            $result = $this->server;
        } else {
            $result = $this->server[$name] ?? '';
        }

        if ($removeSpecialChars) {
            $result = PHPJet::$app->tool->utils->removeSpecialChars($result);
        }

        return $result;
    }

    /**
     * @param string $name
     * @return array|string
     */
    public function getSESSION(string $name = '')
    {
        if (!$name) {
            return $this->session;
        }
        return $this->session[$name] ?? '';
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setSESSION(string $name, $value)
    {
        $_SESSION[$name] = $this->session[$name] = $value;
    }

    /**
     * @param string $name
     */
    public function unsetSESSION(string $name)
    {
        if (isset($this->session[$name])) {
            unset($_SESSION[$name]);
            unset($this->session[$name]);
        }
    }

    /**
     * @param string $sessionKey
     * @deprecated
     */
    public static function eraseFullSession(string $sessionKey = "checkout")
    {
        $session = self::getSession();
        foreach ($session as $key => $value) {
            if (strpos($key, $sessionKey) === 0) {
                unset($_SESSION[$key]);
            }
        }
    }

    /**
     * @return string
     */
    public function getUserIP(): string
    {
        return $this->getSERVER('REMOTE_ADDR');
    }

    /**
     * @return string
     */
    public function getRequestMethod(): string
    {
        return $this->getSERVER('REQUEST_METHOD');
    }

    /**
     * @param string $name
     * @return string|bool
     */
    public function getCookie(string $name)
    {
        if (isset($this->cookie[$name])) {
            return $this->cookie[$name];
        }
        // quick reflection - bool is yes/no datatype, it's suitable for questioning like 'is it so?'.
        // but if we expected to find a thing, but didn't find anything, what should we get?
        return false;
    }

    /**
     * @param string $name
     * @param string $value
     * @param bool $secure
     * @param bool $httpOnly
     * @return bool
     */
    public function setCookie(string $name, string $value = '', bool $secure = true, bool $httpOnly = true): bool
    {
        return setcookie($name, $value, time() + $this->cookieDefaultExpires, '/', Config::$config['domain'], $secure, $httpOnly);
    }

    /**
     * @param bool $dieOnFalse
     * @return bool
     * @deprecated
     * All the csrf-things are deprecated now, class Auth handles it
     */
    public function checkCSRFToken(bool $dieOnFalse = false): bool
    {
        $token = $this->post[$this->CSRFTokenKey] ?? null;
        if (!$token || !PHPJet::$app->system->token->validateToken($token)) {
            if ($dieOnFalse) {
                PHPJet::$app->exit('Invalid CSRF-Token.');
            }
            return false;
        }
        $this->CSRFAlreadyChecked = true;
        return true;
    }

    private function sessionRegenerateId()
    {
        // the main idea is to generate new session key every time changes happen
        // even if the session were hijacked, it will be expired very soon, so doing this way we reduce chance of users's (or admin's) account penetration
        // again, it's not 100% way to do secure accounts, but it makes hijacking harder
        // and yes, i almost copied code from php.net ;)

        // step 0. make sure that session is active
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // step 1: create new session id
        $newSessionID = session_create_id($this->sessionIDPrefix);

        // outdated session, we don't need it
        $this->unsetSESSION('session_created');

        // step 2: push time of regenerating into current session
        $this->setSESSION('session_annihilated', time());

        // step 3: save new session id into old session
        $this->setSESSION('new_session_id', $newSessionID);

        // step 4: generate new session id and keep old session
        session_regenerate_id(false);

        // see? we create new session with new identifier, but old session is still alive!
        // in this session we don't need new id and destroy time
        $this->unsetSESSION('session_annihilated');
        $this->unsetSESSION('new_session_id');

        // and create time of creating to check it later
        $this->setSESSION('session_created', time());
    }

    // i made it to have an ability to call this from different places
    private function setSessionINI()
    {
        ini_set('session.use_strict_mode', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_samesite', 'Strict');
        if (Config::$secure['httpsOnly']) {
            ini_set('session.cookie_secure', 1);
        }
    }

    private function sessionStart()
    {
        $this->setSessionINI();
        session_start();
    }

    /**
     * @return void
     */
    private function checkSessionAlive(): void
    {
        $sessionCreatedTime = $this->getSESSION('session_created');
        if (!$sessionCreatedTime || $sessionCreatedTime < time() - $this->sessionCurrentLifeSpan) {
            // session is outdated
            $this->sessionRegenerateId();
        }
    }

    /**
     * @return void
     */
    private function checkSessionActive(): void
    {
        $sessionAnnihilated = $this->getSESSION('session_annihilated');
        if (!$sessionAnnihilated) {
            // everything is fine, nothing to check
            return;
        }

        // if we are here - something definitely went wrong
        // unstable internet connection may cause this - we changed session_id, but user never got a cookie with this id
        // but we gave it some time, let's just check it
        if ($sessionAnnihilated < time() - $this->sessionAnnihilatedLifeSpan) {
            // that's bad, that's means that someone tried to access outdated session
            // let's record this and restart session
            // again, it may be because of bad connection or something
            $sessionID = session_id();
            $date = date('d.m.Y H:i:s', time());
            $userIP = $this->getUserIP();
            $userAgent = $this->getSERVER('HTTP_USER_AGENT');
            $method = $this->getSERVER('REQUEST_METHOD');
            $details = "[Annihilated Session Access] [Session ID: $sessionID] [Date: $date] [IP: $userIP] [User Agent: $userAgent] [Method: $method]";
            $this->recordSuspiciousAction($details);
            session_destroy();
            session_start();
            return;
        }

        // if cookies are still alive, let's recreate identifier
        $newSessionID = $this->getSESSION('new_session_id');
        if (!$newSessionID) {
            // seems like it is impossible scenario, but i still have to check it
            // as they say, you never know
            return;
        }

        // close session and set new id
        session_commit();
        session_id($newSessionID);

        // everything is fine, let it be
        session_start();
    }

    /**
     * @param string $details
     */
    private function recordSuspiciousAction(string $details)
    {
        $logFile = fopen($this->sessionLog, 'a+');
        if ($logFile) {
            fwrite($logFile, $details . "\r\n");
            fclose($logFile);
        }
    }
}
