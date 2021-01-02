<?php

namespace CloudStore\App\Engine\System;
use CloudStore\App\Engine\Config\Config;
use CloudStore\CloudStore;
use TheSeer\Tokenizer\Exception;

/**
 *
 * Component: ShopEngine Error Handler
 * Description: Catch all errors and notify
 *
 */

/**
 * Class ErrorHandler
 * @package CloudStore\App\Engine\Components
 */
class Error
{
    /**
     * @var string
     */
    private $logFile = "errors.log";

    /**
     * ErrorHandler constructor.
     */
    public function __construct()
    {
        ob_start();

        //track all errors
        ini_set('display_errors', 1);
        error_reporting(E_ALL | E_STRICT);

        //set basic error handler
        set_error_handler([$this, 'errorCatcher']);

        //set exception handler
        set_exception_handler([$this, 'exceptionCatcher']);

        //set fatal error handler
        register_shutdown_function([$this, 'fatalErrorCatcher']);
    }

    /**
     * @param $errno
     * @param $errStr
     * @param $errFile
     * @param $errLine
     */
    public function errorCatcher($errno, $errStr, $errFile, $errLine): void
    {
        if (error_reporting()) {
            $this->errorToFile($errno, $errStr, $errFile, $errLine, 'errorCatcher');
        }
//        CloudStore::$app->exit();
    }

    /**
     * @param $errno
     * @param $errStr
     * @param $errFile
     * @param $errLine
     * @param $funcName
     * @param null $code
     * @param bool $sendMail
     */
    public function errorToFile($errno, $errStr, $errFile, $errLine, $funcName, $code = null, $sendMail = false)
    {
        if ($sendMail) {
            $this->errorToEmail($errno, $errStr, $errFile, $errLine, $funcName);
        }

        header("HTTP/1.1 $code");

        $text = "( " . date('Y-m-d H:i:s (T)') . " ) Сработала функция " . $funcName . "; Сбой в работе сайта. Код ошибки/Класс ошибки: " . $errno . "; Информация об ошибке: " . $errStr . "; Файл: " . $errFile . "; Строка: " . $errLine . "\r\n";

        // TODO: disable before release
        if (!empty($_GET['debug'])) {
            echo $text . "<hr>";
        }

        // be sure to give a permission
        $errorFile = fopen(ENGINE . $this->logFile, 'a+');
        if ($errorFile) {
            fwrite($errorFile, $text);
            fclose($errorFile);
        }

        if ($sendMail) {
            $this->errorToEmail($errno, $errStr, $errFile, $errLine, $funcName);
        }
    }

    /**
     * @param $errno
     * @param $errStr
     * @param $errFile
     * @param $errLine
     * @param $funcName
     * @throws \phpmailerException
     */
    public function errorToEmail($errno, $errStr, $errFile, $errLine, $funcName)
    {
        // disabled
        return;

        $from = Config::$config['service_email'];
        $to = Config::$config['developer_email'];
        $subject = 'Ошибка на сайте';
        $text = "( " . date('Y-m-d H:i:s (T)') . " ) Сработала функция " . $funcName . "; Сбой в работе сайта. Код ошибки/Класс ошибки: " . $errno . "; Информация об ошибке: " . $errStr . "; Файл: " . $errFile . "; Строка: " . $errLine . "\r\n";

        CloudStore::$app->system->mail->sendMail($to, $from, $subject, $text);
    }

    /**
     * @param \Exception $e
     */
    public function exceptionCatcher($e)
    {
        $this->errorToFile(get_class($e), $e->getMessage(), $e->getFile(), $e->getLine(), 'exceptionCatcher', 500, true);
        echo CloudStore::$app->router->errorPage500();
        CloudStore::$app->exit();
    }

    /**
     *
     */
    public function fatalErrorCatcher(): void
    {
        if ($error = error_get_last() AND $error['type'] & (E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR)) {
            //ob_end_clean();
            $this->errorToFile($error['type'], $error['message'], $error['file'], $error['line'], 'fatalErrorCatcher', 500, true);
            echo CloudStore::$app->router->errorPage500();
        }
    }
}
