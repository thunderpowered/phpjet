<?php


namespace Jet\App\MVC\Admin\Controllers;

use Jet\App\Engine\Interfaces\ViewResponse;
use Jet\App\MVC\Admin\Models\ModelAdmin;
use Jet\PHPJet;

/**
 * Class ControllerAuth
 * @package Jet\App\MVC\Admin\Controllers
 */
class ControllerAuth extends ControllerAdmin
{
    /**
     * @var ModelAdmin
     */
    protected $modelAdmin;
    /**
     * @var array
     */
    protected $methods = [
        'POST'
    ];
    /**
     * @var string
     */
    private $jsonEmailField = 'email';
    /**
     * @var string
     */
    private $jsonPasswordField = 'password';
    /**
     * @var string
     */
    private $json2FVerificationField = 'verification';

    /**
     * ControllerAuth constructor.
     * @param string $name
     */
    public function __construct(string $name = "")
    {
        parent::__construct($name, true);
        $this->modelAdmin = new ModelAdmin('admin', $this->urlTokenURLKey, $this->urlTokenSessionKey);
    }

    /**
     * @return string
     */
    public function actionCheckGET(): string
    {
        $isAdminAuthorized = $this->modelAdmin->isAdminAuthorized();
        // Note: status field is for fetch2 function which will shout out the message
        // So don't use it always if something went wrong -> unable to load important data, write data to DB or something
        // Like in this case -> everything is fine, we checked, so status is true
        PHPJet::$app->tool->JSONOutput->setStatusTrue();
        if ($isAdminAuthorized) {
            $urls = $this->modelAdmin->getAdminAPIUrls(true);
            PHPJet::$app->tool->JSONOutput->setData([
                'auth' => true,
                'urls' => $urls
            ]);
        } else {
            PHPJet::$app->tool->JSONOutput->setData([
                'auth' => false
            ]);
        }

        return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
    }

    /**
     * @return string
     */
    public function actionBasicPOST(): string
    {
        $json = PHPJet::$app->system->request->getJSON();
        PHPJet::$app->tool->JSONOutput->setAction('1F'); // actions: [1F, 2F, S]
        if (!$json || empty($json[$this->jsonEmailField]) || empty($json[$this->jsonPasswordField])) {
            PHPJet::$app->tool->JSONOutput->setStatusFalse();
            PHPJet::$app->tool->JSONOutput->setMessageBoxText('No data');

            $this->modelAdmin->recordActions('Auth', false, 'attempt failed - no data.');
            return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
        }

        // Following actions recorded in Model
        $email = $json[$this->jsonEmailField];
        $password = $json[$this->jsonPasswordField];
        $result = $this->modelAdmin->authorizeAdmin($email, $password);
        if (!$result['valid']) {
            PHPJet::$app->tool->JSONOutput->setStatusFalse();
            PHPJet::$app->tool->JSONOutput->setMessageBoxText('Wrong login or password.');

            return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
        }

        if (!$result['2F']) {
            return $this->returnSuccessfulAuthorizationMessage($result);
        } else {
            PHPJet::$app->tool->JSONOutput->setStatusTrue();
            PHPJet::$app->tool->JSONOutput->setMessageBoxText('We have sent you email with verification code.');
            PHPJet::$app->tool->JSONOutput->setAction('2F');
            return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
        }
    }

    /**
     * @return string
     */
    public function actionLogoutPOST(): string
    {
        // Following actions record in Model
        $result = $this->modelAdmin->logout();
        if (!$result) {
            PHPJet::$app->tool->JSONOutput->setStatusFalse();
            PHPJet::$app->tool->JSONOutput->setMessageBoxText('Failed. Probably admin is already signed off.');
            return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
        } else {
            PHPJet::$app->tool->JSONOutput->setStatusTrue();
            PHPJet::$app->tool->JSONOutput->setMessageBoxText('You have successfully signed out.');
            return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
        }
    }

    /**
     * @return string
     */
    public function actionVerifyPOST(): string
    {
        $json = PHPJet::$app->system->request->getJSON();
        if (!$json || empty($json[$this->json2FVerificationField])) {
            PHPJet::$app->tool->JSONOutput->setStatusFalse();
            PHPJet::$app->tool->JSONOutput->setMessageBoxText('No data provided.');

            $this->modelAdmin->recordActions('Auth', false, '2F verification failed - empty data.');
            return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
        }

        // Following actions record in Model

        $verificationCode = $json[$this->json2FVerificationField];
        $result = $this->modelAdmin->validate2FAuthentication($verificationCode);
        if (!$result['valid']) {
            PHPJet::$app->tool->JSONOutput->setStatusFalse();
            PHPJet::$app->tool->JSONOutput->setMessageBoxText('Wrong verification code.');
            return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
        }

        return $this->returnSuccessfulAuthorizationMessage($result);
    }

    /**
     * @param string $method
     * @param array $POST
     * @param array $GET
     * @return ViewResponse
     */
    public function actionLogin(string $method, array $POST, array $GET): ViewResponse
    {
        if ($method === 'GET') {
            // Check whether or not admin authorized
            $isAdminAuthorized = $this->modelAdmin->isAdminAuthorized();
            if ($isAdminAuthorized) {
                return $this->view->json(true, [
                    'auth' => true
                ]);
            } else {
                return $this->view->json(true, [
                    'auth' => false
                ]);
            }
        } else if ($method === 'POST') {
            // proceed authorization
            $email = $POST['email'];
            $password = $POST['password'];

            $result = $this->modelAdmin->authorizeAdmin($email, $password);
            // todo ...
            return $this->view->json(false, ['auth' => false]);
        }
    }
}