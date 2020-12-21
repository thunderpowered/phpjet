<?php

namespace CloudStore\App\Engine\Controllers;

use CloudStore\App\Engine\CloudStore;
use CloudStore\App\Engine\Components\Request;
use CloudStore\App\Engine\Components\S;
use CloudStore\App\Engine\Core\Controller;
use CloudStore\App\Engine\Core\Router;
use CloudStore\App\Engine\Core\System;
use CloudStore\App\Engine\Components\Utils;

class ControllerUser extends Controller
{

    public $change_password;
    public $change_information;

    //Make this controller actionable
    public static function getUserInfo()
    {
        /*
         * 
         * In some cases, it is more appropriate to create a separate methods for manipulating data and displaying this data to the user
         */

        if (!\CloudStore\App\Engine\Components\Request::getSession('user_id')) {

            return false;
        }

        $id = \CloudStore\App\Engine\Components\Request::getSession('user_id');
        //$sql  = "SELECT * FROM users WHERE users_id=?";
        //return \CloudStore\App\Engine\Components\Getter::getFreeData($sql, [$id]);
        return CloudStore::$app->store->loadOne("users", ["users_id" => $id]);
    }

    //Actions

    public function actionLogIn()
    {
        $this->title = "Вход";

        if (\CloudStore\App\Engine\Components\Request::getSession('user_is_logged')) {

            return Utils::regularRedirect('user', 'account');
        }

        if ($post = \CloudStore\App\Engine\Components\Request::post()) {
            $csrf = $post["csrf"];

            if (!Utils::validateToken($csrf)) {

                return false;
            }

            if ($this->model->Login()) {

                return Utils::regularRedirect("user", "account");
            } else {

                Request::setSession('error_login_message', 'error');
            }
        }

        return $this->view->render("view_login", [
        ]);
    }

    public function actionSignUp()
    {
        $this->title = "Регистрация";

        if (\CloudStore\App\Engine\Components\Request::getSession('user_is_logged')) {
            Utils::regularRedirect('user', 'account');
        }

        if (\CloudStore\App\Engine\Components\Request::post('signup')) {

            $csrf = \CloudStore\App\Engine\Components\Request::post('csrf');

            if (!Utils::validateToken($csrf)) {
                return false;
            }

            if ($this->model->validateSignUp()) {
                if ($this->model->signUp()) {
                    \CloudStore\App\Engine\Components\Request::setSession('sign_message', 'success');
                    return Utils::regularRedirect("user", "login");
                }
            } else {
                \CloudStore\App\Engine\Components\Request::setSession('sign_message', 'error');
            }
        }

        return $this->view->render("view_signup", [
        ]);
    }

    // Проверить!
    public function actionActivate()
    {
        $activate = null;

        if (\CloudStore\App\Engine\Components\Request::get('token')) {
            $token = \CloudStore\App\Engine\Components\Request::get('token');

            $array = CloudStore::$app->store->loadOne("users", ["users_activate_token" => $token]);

            if ($array) {
                if ($this->model->activate($array)) {
                    $activate = true;

                    // User isn't active
                    // So we need to activate him
                    CloudStore::$app->store->update("users", ["users_active" => 1, "users_activate_token" => ""], ["users_id" => $array["users_id"]]);
                    $this->view->setLayout("empty");
                    return $this->view->render("view_activate2", []);
                } else {
                    $activate = false;
                }
            }
        } else {
            CloudStore::$app->router->errorPage404();
        }

        $this->view->setLayout("empty");
//        $this->view->layout = "empty";
        return $this->view->render("view_activate3", []);
    }

    public function actionAccount()
    {
        $this->title = "Аккаунт";

        $user = $this->get_user();

        if (\CloudStore\App\Engine\Components\Request::post('user_account_change')) {
            $csrf = \CloudStore\App\Engine\Components\Request::post('csrf');

            if (!Utils::validateToken($csrf)) {
                return Utils::regularRedirect("user", 'logout');
            }

            if (\CloudStore\App\Engine\Components\Request::post('myaccount_new_password') OR \CloudStore\App\Engine\Components\Request::post('myaccount_old_password') OR \CloudStore\App\Engine\Components\Request::post('myaccount_new_repassword')) {
                if (Controller::getModel()->passwordChange($user)) {
                    $this->change_password = true;
                }
            }

            if (Controller::getModel()->userChange()) {
                $this->change_information = true;
            }

            return Utils::regularRedirect('user', 'account');
        }

        if (!\CloudStore\App\Engine\Components\Request::getSession('user_id')) {
            return Utils::regularRedirect('user', 'login');
        }

        return $this->view->render("view_myaccount", [
            'password' => $this->change_password,
            'information' => $this->change_information,
            'user' => $user
        ]);
    }

