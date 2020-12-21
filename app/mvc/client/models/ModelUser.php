<?php

namespace CloudStore\App\MVC\Client\Models;

use CloudStore\App\Engine\Components\S;
use CloudStore\App\Engine\Config\Database;
use CloudStore\App\Engine\Core\Model;
use CloudStore\App\Engine\Core\System;
use CloudStore\App\Engine\Components\Utils;

class ModelUser extends Model
{

    private $invite_user;
    private $token;

    public function validateSignUp()
    {
        $db = Database::getInstance();
        $post = \CloudStore\App\Engine\Components\Request::post();
        \CloudStore\App\Engine\Components\Request::setSession('sign_message_text', 'Регистрационные данные введены неверно.');

        if (!$post) {
            \CloudStore\App\Engine\Components\Request::setSession('sign_message_text', 'Регистрационные данные введены неверно.');
            return false;
        }

        $email = CloudStore::$app->store->loadOne("users", ["users_email" => $post["customer_email"]]);

        if ($email) {
            \CloudStore\App\Engine\Components\Request::setSession('sign_message_text', 'Пользователь с таким E-Mail уже существует.');
            return false;
        }

        if (!preg_match("/^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i", trim($post['customer_email']))) {
            \CloudStore\App\Engine\Components\Request::setSession('sign_message_text', 'E-Mail введён неверно.');
            return false;
        }

        if ($post['customer_first_name'] AND $post['customer_last_name'] AND $post['customer_email'] AND $post['customer_password']) {
            return true;
        }

        return false;
    }

    public function signUp()
    {
        //If ref
        if ($ref = \CloudStore\App\Engine\Components\Request::get('ref')) {

            $invite = CloudStore::$app->store->loadOne("users", ["users_referer_key" => $ref]);

            if ($invite) {
                $this->invite_user = $invite['users_id'];
            }
        }

        $post = \CloudStore\App\Engine\Components\Request::post();

        $email = CloudStore::$app->store->loadOne("users", ["users_email" => $post["customer_email"]]);

        if ($email) {
            return false;
        }

        $db = database::getInstance();
        $ip = System::getUserIP();

        $this->token = hash("sha256", uniqid(rand(), true) . md5($post['customer_email']));

        $referer_key = hash("sha256", uniqid(rand(), true) . md5($post['customer_email'] . $ip));

        $password = password_hash($post['customer_password'], PASSWORD_BCRYPT);

        $result = CloudStore::$app->store->collect("users", [
            "users_name" => $post['customer_first_name'],
            "users_last_name" => $post['customer_last_name'],
            "users_email" => $post["customer_email"],
            "users_password" => $password,
            "users_ip" => $ip,
            "users_activate_token" => $this->token,
            "users_token" => $this->token,
            "users_referer_key" => $referer_key,
            "users_invite_id" => $this->invite_user
        ]);

        \CloudStore\App\Engine\Components\Request::setSession("customer_first_name", $post['customer_first_name']);

        if ($result) {
            //Erase session

            if ($this->sendMessage($post["customer_email"])) {
                \CloudStore\App\Engine\Components\Request::eraseFullSession('customer');
                return true;
            }
        }

        return false;
    }

    public function sendMessage($email)
    {
        try {
            $token = $this->token;
            $mailfrom = \CloudStore\App\Engine\Config\Config::$config['admin_email'];
            $mailto = $email;
            $subject = 'Регистрация пользователя';

            require_once HOME . 'templates/mail/' . THEME_MAIL . 'mailbodysignup.php';

            Utils::sendMail2($mailto, $mailfrom, $subject, $body);
            return true;
        } catch (\Exception $e) {
            System::exceptionToFile($e);
            return false;
        }
    }

    public function activate($array)
    {
        if ($array['users_active'] === '1') {
            return false;
        } elseif ($array['users_active'] === '0') {
            return true;
        }
        return $array;
    }

    public function finish($post)
    {

        if ($post['activate_password'] !== $post['activate_repassword'] OR strlen($post['activate_password']) < 6) {
            \CloudStore\App\Engine\Components\Request::setSession('activate_message_bad', 'Пароли должны совпадать и содержать хотя бы 6 символов');
            return false;
        }

        $password = password_hash($post['activate_password'], PASSWORD_BCRYPT);
        $token = $post['token'];

        $db = database::getInstance();

        $_use = CloudStore::$app->store->loadOne("users", ["users_token" => $token]);

        $result = CloudStore::$app->store->update("users", ["users_password" => $password, "users_active" => 1, "users_token" => ""], ["users_token" => $token]);

        if ($result) {
            //If this user was invited by smth

            if ($_use['users_invite_id'] !== "0") {

                $user = CloudStore::$app->store->loadOne("users", ["users_id" => $_use['users_invite_id']]);

                $new_count = $user['users_invited'] + 1;
                $new_points = $user['users_points'] + \CloudStore\App\Engine\Config\Config::$config['points'];

                $result = CloudStore::$app->store->update("users", ["users_invited" => $new_count, "users_points" => $new_points], ["users_id" => $user["users_id"]]);

                if (!$result) {

                    return false;
                }
            }
            //

            \CloudStore\App\Engine\Components\Request::eraseFullSession();
            return true;
        }

        \CloudStore\App\Engine\Components\Request::setSession('activate_message_bad', 'Произошла ошибка. Повторите попытку.');
        return false;
    }

