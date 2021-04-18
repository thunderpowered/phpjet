<?php


namespace Jet\App\MVC\Admin\Controllers;

use Jet\App\Engine\Core\Controller;
use Jet\App\Engine\Core\View;
use Jet\App\Engine\Interfaces\MessageBox;
use Jet\App\Engine\Interfaces\ViewResponse;
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
     * @param View $view
     * @param bool $enableTracker
     */
    public function __construct(View $view, bool $enableTracker = false)
    {
        parent::__construct($view, $enableTracker);
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
     * @return ViewResponse
     */
    public function actionCheck(): ViewResponse
    {
        $admin = $this->modelAdmin->isAdminAuthorized();
        if ($admin->status) {
            return $this->view->json(HTTP_OK, [
                'auth' => true,
                'admin_id' => $admin->customData['id']
            ]);
        } else {
            return $this->view->json(HTTP_OK, [
                'auth' => false,
            ]);
        }
    }

    /**
     * @param string $method
     * @param array $POST
     * @return ViewResponse
     */
    public function actionLogin(string $method, array $POST): ViewResponse
    {
        $email = $POST['email'];
        $password = $POST['password'];
        $result = $this->modelAdmin->authorizeAdmin($email, $password);
        if (!$result->status) {
            return $this->view->json(HTTP_BAD_REQUEST, ['auth' => false], '', new MessageBox(MessageBox::ERROR, 'Wrong login or password'));
        }
        if (isset($result->customData['action']) && $result->customData['action'] === '2F') {
            return $this->view->json(HTTP_OK, ['auth' => false], '2F', new MessageBox(MessageBox::INFO, 'We have sent you email with verification code'));
        } else {
            return $this->view->json(HTTP_OK, ['auth' => true, 'admin_id' => $result->customData['id']], 'S', new MessageBox(MessageBox::SUCCESS, 'Successfully authorized'));
        }
    }

    /**
     * @param string $method
     * @param array $POST
     * @return ViewResponse
     */
    public function actionVerify(string $method, array $POST): ViewResponse
    {
        $verificationCode = $POST['verification'];
        $result = $this->modelAdmin->validate2FAuthentication($verificationCode);
        if (!$result->status) {
            return $this->view->json(HTTP_BAD_REQUEST, ['auth' => false], '', new MessageBox(MessageBox::ERROR, 'Wrong verification code'));
        } else {
            return $this->view->json(HTTP_OK, ['auth' => true, 'admin_id' => $result->customData['id']], 'S', new MessageBox(MessageBox::SUCCESS, 'Successfully authorized'));
        }
    }

    /**
     * @param string $method
     * @return ViewResponse
     */
    public function actionLogout(string $method): ViewResponse
    {
        $result = $this->modelAdmin->logout();
        if (!$result->status) {
            // i really have no idea what i should return in this case
            return $this->view->json(HTTP_BAD_REQUEST, ['auth' => null], '', new MessageBox(MessageBox::ERROR, "Failed - " . $result->message));
        } else {
            return $this->view->json(HTTP_OK, ['auth' => false], '', new MessageBox(MessageBox::SUCCESS, 'Successfully signed off'));
        }
    }
}