    public function get_user()
    {

        if (!\CloudStore\App\Engine\Components\Request::getSession("user_is_logged")) {

            return Utils::regularRedirect("user", "login");
        }

        // If something wrong with user
        if (!Utils::validateUser()) {

            return Utils::regularRedirect("user", "logout");
        }


        $user = CloudStore::$app->store->loadOne("users", ["users_id" => \CloudStore\App\Engine\Components\Request::getSession("user_id")]);


        if (empty($user)) {

            return Utils::regularRedirect("user", "logout");
        }

        return $user;
    }

    public function actionOrders()
    {
        $this->title = "Мои заказы";

        if (!\CloudStore\App\Engine\Components\Request::getSession('user_is_logged')) {
            Utils::regularRedirect('user', 'login');
        }

        if (!Utils::validateUser()) {
            return false;
        }

        $id = \CloudStore\App\Engine\Components\Request::getSession("user_id");

        $orders = CloudStore::$app->store->load("orders", ["orders_users_id" => $id, "orders_status" => "!0"], ["orders_id" => "DESC"]);

        $sql = "SELECT * FROM user_addresses a "
            . "LEFT OUTER JOIN countries co ON a.address_country = co.country_handle AND a.store = co.store "
            . "LEFT OUTER JOIN region r ON a.address_region = r.region_handle AND a.store = r.store "
            . "WHERE address_user=:id AND a.store = :store";
        $addresses = CloudStore::$app->store->execGet($sql, [":id" => $id, ":store" => \CloudStore\App\Engine\Config\Config::$config["site_id"]]);

        if (count($addresses) < 1) {
            $addresses = false;
        }

        return $this->view->render("view_orders", [
            'orders' => $orders,
            'addresses' => $addresses
        ]);
    }

    public function actionAddresses(bool $option = false)
    {
        $this->title = "Мои адреса";

        if (!Utils::validateUser()) {
            return false;
        }

        $id = \CloudStore\App\Engine\Components\Request::getSession('user_id');
        $opt = strtolower(trim(Router::getOption()));

        if (!\CloudStore\App\Engine\Components\Request::getSession('user_is_logged')) {
            Utils::regularRedirect('user', 'login');
        }

        if (!$option) {

            if ($opt === "red") {

                return $this->Red();
            } else if ($opt === "add") {

                return $this->Add();
            }
        }

        //If sended form "address_change"
        $red = null;
        if (\CloudStore\App\Engine\Components\Request::post('address_change')) {
            $csrf = \CloudStore\App\Engine\Components\Request::post('csrf');

            if (!Utils::validateToken($csrf)) {
                return Router::hoHome();
            }

            $addid = \CloudStore\App\Engine\Components\Request::post('address_change');

            if ($this->model->ChangeAddress($addid)) {
                return Utils::regularRedirect('user', 'addresses');
            }

            $red = false;
        }

        if (\CloudStore\App\Engine\Components\Request::post('address_new')) {
            $csrf = \CloudStore\App\Engine\Components\Request::post('csrf');

            if (!Utils::validateToken($csrf)) {
                return Router::hoHome();
            }

            //var_dump(\CloudStore\App\Engine\Components\Request::Post());
            //return;

            if ($this->model->NewAddress()) {
                return Utils::regularRedirect('user', 'addresses');
            }

            $new = false;
        }

        $sql = "SELECT * FROM user_addresses a "
            . "LEFT JOIN countries co ON a.address_country = co.country_handle AND a.store = co.store "
            . "LEFT JOIN region r ON a.address_region = r.region_handle AND a.store = r.store "
            . "WHERE address_user=:id AND a.store = :store";
        $addresses = CloudStore::$app->store->execGet($sql, [":id" => $id, ":store" => \CloudStore\App\Engine\Config\Config::$config["site_id"]]);

        $start = [
            'addresses' => $addresses,
            'red' => $red
        ];

        if ($option) {
            return $start;
        }

        return $this->view->render("view_addresses", [
            'start' => $start
        ]);
    }