    public function logIn()
    {

        /*
          !!!
          TASK: CONNECT WITH FORM.PHP
          !!!
         */

        $db = database::getInstance();
        $post = \CloudStore\App\Engine\Components\Request::post();

        if (!$post) {

            return false;
        }

        if ($user = CloudStore::$app->store->loadOne("users", ["users_email" => $post['login_email']])) {

            $token = hash("sha256", uniqid(rand(), true) . md5($user['users_email']));

            if (password_verify($post['login_password'], $user['users_password'])) {

                \CloudStore\App\Engine\Components\Request::setSession('user_id', $user['users_id']);
                \CloudStore\App\Engine\Components\Request::setSession('user_email', $user['users_email']);
                \CloudStore\App\Engine\Components\Request::setSession('user_token', $token);

                if (!S::update("users", ["users_session_token" => $token], ["users_id" => $user["users_id"]])) {

                    return false;
                }

                \CloudStore\App\Engine\Components\Request::setSession('user_is_logged', true);
                return true;
            }
        }

        return false;
    }

    public function userChange()
    {

        /*
          !!!
          TASK: CONNECT WITH FORM.PHP
          !!!
         */

        $db = database::getInstance();
        $post = \CloudStore\App\Engine\Components\Request::post();

        if (!$post) {
            return false;
        }

        if (!$post['myaccount_name']) {
            \CloudStore\App\Engine\Components\Request::setSession('error_account_name', 'Это поле не может быть пустым');
        }

        if (!$post['myaccount_email']) {
            \CloudStore\App\Engine\Components\Request::setSession('error_account_email', 'Это поле не может быть пустым');
        }

        if (!$post['myaccount_name'] OR !$post['myaccount_email']) {
            return false;
        }

        $data = $post['myaccount_date'];

        if (!$data) {
            $data = null;
        }

        //Optional errors

        $opt = false;

        if ($post['myaccount_email'] AND !preg_match("/^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i", trim($post['myaccount_email']))) {
            $opt = true;
            \CloudStore\App\Engine\Components\Request::setSession('error_account_email', 'E-Mail имеет неверный формат');
        }

        $re = '/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/i';

        if ($post['myaccount_phone'] AND !preg_match($re, trim($post['myaccount_phone']))) {
            $opt = true;
            \CloudStore\App\Engine\Components\Request::setSession('error_account_phone', 'Мобильный телефон имеет неверный формат');
        }

        if ($post['myaccount_act_phone'] AND !preg_match($re, trim($post['myaccount_act_phone']))) {
            $opt = true;
            \CloudStore\App\Engine\Components\Request::setSession('error_account_act_phone', 'Мобильный телефон имеет неверный формат');
        }

        if ($opt) {
            return false;
        }

        $id = \CloudStore\App\Engine\Components\Request::getSession("user_id");

        $result = CloudStore::$app->store->update("users", [
            "users_name" => $post['myaccount_name'],
            "users_patronymic" => $post['myaccount_patr'],
            "users_last_name" => $post['myaccount_last_name'],
            "users_email" => $post['myaccount_email'],
            "users_phone" => $post['myaccount_phone'],
            "users_gender" => $post['myaccount_gender'],
            "users_act_phone" => $post['myaccount_act_phone'],
            "users_date_of_birth" => $data
        ], ["users_id" => $id]);

        if ($result) {
            \CloudStore\App\Engine\Components\Request::setSession('error_user_success', true);
            return true;
        }

        return false;
    }

