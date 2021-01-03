<?php

namespace CloudStore\App\Engine\System;

use CloudStore\App\Engine\Config\Config;
use CloudStore\CloudStore;

/**
 *
 * Component: ShopEngine Request
 * Description: Pretreatment of POST, GET and SESSION.
 * TODO: this class is needed to be refactored
 *
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

    // 60*60*24*30*6 = 180 days = ~half a year;
    private $cookieDefaultExpires = 15552000;

    /**
     * @var string
     * @todo important note: there's Form class that generates forms, should be agreed with it
     */
    private $CSRFTokenKey = '__csrf';

    /**
     * @var bool
     */
    private $CSRFAlreadyChecked = false;
    /**
     * @var string
     */
    private $method;

    // There are some problems with clearing post that contains json string
    // Another temporary solution
    // Only for POST

    /**
     * Request constructor.
     * The main goal was to give a monopoly on the use of this data to this class. But is it necessary?
     */
    public function __construct()
    {
        $this->post = $_POST;
        unset($_POST);

        $this->get = $_GET;
        unset($_GET);

        $this->server = $_SERVER;
        unset($_SERVER);

        // well session and cookie technically available even without this class, but it'd be good to still use it
        $this->session = $_SESSION;
        $this->cookie = $_COOKIE;

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

        $this->method = $this->getSERVER('REQUEST_METHOD');
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
     * @return string|array
     */
    public function getGET(string $name = '', bool $removeSpecialChars = true)
    {
        $result = [];
        if (!$name) {
            $result = $this->get;
        } else {
            if (array_key_exists($name, $this->get)) {
                $result = $this->get[$name];
            }
        }

        if ($removeSpecialChars) {
            $result = CloudStore::$app->tool->utils->removeSpecialChars($result);
        }

        return $result;
    }

    /**
     * @param string $name
     * @param bool $removeSpecialChars
     * @return string|array
     */
    public function getPOST(string $name = '', bool $removeSpecialChars = false)
    {
        if (!$this->post) {
            return [];
        }

        // Since Request owns POST-array, there's no need to check CSRF-token twice
        // If it is already checked we assume that everything is correct
        if (!$this->CSRFAlreadyChecked) {
            if ($this->checkCSRFToken()) {
                return [];
            }
        }

        $result = [];
        if (!$name) {
            $result = $this->post;
        } else {
            if (array_key_exists($name, $this->post)) {
                $result = $this->post[$name];
            }
        }

        if ($removeSpecialChars) {
            $result = CloudStore::$app->tool->utils->removeSpecialChars($result);
        }

        return $result;
    }

    /**
     * @return bool
     * It is also temporary solution
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
     * @param string $name
     * @return mixed
     */
    public function getSERVER(string $name = '')
    {
        if (!$name) {
            return $this->server;
        }
        return $this->server[$name] ?? '';
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
        // temp
        // i wanted to set just property and set actual session on destruction
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
        return $this->server['REMOTE_ADDR'];
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

        return false;
    }

    /**
     * @param string $name
     * @param string $value
     * @param bool $secure
     * @param bool $httpOnly
     * @return bool
     */
    public function setCookie(string $name, string $value = '', bool $secure = true, bool $httpOnly = true)
    {
        return setcookie($name, $value, time() + $this->cookieDefaultExpires, '/', Config::$config['domain'], $secure, $httpOnly);
    }

    /**
     * @param bool $dieOnFalse
     * @return bool
     */
    public function checkCSRFToken(bool $dieOnFalse = false): bool
    {
        $token = $this->post[$this->CSRFTokenKey] ?? null;
        if (!$token || !CloudStore::$app->system->token->validateToken($token)) {
            if ($dieOnFalse) {
                CloudStore::$app->exit('Invalid CSRF-Token.');
            }
            return false;
        }
        $this->CSRFAlreadyChecked = true;
        return true;
    }
}