    public function red()
    {
        $red_id = \CloudStore\App\Engine\Components\Request::get('addid');

        if (!$red_id) {
            $red = false;
        }

        $red = $this->model->getAddress($red_id);

        if (count($red) < 1) {
            $red = false;
        }

        $return = $this->actionAddresses(true);

        $return['red'] = $red;

        $contries = CloudStore::$app->store->load("countries", ["country_avail" => 1]);

        if (isset($return['red']['address_country'])) {
            $sql = "SELECT * FROM region WHERE country_id IN ("
                . "SELECT country_id FROM countries WHERE country_handle = :handle) AND region_avail = '1' AND store = :store";
            $regions = CloudStore::$app->store->execGet($sql, [":handle" => $return['red']['address_country'], ":store" => \CloudStore\App\Engine\Config\Config::$config["site_id"]]);
        }

        return $this->view->render("view_addresses", [
            'start' => $return,
            'countries' => $contries ?? null,
            'regions' => $regions ?? null
        ]);
    }

    public function add()
    {
        $new = true;

        $return = $this->actionAddresses(true);

        $return['new'] = $new;

        $countries = CloudStore::$app->store->load("countries", ["country_avail" => 1]);

        return $this->view->render("view_addresses", [
            'start' => $return,
            'countries' => $countries
        ]);
    }

    public function actionLogout()
    {
        if (!\CloudStore\App\Engine\Components\Request::getSession('user_is_logged')) {
            return Utils::regularRedirect('user', 'login');
        }

        if (\CloudStore\App\Engine\Components\Request::eraseUserSession()) {
            return Utils::regularRedirect('user', 'login');
        }

        return Utils::regularRedirect('user', 'login');
    }

    public function actionInvite()
    {
        $this->title = "Пригласить друга";

        if (!\CloudStore\App\Engine\Components\Request::getSession('user_is_logged')) {
            return Utils::regularRedirect('user', 'login');
        }

        if (!Utils::validateUser()) {
            return false;
        }

        $error = false;

        if (\CloudStore\App\Engine\Components\Request::post('invite')) {
            $csrf = \CloudStore\App\Engine\Components\Request::post('csrf');

            if (!Utils::validateToken($csrf)) {
                return Router::hoHome();
            }

            if (!$this->model->SendInvite()) {
                $error = true;
            }
        }

        return $this->view->render("view_invite", [
            'error' => $error
        ]);
    }

    public function newPassword()
    {
        $this->title = "Восстановление пароля";

        //$this->actionRestore(true);

        if (\CloudStore\App\Engine\Components\Request::post('restore_new')) {

            $token = \CloudStore\App\Engine\Components\Request::get('token');

            if ($errors = $this->model->newPassword($token)) {

                return $this->view->render("view_restore", [
                    'status' => 'new_password',
                    'errors' => $errors
                ]);
            }
            return Utils::regularRedirect('user', 'login');
        }

        $token = Request::get("token");
        if ($token) {

            if ($this->model->newPasswordValidate($token)) {

                return $this->view->render("view_restore", [
                    'status' => 'new_password'
                ]);
            }
        }

        return Router::hoHome();
    }

    // Custom helpers

    public function actionRestore(bool $option = false)
    {
        $this->title = "Восстановление пароля";

        if (\CloudStore\App\Engine\Components\Request::getSession('user_is_logged')) {
            return Utils::regularRedirect('user', 'account');
        }

        if(Request::get("token")) {

            return $this->newPassword();
        }

        $start = false;
        $errors = false;

        if (\CloudStore\App\Engine\Components\Request::post('restore')) {

            $csrf = \CloudStore\App\Engine\Components\Request::post('csrf');

            if (!Utils::validateToken($csrf)) {
                return Router::hoHome();
            }

            if (!$errors = Controller::getModel()->Restore()) {
                $start = true;
            }
        }

        if ($option) {
            return true;
        }

        return $this->view->render("view_restore", [
            'errors' => $errors,
            'start' => $start
        ]);
    }
}
