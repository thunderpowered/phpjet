<?php

namespace CloudStore\App\Engine\Tools;

use CloudStore\App\Engine\Core\Component;

/**
 *
 * Component: ShopEngine Form
 * Description: none
 *
 *
 */
class Form extends Component
{

    public static $errors;
    private static $check;
    private static $name;
    private static $validators;
    private static $opened = false;

    // Start the form
    // 1. $method - method (POST/GET)
    // 2. $action - array of controller and action

    public static function open(string $method = "post", array $action, string $name = null)
    {

        // Delete all old forms
        if (!self::$opened) {

            self::eraseForms();

            self::$opened = true;
        }

        self::$check = rand(0, 777);
        self::$name = $name;

        // If already exist (not the best way, but...)
        while (!empty($_SESSION["form_" . self::$check])) {

            self::$check = rand(0, 777);
        }

        $_SESSION["form_" . self::$check] = [
            "form_id" => self::$check,
            "required" => []
        ];

        if (empty($action[0]) OR empty($action[1])) {

            $url = \CloudStore\App\Engine\Core\Router::getHost();
        } else {

            $url = \CloudStore\App\Engine\Core\Router::getHost() . "/" . $action[0] . "/" . $action[1];
        }

        echo "<form autocomplete=\"off\" id=\"form-" . self::$check . "\" class=\"standart-form\" method=\"{$method}\" action=\"" . $url . "\">";
    }

    // Close the form

    private static function eraseForms()
    {

        foreach ($_SESSION as $key => $value) {

            if (strpos($key, "form_") !== false) {

                unset($_SESSION[$key]);
            }
        }
    }

    public static function close()
    {

        // Not so easy :)

        echo "<input type=\"hidden\" name=\"csrf\" value=\"" . \CloudStore\App\Engine\Tools\Utils::generateToken() . "\">";

        if (self::$name) {

            echo "<input type=\"hidden\" name=\"" . self::$name . "\" value=\"" . self::$check . "\">";
        }

        echo "<input type=\"hidden\" name=\"check\" value=\"" . self::$check . "\">";

        echo "</form>";

        self::$check = null;
        self::$name = null;
    }

    public static function input(string $name, string $type, bool $required, array $minmax = array(5, 35), array $validator = array(), string $error = "Поле не может быть пустым", string $label = "", string $placeholder = "", string $value = null)
    {

        // Set required
        if ($required) {

            $_SESSION["form_" . self::$check]["required"][$name]["text"] = $error;
            $_SESSION["form_" . self::$check]["required"][$name]["minmax"] = $minmax;
            $_SESSION["form_" . self::$check]["required"][$name]["handler"] = $validator;
        }

        // 1. Wrapper

        echo "<div class=\"standart-form__input-container\">";

        // 2. Label (if exists)

        if (!empty($label)) {

            echo "<label class=\"standart-form__label\" for=\"{$name}_" . self::$check . "\">" . $label . "</label>";
        }

        // 3. Input must be wrappered (for ajax-validation)

        echo "<div class=\"standart-form__input-wrapper\">";

        echo "<input autocomplete=\"off\" id=\"{$name}_" . self::$check . "\" type=\"{$type}\" class=\"standart-form__general-input\" name=\"{$name}\" placeholder=\"{$placeholder}\" value=\"{$value}\">";

        echo "</div>";

        echo "</div>";
    }

    public static function select(string $name, array $values, string $label = "", bool $required = true)
    {

        if (!empty($values)) {

            // Set required
            if ($required) {

                $_SESSION["form_" . self::$check]["required"][$name] = true;
            }

            echo "<div class=\"standart-form__input-container\">";

            if (!empty($label)) {

                echo "<label class=\"standart-form__label\" for=\"{$name}_" . self::$check . "\">" . $label . "</label>";
            }

            echo "<div class=\"standart-form__select-wrapper\">";

            echo "<select class=\"standart-form__general-select\" id=\"{$name}_" . self::$check . "\">";

            foreach ($values as $key => $value) {

                echo "<option class=\"standart-form__general-select-option\" value=\"{$key}\" >{$value}</option>";
            }

            echo "</select>";

            echo "</div>";

            echo "</div>";

        }
    }

    public static function submit(string $title = "Отправить")
    {

        echo "<div class=\"standart-form__input-container\">";

        echo "<input type=\"submit\" class=\"header__front-submit\" data-form=\"" . self::$check . "\" name=\"submitted_" . self::$check . "\" value=\"{$title}\">";

        echo "</div>";
    }

    public static function hasErrors($id)
    {

        self::validateAll();

        if (empty(self::$errors[$id])) {

            return false;
        }

        return self::$errors[$id];
    }

    public static function validateAll()
    {

        // Request must be included before this act

        foreach ($_SESSION as $key => $value) {

            if (strpos($key, "form_") !== false) {

                $id = $value["form_id"];

                if (!empty($_POST["check"])) {

                    self::validate($id);
                }
            }
        }
    }

    private static function validate($id)
    {

        $post = $_POST;

        if (empty($required = $_SESSION["form_" . $id]["required"])) {

            return false;
        }

        foreach ($required as $key => $value) {

            if (empty($post[$key])) {

                self::$errors[$id][$key] = $value["text"];
                continue;
            }

            if (!empty($value["minmax"])) {

                if (mb_strlen($post[$key]) < $value["minmax"][0] OR mb_strlen($post[$key]) > $value["minmax"][1]) {

                    self::$errors[$id][$key] = $value["text"];
                    continue;
                }
            }


            if (!empty($value["handler"][0]) AND !empty($value["handler"][1])) {

                $validator = $value["handler"][0];
                $method = $value["handler"][1];

                if (empty(self::$validators[$validator])) {

                    self::$validators[$validator] = self::validator($validator);
                }

                if (method_exists(self::$validators[$validator], $method)) {

                    if (!self::$validators[$validator]->$method($post[$key])) {

                        self::$errors[$id][$key] = $value["text"];
                        continue;
                    }
                }
            }
        }

        return true;
    }

    private static function validator($validator)
    {

        $filename = ENGINE . "validators/" . $validator . ".php";

        if (file_exists($filename)) {

            require_once($filename);

            if (class_exists($validator)) {

                $object = new $validator;
                return $object;
            }
        }

        return false;
    }
}