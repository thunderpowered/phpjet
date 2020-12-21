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

    // 60*60*24*30*6 = 180 days = ~half a year;
    private $cookieDefaultExpires = 15552000;

    // Important!
    // List of ignored session keys in PostToSess function.
    /**
     * @var array
     */
    private static $ignored = [
        "token",
        "csrf",
        "session_token",
        "checkout_token",
        "user_token"
    ];
    /**
     * @var string
     * @todo important note: there's Form class that generates forms, should be agreed with it
     */
    private $CSRFTokenKey = '__csrf';

    // There are some problems with clearing post that contains json string
    // Another temporary solution
    // Only for POST
    /**
     * @var array
     */
    private static $doNotClear = [
        "ControllerAjax"
    ];

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

        // mostly temp, need better solution
        $token = $this->post[$this->CSRFTokenKey];
        if (!CloudStore::$app->system->token->validateToken($token)) {
            return [];
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
     * @param $post
     * @return bool
     * @deprecated
     */
    public static function postToSession($post)
    {
        if (empty($post)) {
            return false;
        }

        foreach ($post as $key => $value) {
            // This can be insecure as SESSION contains CSRF-token.
            // To prevent this action, use next construction:
            $key = trim($key);
            if (in_array($key, self::$ignored)) {
                continue;
            }
            if (is_array($value)) {
                continue;
            }
            try {
                $_SESSION[$key] = CloudStore::$app->tool->utils->removeSpecialChars($value);
            } catch (\Exception $e) {
                // Who cares?..
                continue;
            }
        }

        return true;
    }

    /**
     * @param $name
     * @param $value
     * @return array|string
     * @deprecated
     */
//    public static function setSession($name, $value)
//    {
//        return $_SESSION[$name] = CloudStore::$app->tool->utils->removeSpecialChars($value);
//    }

    /**
     * @deprecated
     */
    public static function postUnset()
    {
        unset($_POST);
    }

    /**
     * @return bool
     * @deprecated
     */
    public static function eraseErrorSession()
    {
        $session = $_SESSION;
        foreach ($session as $key => $value) {
            if (strpos($key, "error") === 0) {
                unset($_SESSION[$key]);
            }
        }

        return true;
    }

    /**
     * @return bool
     * @deprecated
     */
    public static function eraseUserSession()
    {
        $session = self::getSession();
        foreach ($session as $key => $value) {
            if (strpos($key, "user") === 0) {
                unset($_SESSION[$key]);
            }
        }

        return true;
    }

    /**
     * @param null $key
     * @return array|bool|string
     * @deprecated
     */
//    public static function getSession($key = null)
//    {
//        if ($key) {
//            if (array_key_exists($key, ($_SESSION ?? []))) {
//                return CloudStore::$app->tool->utils->removeSpecialChars($_SESSION[$key]);
//            } else {
//                return false;
//            }
//        } else {
//            return $_SESSION;
//        }
//    }

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
     * @param bool $removeSpecialChars
     * @return bool
     */
    private static function checkClear(bool $removeSpecialChars)
    {
        // Of course, if variable is set we don't need to change it
        if ($removeSpecialChars) {
            return $removeSpecialChars;
        }

        $controller = CloudStore::$app->router->getControllerObject()->getName();
        if (in_array($controller, self::$doNotClear)) {
            return false;
        }

        return true;
    }
}
