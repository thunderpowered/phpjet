<?php

namespace CloudStore\App\Engine\Controllers;

use CloudStore\App\Engine\Core\Controller;
use CloudStore\App\Engine\Components\Utils;

class ControllerContact extends Controller
{

    public $errors;

    public function start()
    {
        if (\CloudStore\App\Engine\Components\Request::post('contact')) {
            $csrf = \CloudStore\App\Engine\Components\Request::post('csrf');
            if (!SystemHelp()->validateToken($csrf)) {
                return false;
            }

            $this->errors = Controller::getModel()->validate();
            if (!$this->errors) {

                if (!Controller::getModel()->feedback()) {
                    return false;
                }

                \CloudStore\App\Engine\Components\Request::setSession('contact_message', 'success');
            } else {
                \CloudStore\App\Engine\Components\Request::setSession('contact_message', 'error');
            }

            return Utils::strongRedirect('pages', 'contact');
        }
    }
}
