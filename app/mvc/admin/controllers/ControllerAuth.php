<?php


namespace CloudStore\App\MVC\Admin\Controllers;


use CloudStore\App\Engine\Core\Controller;
use CloudStore\App\MVC\Admin\Models\ModelAdmin;
use CloudStore\CloudStore;

/**
 * Class ControllerAuth
 * @package CloudStore\App\MVC\Admin\Controllers
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
        parent::__construct($name);
        $this->modelAdmin = new ModelAdmin();
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
        CloudStore::$app->tool->JSONOutput->setStatusTrue();
        if ($isAdminAuthorized) {
            CloudStore::$app->tool->JSONOutput->setData([
                'auth' => true
            ]);
        } else {
            CloudStore::$app->tool->JSONOutput->setData([
                'auth' => false
            ]);
        }

        return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
    }

    /**
     * @return string
     */
    public function actionBasic(): string
    {
        $json = CloudStore::$app->system->request->getJSON();
        if (!$json || empty($json[$this->jsonEmailField]) || empty($json[$this->jsonPasswordField])) {
            CloudStore::$app->tool->JSONOutput->setStatusFalse();
            CloudStore::$app->tool->JSONOutput->setMessageBoxText('No data provided.');

            $this->modelAdmin->recordActions('Auth', false, 'attempt failed - empty data.');
            return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
        }

        // Following actions record in Model

        $email = $json[$this->jsonEmailField];
        $password = $json[$this->jsonPasswordField];
        $result = $this->modelAdmin->authorizeAdmin($email, $password);
        if (!$result['valid']) {
            CloudStore::$app->tool->JSONOutput->setStatusFalse();
            CloudStore::$app->tool->JSONOutput->setMessageBoxText('Wrong login or password.');

            return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
        }

        // is 2F enabled?
        if (!$result['2F']) {
            CloudStore::$app->tool->JSONOutput->setStatusTrue();
            CloudStore::$app->tool->JSONOutput->setMessageBoxText('Successfully authorized.');
            CloudStore::$app->tool->JSONOutput->setAction('S');
            return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
        } else {
            CloudStore::$app->tool->JSONOutput->setStatusTrue();
            CloudStore::$app->tool->JSONOutput->setMessageBoxText('We have sent you email with verification code.');
            CloudStore::$app->tool->JSONOutput->setAction('2F');
            return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
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
            CloudStore::$app->tool->JSONOutput->setStatusFalse();
            CloudStore::$app->tool->JSONOutput->setMessageBoxText('Failed. Probably admin is already signed off.');

            return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
        } else {
            CloudStore::$app->tool->JSONOutput->setStatusTrue();
            CloudStore::$app->tool->JSONOutput->setMessageBoxText('You have successfully signed out.');
            return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
        }
    }

    /**
     * @return string
     */
    public function actionVerifyCode(): string
    {
        $json = CloudStore::$app->system->request->getJSON();
        if (!$json || empty($json[$this->json2FVerificationField])) {
            CloudStore::$app->tool->JSONOutput->setStatusFalse();
            CloudStore::$app->tool->JSONOutput->setMessageBoxText('No data provided.');

            $this->modelAdmin->recordActions('Auth', false, '2F verification failed - empty data.');
            return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
        }

        // Following actions record in Model

        $verificationCode = $json[$this->json2FVerificationField];
        $result = $this->modelAdmin->validate2FAuthentication($verificationCode);
        if (!$result) {
            CloudStore::$app->tool->JSONOutput->setStatusFalse();
            CloudStore::$app->tool->JSONOutput->setMessageBoxText('Wrong verification code.');
            return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
        }

        CloudStore::$app->tool->JSONOutput->setStatusTrue();
        CloudStore::$app->tool->JSONOutput->setMessageBoxText('Successfully authorized.');
        CloudStore::$app->tool->JSONOutput->setAction('S');
        return CloudStore::$app->tool->JSONOutput->returnJSONOutput();
    }
}