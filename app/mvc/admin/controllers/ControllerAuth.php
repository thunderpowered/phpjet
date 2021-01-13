<?php


namespace Jet\App\MVC\Admin\Controllers;


use Jet\App\Engine\Core\Controller;
use Jet\App\MVC\Admin\Models\ModelAdmin;
use Jet\PHPJet;

/**
 * Class ControllerAuth
 * @package Jet\App\MVC\Admin\Controllers
 */
class ControllerAuth extends Controller
{
    /**
     * @var ModelAdmin
     */
    private $modelAdmin;
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
    public function actionCheck(): string
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
    public function actionBasic(): string
    {
        $json = PHPJet::$app->system->request->getJSON();
        if (!$json || empty($json[$this->jsonEmailField]) || empty($json[$this->jsonPasswordField])) {
            PHPJet::$app->tool->JSONOutput->setStatusFalse();
            PHPJet::$app->tool->JSONOutput->setMessageBoxText('No data');

            $this->modelAdmin->recordActions('Auth', false, 'attempt failed - no data.');
            return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
        }

        // Following actions record in Model

        $email = $json[$this->jsonEmailField];
        $password = $json[$this->jsonPasswordField];
        $result = $this->modelAdmin->authorizeAdmin($email, $password);
        if (!$result['valid']) {
            PHPJet::$app->tool->JSONOutput->setStatusFalse();
            PHPJet::$app->tool->JSONOutput->setMessageBoxText('Wrong login or password.');

            return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
        }

        // is 2F enabled?
        if (!$result['2F']) {
            PHPJet::$app->system->token->generateHash();
            PHPJet::$app->tool->JSONOutput->setStatusTrue();
            PHPJet::$app->tool->JSONOutput->setMessageBoxText('Successfully authorized.');
            PHPJet::$app->tool->JSONOutput->setAction('S');
            PHPJet::$app->tool->JSONOutput->setData([
                'urls' => $result['urls']
            ]);
            return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
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
    public function actionLogout(): string
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
    public function actionVerifyCode(): string
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

        PHPJet::$app->tool->JSONOutput->setStatusTrue();
        PHPJet::$app->tool->JSONOutput->setMessageBoxText('Successfully authorized.');
        PHPJet::$app->tool->JSONOutput->setAction('S');
        PHPJet::$app->tool->JSONOutput->setData([
            'urls' => $result['urls']
        ]);
        return PHPJet::$app->tool->JSONOutput->returnJSONOutput();
    }
}