    public function passwordChange($user)
    {
        $db = database::getInstance();
        $id = \CloudStore\App\Engine\Components\Request::getSession('user_id');
        //$op   = \CloudStore\App\Engine\Config\Config::Password();
        $post = \CloudStore\App\Engine\Components\Request::post();

        \CloudStore\App\Engine\Components\Request::setSession('error_password', true);

        if (!$post['myaccount_old_password']) {
            \CloudStore\App\Engine\Components\Request::setSession('error_account_old_password', 'Это поле не может быть пустым');
            return false;
        }

        if (!$post['myaccount_new_password'] OR strlen($post['myaccount_new_password']) < 6) {
            \CloudStore\App\Engine\Components\Request::setSession('error_account_new_password', 'Это поле должно содержать минимум 6 символов');
            return false;
        }

        if (!$post['myaccount_new_repassword'] OR strlen($post['myaccount_new_repassword']) < 6) {
            \CloudStore\App\Engine\Components\Request::setSession('error_account_new_repassword', 'Это поле должно содержать минимум 6 символов');
            return false;
        }

        if (!$post['myaccount_old_password'] OR !$post['myaccount_new_password'] OR !$post['myaccount_new_repassword']) {
            return false;
        }

        $old = $post['myaccount_old_password'];

        if (!password_verify($old, $user['users_password'])) {

            \CloudStore\App\Engine\Components\Request::setSession('error_account_old_password', 'Неверный пароль');
            return false;
        }

        if ($post['myaccount_new_password'] !== $post['myaccount_new_repassword']) {
            \CloudStore\App\Engine\Components\Request::setSession('error_account_new_repassword', 'Пароли не совпадают');
            return false;
        }

        $new = $post['myaccount_new_password'];

        $password = password_hash($new, PASSWORD_BCRYPT);

        $result = CloudStore::$app->store->update("users", ["users_password" => $password], ["users_id" => $id]);

        if ($result) {

            \CloudStore\App\Engine\Components\Request::eraseFullSession('error_password');
            \CloudStore\App\Engine\Components\Request::setSession('error_account_new_repassword_success', 'Пароль успешно изменен');

            return true;
        }

        return false;
    }

    public function getAddress($id)
    {
        $sql = "SELECT * FROM user_addresses a "
            . "LEFT JOIN countries co ON a.address_country = co.country_handle AND a.store = co.store "
            . "LEFT JOIN region r ON a.address_region = r.region_handle AND a.store = r.store "
            . "WHERE address_id=:id AND a.store = :store";
        return CloudStore::$app->store->execGet($sql, [":id" => $id, ":store" => \CloudStore\App\Engine\Config\Config::$config["site_id"]])[0];
    }

    public function changeAddress($id)
    {
        $db = database::getInstance();
        $post = \CloudStore\App\Engine\Components\Request::post();

        if (!$post) {

            return false;
        }

        if (!$post['address_address'] OR !$post['address_city'] OR !$post['address_index'] OR !$post['address_phone']) {

            return false;
        }

        $post['address_country'] = $post['address_country'] ?? "";
        $post['address_region'] = $post['address_region'] ?? "";

        $result = CloudStore::$app->store->update("user_addresses", [
            "address_name" => $post['address_first_name'],
            "address_last_name" => $post['address_last_name'],
            "address_company" => $post['address_company'],
            "address_optional" => $post['address_optional'],
            "address" => $post['address_address'],
            "address_city" => $post['address_city'],
            "address_country" => $post['address_country'],
            "address_region" => $post['address_region'],
            "address_phone" => $post['address_phone'],
            "address_index" => $post["address_index"]
        ], ["address_id" => $id]);

        if ($result) {

            return true;
        }
    }

    public function newAddress()
    {
        $db = database::getInstance();
        $post = \CloudStore\App\Engine\Components\Request::post();
        $user_id = \CloudStore\App\Engine\Components\Request::getSession('user_id');

        if (!$post) {
            return false;
        }

        if (!$post['address_new_address'] OR !$post['address_new_city'] OR !$post['address_new_index'] OR !$post['address_new_phone']) {
            return false;
        }

        //Some corrections
        $index = (int)$post['address_new_index'];

        $result = @S::collect("user_addresses", [
            "address_user" => $user_id,
            "address_name" => $post['address_new_first_name'],
            "address_last_name" => $post['address_new_last_name'],
            "address_company" => $post['address_new_company'],
            "address_optional" => $post['address_new_optional'],
            "address" => $post['address_new_address'],
            "address_city" => $post['address_new_city'],
            "address_country" => $post['address_country'],
            "address_region" => $post['address_new_region'],
            "address_phone" => $post['address_new_phone'],
            "address_index" => $index
        ]);

        if ($result) {
            \CloudStore\App\Engine\Components\Request::eraseFullSession('address');
            return true;
        }
        return false;
    }

