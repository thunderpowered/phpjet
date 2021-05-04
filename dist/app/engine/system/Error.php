<?php

namespace Jet\App\Engine\System;

use Jet\App\Engine\Config\Config;
use Jet\PHPJet;
use TheSeer\Tokenizer\Exception;

/**
 *
 * Component: ShopEngine Error Handler
 * Description: Catch all errors and notify
 *
 */

/**
 * Class ErrorHandler
 * @package Jet\App\Engine\Components
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

        //set fatal error handler
        register_shutdown_function([$this, 'fatalErrorCatcher']);

        //set exception handler
        set_exception_handler([$this, 'exceptionCatcher']);
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
    }

    /**
     * @param $errno
     * @param $errStr
     * @param $errFile
     * @param $errLine
     * @param $funcName
     * @param null $code
     * @param bool $sendMail
     * @param string $errorID
     */
    public function errorToFile($errno, $errStr, $errFile, $errLine, $funcName, $code = null, $sendMail = false, $errorID = '')
    {
        if ($sendMail) {
            $this->errorToEmail($errno, $errStr, $errFile, $errLine, $funcName);
        }
        $text = "( " . date('Y-m-d H:i:s (T)') . " ) " . ($errorID ? 'ID Ошибки: ' . $errorID . ' | ' : '')
            . "Сработала функция " . $funcName . "; Сбой в работе сайта. Код ошибки/Класс ошибки: " . $errno . "; Информация об ошибке: " . $errStr . "; Файл: " . $errFile . "; Строка: " . $errLine
            . "\r\n";

        // be sure to give a permission
        $errorFile = fopen(ENGINE . $this->logFile, 'a+');
        if ($errorFile) {
            fwrite($errorFile, $text);
            fclose($errorFile);
        }
    }

    /**
     * @param $errno
     * @param $errStr
     * @param $errFile
     * @param $errLine
     * @param $funcName
     */
    public function errorToEmail($errno, $errStr, $errFile, $errLine, $funcName)
    {
        // disabled
        // todo
    }

    /**
     * @param \Exception $exception
     */
    public function exceptionCatcher(\Exception $exception)
    {
        $errorID = $this->generateErrorID();
        if ($exception) {
            $this->errorToFile(get_class($exception), $exception->getMessage(), $exception->getFile(), $exception->getLine(), 'exceptionCatcher', 500, true, $errorID);
        }
        $this->shutDown();
    }

    /**
     *
     */
    public function fatalErrorCatcher(): void
    {
        $errorID = $this->generateErrorID();
        $error = error_get_last();
        if ($error['type'] & (E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR)) {
            $this->errorToFile($error['type'], $error['message'], $error['file'], $error['line'], 'fatalErrorCatcher', 500, true, $errorID);
        }
        $this->shutDown();
    }

    /**
     * @param string $message
     */
    private function shutDown(string $message = ''): void
    {
        if (PHPJet::$app->router) { // because route could not be included in some particular cases
            PHPJet::$app->router->errorPage(500, $message, 'error', true);
        } else {
            PHPJet::$app->exit($message);
        }
    }

    /**
     * @return string
     */
    private function generateErrorID(): string
    {
        return hash('sha256', uniqid('ERROR', true));
    }
}