    public function sendInvite()
    {
        $post = \CloudStore\App\Engine\Components\Request::post();

        if (!$post) {
            return false;
        }

        if (!$post['invite_email'] OR !preg_match("/^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i", trim($post['invite_email']))) {
            \CloudStore\App\Engine\Components\Request::setSession('error_email', 'E-Mail введён неверно');
            return false;
        }

        $id = \CloudStore\App\Engine\Components\Request::getSession("user_id");

        $user = CloudStore::$app->store->loadOne("users", ["users_id" => $id]);

        $tpl = file_get_contents(HOME . 'templates/mail/' . THEME_MAIL . 'invite_tpl.php');

        $tpl = str_replace("{{HOST}}", Router::getHost(), $tpl);
        $tpl = str_replace("{{SITE_NAME}}", \CloudStore\App\Engine\Config\Config::$config['site_name'], $tpl);
        $tpl = str_replace("{{ADMIN_EMAIL}}", \CloudStore\App\Engine\Config\Config::$config['admin_email'], $tpl);

        $tpl = str_replace("{{NAME}}", $user['users_name'] . ' ' . $user['users_last_name'], $tpl);
        $tpl = str_replace("{{SITE_NAME}}", \CloudStore\App\Engine\Config\Config::$config['site_name'], $tpl);
        $tpl = str_replace("{{INVITE_LINK}}", Router::getHost() . '/user/signup?ref=' . $user['users_referer_key'], $tpl);
        $tpl = str_replace("{{TEXT}}", $post['invite_text'], $tpl);

        $mailfrom = \CloudStore\App\Engine\Config\Config::$config['admin_email'];
        $mailto = $post['invite_email'];
        $subject = 'Приглашение на сайт "' . \CloudStore\App\Engine\Config\Config::$config['site_name'] . '"';

        if (\CloudStore\App\Engine\Components\Utils::sendMail2($mailto, $mailfrom, $subject, $tpl)) {
            \CloudStore\App\Engine\Components\Request::setSession('error_success_email', 'Мы отправили письмо по указанному адресу');
            return true;
        }

        return false;
    }

    public function restore()
    {
        $post = \CloudStore\App\Engine\Components\Request::post('restore_email');

        if (!$post OR !preg_match("/^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i", trim($post))) {
            return 'E-Mail введён некорректно';
        }

        $token = hash("sha256", uniqid(rand(), true) . md5($post));

        $db = database::getInstance();

        if (!S::loadOne("users", ["users_email" => $post])) {
            return 'Пользователь с таким E-Mail не найден';
        }

        if (!S::update("users", ["users_temp_token" => $token], ["users_email" => $post])) {

            return true;
        }

        $tpl = file_get_contents(HOME . 'templates/mail/' . THEME_MAIL . 'restore_tpl.php');

        $tpl = str_replace("{{HOST}}", Router::getHost(), $tpl);
        $tpl = str_replace("{{SITE_NAME}}", \CloudStore\App\Engine\Config\Config::$config['site_name'], $tpl);
        $tpl = str_replace("{{ADMIN_EMAIL}}", \CloudStore\App\Engine\Config\Config::$config['admin_email'], $tpl);

        $tpl = str_replace("{{RESTORE_LINK}}", Router::getHost() . "/user/restore/new_password?token={$token}", $tpl);

        //$body     = "Для восстановления паролья перейдите по <a href='".ShopEngine::getHost()."/user/restore/new_password?token={$token}'>ссылке</a>";
        $subject = "Восстановление пароля на сайте \"" . \CloudStore\App\Engine\Config\Config::$config['site_name'] . "\"";
        $mailto = $post;
        $mailfrom = \CloudStore\App\Engine\Config\Config::$config['admin_email'];

        // Let me explain
        // True - when there are some errors
        // False - when no errors
        if (\CloudStore\App\Engine\Components\Utils::sendMail2($mailto, $mailfrom, $subject, $tpl)) {
            return false;
        }
        return true;
    }

    public function newPasswordValidate($token)
    {

        if (!S::loadOne("users", ["users_temp_token" => $token])) {
            return false;
        }

        return true;
    }

    public function newPassword($token)
    {
        $db = database::getInstance();

        if (!S::loadOne("users", ["users_temp_token" => $token])) {
            return 'Пользователь не найден';
        }

        $password1 = \CloudStore\App\Engine\Components\Request::post('restore_password');
        $password2 = \CloudStore\App\Engine\Components\Request::post('restore_repassword');

        if ($password1 !== $password2 OR strlen($password1) < 6 OR strlen($password2) < 6) {

            return 'Пароли должны совпадать и содержать минимум 6 символов';
        }

        $password = password_hash($password1, PASSWORD_BCRYPT);

        if (!S::update("users", ["users_password" => $password, "users_temp_token" => ""], ["users_temp_token" => $token])) {

            return 'Произошла ошибка';
        }

        \CloudStore\App\Engine\Components\Request::setSession('success_password', 'Пароль успешно изменен. Вы можете войти.');

        return false;
    }
}
