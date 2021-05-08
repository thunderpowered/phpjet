<?php

namespace Jet\App\Engine\Tools;

use Jet\App\Engine\Config\Config;
use Jet\App\Engine\Core\Router;
use Jet\App\Engine\Core\System;
use Jet\PHPJet;

/**
 *
 * Methods for solving simple problems
 * Many of this problems are solving by another ways
 * So i need to refactor this class
 * I'll do it in next updates
 *
 * Object Help can be used by 3 ways:
 * 1. Use Help::static_method($rapam); to call static methods of Help (deprecated).
 * 2. Use Utils::method( $param ); - most common way.
 * 3. You can also use it as component of SE. Just use ShopEngine::$comp->help->method( $param ); (also deprecated)
 *
 */

/**
 * Class Utils
 * @package Jet\App\Engine\Tools
 */
class Utils
{
    /**
     * @return string
     */
    public static function generateToken()
    {

        // Generate CSRF-token. If you want to protect some action, write \Jet\App\Engine\Tools\Utils::generate_token(), then use validate to check it.
        // This method will be improved in next version of SE.
        // After every action, the token will die and generate again.
        // It'll get more security for application. 
        // Current solution is very simple, but it works in most cases.

        if (!isset($_SESSION['token'])) {

            $_SESSION['token'] = hash("sha256", uniqid(rand(), true));
        }
        return $_SESSION['token'];
    }

    /**
     * @param $token
     * @return bool
     */
    public static function validateToken($token)
    {

        // This method check the token
        if (isset($_SESSION['token']) AND $_SESSION['token'] === $token) {

            return true;
        }
        return false;
    }

    /**
     * @param $token
     * @return bool
     */
    public static function validateAction($token)
    {

        // This method crashes the application if something wrong
        // It's more simple to use. Just write Utils::validate_action();

        if (empty($token)) {
            $token = Request::get('token');
        }

        if (isset($_SESSION['token']) AND $_SESSION['token'] === $token) {
            return true;
        }

        die("Forbidden");
    }

    /**
     * @return string
     */
    public static function generateCheckoutToken()
    {

        // Separate method for checkout
        // I have no idea why i did it

        if (!isset($_SESSION['checkout_token'])) {
            $_SESSION['checkout_token'] = hash("sha256", uniqid(rand(), true));
        }
        return $_SESSION['checkout_token'];
    }

    /**
     * @param $token
     * @return bool
     */
    public static function validateCheckoutToken($token)
    {
        if (!empty($_SESSION['checkout_token']) AND $_SESSION['checkout_token'] === $token) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public static function generateHash()
    {

        return hash("sha256", uniqid(rand(), true));
    }

    /**
     * @param string $fileName
     * @return string
     * @todo deny direct access to images folder, do it through some controller or something
     */
    public static function getImageLink(string $fileName = "")
    {
        // Creating link of image
        if ($fileName !== "" && file_exists(IMAGES . $fileName) && !is_dir(IMAGES . $fileName)) {
            $img_path = PHPJet::$app->router->getHost() . '/' . IMAGES . $fileName;
        } else {
            $img_path = PHPJet::$app->router->getHost() . "/common/no_image.png";
        }

        return $img_path;
    }

    /**
     * @param string $fileName
     * @return string
     */
    public static function getThumbnailLink(string $fileName = "")
    {
        // Creating link of thumbnail
        if ($fileName !== "" && file_exists(THUMBNAILS . $fileName) && !is_dir(THUMBNAILS . $fileName)) {
            $img_path = PHPJet::$app->router->getHost() . '/' . THUMBNAILS . $fileName;
        } else {
            $img_path = PHPJet::$app->router->getHost() . "/common/no_image.png";
        }

        return $img_path;
    }

    /**
     * @param string $row
     * @param string|null $class
     * @param string|null $title
     * @return string
     * @deprecated
     */
    public static function getThumbnail(string $row = "", string $class = null, string $title = null)
    {
        if ($row !== "" && file_exists(THUMBNAILS . $row) && !is_dir(THUMBNAILS . $row)) {
            $img_path = PHPJet::$app->router->getHost() . '/' . THUMBNAILS . $row;
        } else {
            $img_path = "/common/no_image.gif";
        }

        $class = !empty($class) ? $class : "default_se_image";
        $title = !empty($title) ? $title : "Default image title";

        return '<img src="' . $img_path . '" class="' . $class . '" title="' . htmlentities($title) . '" alt="' . htmlentities($title) . '"/>';
    }

    /**
     * @param string $row
     * @param string|null $class
     * @param string|null $title
     * @return string
     * @deprecated
     */
    public static function getImage(string $row = "", string $class = null, string $title = null)
    {
        if ($row !== "" && file_exists(IMAGES . $row) && !is_dir(IMAGES . $row)) {
            $img_path = PHPJet::$app->router->getHost() . '/' . IMAGES . $row;
        } else {
            $img_path = "/common/no_image.gif";
        }

        $class = !empty($class) ? $class : "default_se_image";
        $title = !empty($class) ? $class : "Default image title";

        return '<img src="' . $img_path . '" class="' . $class . '" title="' . htmlentities($title) . '" alt="' . htmlentities($title) . '"/>';
    }

    /**
     * @param $row
     * @param $oNwidth
     * @param $oNheight
     * @param $title
     * @param string $e
     * @param string $class
     * @return string
     */
    public static function resizeImage($row, $oNwidth, $oNheight, $title, $e = "px", $class = "default_se_image")
    {
        if ($row !== "" && file_exists($row) && !is_dir($row)) {
            $img_path = $row;
        } else {
            $img_path = "common/no_image.gif";
        }

        if ($oNheight !== null AND $oNwidth !== null) {

            $max_width = $oNwidth;
            $max_height = $oNheight;
            list($width, $height) = getimagesize($img_path);
            $ratioh = $max_height / $height;
            $ratiow = $max_width / $width;
            $ratio = min($ratioh, $ratiow);
            $width = intval($ratio * $width);
            $height = intval($ratio * $height);
        } else {

            $width = "";
            $height = "";
            $e = "";
        }

        $image = '<img src="/' . $img_path . '" class="' . $class . '" title="' . htmlentities($title) . '" width="' . $width . $e . '" height="' . $height . $e . '" alt="' . htmlentities($title) . '"/>';
        //$image = '<img src="/'.$img_path.'" title="'.$title.'" alt="'.$title.'"/>';
        return $image;
    }

    /**
     * @param $num
     * @return string
     */
    public static function asSimplePrice($num)
    {
        if (!$num) {
            return '0.00';
        }

        return number_format($num, 2, '.', ',');
    }

    /**
     * @param float $number
     * @return string
     */
    public static function asPrice(float $number): string
    {
//        $priceTemplate = PHPJet::$app->
        if (empty($_SESSION['price_template'])) {

            $price_template = PHPJet::$app->store->loadOne("settings", ["settings_name" => "price_template"], false);
            if (!$price_template) {
                return $number;
            }

            // @todo if empty
            $_SESSION['price_template'] = unserialize($price_template['settings_value']);
        }

        $price_template = $_SESSION['price_template'];

        return @number_format($number, intval($price_template['decimals']), $price_template['decimal_delimiter'], $price_template['thousands_delimiter']) . ' ' . $price_template['price_currency'];
    }

    /**
     * @param $from
     * @param $to
     * @param $subject
     * @param $body
     */
    public static function sendMail($from, $to, $subject, $body)
    {
        $charset = 'utf-8';
        mb_language("ru");
        $headers = "MIME-version: 1.0 \n";
        $headers .= "From: <" . $from . "> \n";
        $headers .= "Reply-To: <" . $from . "> \n";
        $headers .= "Content-Type: text/html; charset=$charset \n";

        $subject = '=?' . $charset . '?B?' . base64_encode($subject) . '?=';

        mail($to, $subject, $body, $headers);
    }

    /**
     * @return string
     */
    public static function generatePassword()
    {
        $number = 11;

        $arr = array('a', 'b', 'c', 'd', 'e', 'f',
            'g', 'h', 'i', 'j', 'k', 'l',
            'm', 'n', 'o', 'p', 'r', 's',
            't', 'u', 'v', 'x', 'y', 'z',
            '1', '2', '3', '4', '5', '6',
            '7', '8', '9', '0');

        $password = "";

        for ($i = 0; $i < $number; $i++) {
            $index = rand(0, count($arr) - 1);
            $password .= $arr[$index];
        }

        return $password;
    }

    /**
     * @param $dataToClear
     * @param bool $trim
     * @return array|string
     */
    public function removeSpecialChars($dataToClear, bool $trim = true)
    {
        if (is_array($dataToClear)) {
            //recursive clear
            foreach ($dataToClear as $key => $value) {
                $dataToClear[$key] = $this->removeSpecialChars($value);
            }

            return $dataToClear;
        }

        // Not good, but it's temporary
        // UPD: 21.09.2019 not as temporary as i thought :)
        // UPD: 11.07.2020 still there ha-ha-ha
        if ($trim) {
            $dataToClear = trim($dataToClear);
        }

        return htmlentities($dataToClear, ENT_COMPAT | ENT_HTML401);
    }

    /**
     * @param $dataToRevert
     * @return array|string
     */
    public function revertRemoveSpecialCart($dataToRevert)
    {
        if (is_array($dataToRevert)) {
            foreach ($dataToRevert as $key => $value) {
                $dataToRevert[$key] = $this->revertRemoveSpecialCart($value);
            }

            return $dataToRevert;
        }

        return html_entity_decode($dataToRevert);
    }

    /**
     * TODO: deprecated GET RID OF IT
     * @param $main
     * @deprecated
     */
    public static function getPagination($main)
    {
        $count = ProductManager::$count;
        $num = ProductManager::$num;

        $array = Paginator::preparePagination($count, $num);
        $page = $array["page"];
        $sorting = $array["sort"];
        $total = $array["total"];
        $main_page = $main;

        if (file_exists(HOME . 'widgets/views/' . THEME_VIEWS . "pagination.php")) {

            @include HOME . 'widgets/views/' . THEME_VIEWS . "pagination.php";
        } else {

            @include ENGINE . 'widgets/views/pagination.php';
        }
    }

    /**
     * TODO: deprecated
     * @deprecated
     */
    static function getSorting()
    {
        return false;

        $main_page = System::getControllerObject()::GetPageAddress();

        if (\Jet\App\Engine\Core\System::getControllerObject() === 'ControllerMain') {
            //Действия со статическим URI. Тоже кастыль, но я придумаю что-нибудь
            if ($_GET["page"]) {
                $page = \Jet\App\Engine\Tools\Utils::removeSpecialChars($_GET["page"]);
                echo '
                    <li><a href="' . $main_page . '?page=' . $page . '&sort=price-asc">От дешевых к дорогим</a></li>
                    <li><a href="' . $main_page . '?page=' . $page . '&sort=price-desc">От дорогих в дешевым</a></li>
                    <li><a href="' . $main_page . '?page=' . $page . '&sort=popular">Популярное</a></li>
                    <li><a href="' . $main_page . '?page=' . $page . '&sort=news">Новинки</a></li>
                    <li><a href="' . $main_page . '?page=' . $page . '&sort=brand">От А до Я</a></li>
                ';
            } else {
                echo '
                    <li><a href="' . $main_page . '?sort=price-asc">От дешевых к дорогим</a></li>
                    <li><a href="' . $main_page . '?sort=price-desc">От дорогих в дешевым</a></li>
                    <li><a href="' . $main_page . '?sort=popular">Популярное</a></li>
                    <li><a href="' . $main_page . '?sort=news">Новинки</a></li>
                    <li><a href="' . $main_page . '?sort=brand">От А до Я</a></li>
                ';
            }
        } else {
            //Действия со динамическим URI. 
            if ($_GET["page"]) {
                $page = \Jet\App\Engine\Tools\Utils::removeSpecialChars($_GET["page"]);
                echo '
                    <li><a href="' . $main_page . 'page=' . $page . '&sort=price-asc">От дешевых к дорогим</a></li>
                    <li><a href="' . $main_page . 'page=' . $page . '&sort=price-desc">От дорогих в дешевым</a></li>
                    <li><a href="' . $main_page . 'page=' . $page . '&sort=popular">Популярное</a></li>
                    <li><a href="' . $main_page . 'page=' . $page . '&sort=news">Новинки</a></li>
                    <li><a href="' . $main_page . 'page=' . $page . '&sort=brand">От А до Я</a></li>
                ';
            } else {
                echo '
                <li><a href="' . $main_page . 'sort=price-asc">От дешевых к дорогим</a></li>
                <li><a href="' . $main_page . 'sort=price-desc">От дорогих в дешевым</a></li>
                <li><a href="' . $main_page . 'sort=popular">Популярное</a></li>
                <li><a href="' . $main_page . 'sort=news">Новинки</a></li>
                <li><a href="' . $main_page . 'sort=brand">От А до Я</a></li>
            ';
            }
        }
    }

    /**
     * @return bool
     * @deprecated
     */
    public static function updateCount()
    {
        return false;
    }

    /**
     * @param $url
     * @param int $code
     * @deprecated
     */
    public static function away($url, $code = 301)
    {
        header("Location: " . $url, true, $code);
        exit();
    }

    /**
     * @param $controller
     * @param $action
     * @deprecated
     */
    public static function strongRedirect($controller, $action)
    {
        $route = \Jet\App\Engine\Core\Router::getRoute();
        if (($route[1] !== $controller OR \Jet\App\Engine\Core\Router::getAction() !== $action) AND empty($route[3])) {

            header("Location: /$controller/$action", true, 301);
            exit();
        }
    }

    /**
     * @param $url
     * @param int $code
     * @deprecated
     */
    public static function redirect($url, $code = 301)
    {
        if (empty($url)) {
            self::homeRedirect();
        }

        header("Location: " . PHPJet::$app->router->getHost() . "/" . $url, true, $code);
        PHPJet::$app->exit();
    }

    /**
     * @deprecated
     */
    public static function homeRedirect()
    {
        header("Location: " . PHPJet::$app->router->getHost(), true, 301);
        PHPJet::$app->exit();
    }

    /**
     * @param $controller
     * @param $action
     * @param int $code
     * todo: i think it should be moved to Router
     * @deprecated
     */
    public static function regularRedirect($controller, $action, $code = 301)
    {
        header("Location: /$controller/$action", true, $code);
        exit();
    }

    /**
     * @deprecated
     */
    public static function refresh()
    {
        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        header("location: $actual_link", true, 301);
        exit();
    }

    /**
     * @return bool
     */
    public static function indexRedirect()
    {
        // Deprecated
        return false;
    }

    /**
     * @param $str
     * @param bool $cut
     * @return string
     */
    public static function makeHandler($str, bool $cut = false)
    {

        if (!$str) {
            return uniqid("handle", true);
        }

        if ($cut) {

            $pos = strpos($str, "-");
            $int = substr(trim($str), 0, $pos);

            if (intval($int)) {
                $new_str = substr(trim($str), $pos + 1);
            }
        }


        $new_str = \Jet\App\Engine\Tools\Utils::rus2Lat((isset($new_str) ? $new_str : $str));
        //$new_str = str_replace(' ', '-', trim($new_str));
        //$new_str = str_replace('\'', '', $new_str);

        $new_str = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $new_str)));

        return strtolower($new_str);
    }

    /**
     * @param string $mailTo
     * @param string $mailFrom
     * @param string $subject
     * @param string $body
     * @param null $data
     * @return bool
     */
    public static function sendMail2(string $mailTo, string $mailFrom, string $subject, string $body, $data = null): bool
    {
        // TODO: System/Mail
        return false;

        $mail = new \PHPMailer(); // create a new object (defined in Vendor)
        $mail->IsSMTP(); // enable SMTP
        $mail->CharSet = "utf-8";
        $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
        $mail->SMTPAuth = true; // authentication enabled
        $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 465; // or 587
        $mail->IsHTML(true);
        $mail->Username = \Jet\App\Engine\Config\Config::$mail['email'];
        $mail->Password = \Jet\App\Engine\Config\Config::$mail['password'];
        $mail->SetFrom($mailFrom, \Jet\App\Engine\Config\Config::$config['site_name']);
        $mail->Subject = $subject;
        $mail->Body = $body;
        if ($data) {
            foreach ($data as $cur) {
                if (empty($cur["handle"])) {

                    $cur['handle'] = "common";
                }
                $mail->AddEmbeddedImage($cur['image'], $cur['handle']);
            }
        }
        $mail->AddAddress($mailTo);
        //$mail->addReplyTo(\Jet\App\Engine\Config\Config::$config['developer_email'], $subject.' (replied)');

        if (!$mail->Send()) {
//            \Jet\App\Engine\Core\System::exceptionToFile($mail->ErrorInfo);
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public static function makeYML()
    {
        // disabled
        return false;

        //require_once ENGINE . 'components/ymlgenerator.php';
        /*
        $yandex = new YandexYML;
        $text = $yandex->generateYML();
        $file = fopen('engine/text.yml', 'a');

        fwrite($file, $text);
        fclose($file);
        */
    }

    /**
     * @param $string
     * @return string
     */
    public static function rus2Lat($string)
    {
        $converter = array(
            'а' => 'a', 'б' => 'b', 'в' => 'v',
            'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
            'и' => 'i', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
            'ь' => '\'', 'ы' => 'y', 'ъ' => '\'',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
            'А' => 'A', 'Б' => 'B', 'В' => 'V',
            'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
            'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
            'И' => 'I', 'Й' => 'Y', 'К' => 'K',
            'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R',
            'С' => 'S', 'Т' => 'T', 'У' => 'U',
            'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
            'Ь' => '\'', 'Ы' => 'Y', 'Ъ' => '\'',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        );
        return strtr($string, $converter);
    }

    /**
     * @param $str
     * @return string
     */
    public static function replaceASCII($str)
    {
        // @todo replace ASCII?
        // I remember that i used to use something more then htmlspecialchars
        return htmlspecialchars($str);
    }

    /**
     * @param $str
     * @return string
     */
    public static function replaceSpecialSymbols($str)
    {
        // wtf
        return htmlspecialchars_decode($str);
    }

    /**
     * @return bool
     * todo: move to Request class
     */
    public static function validateUser()
    {
        $id = Request::getSession("user_id");
        $user = PHPJet::$app->store->loadOne("users", ["users_id" => $id]);
        if ($user['users_session_token'] !== \Jet\App\Engine\Tools\Request::getSession("user_token")) {
            return false;
        }
        return true;
    }

    /**
     * disabled, outdated, deprecated
     */
    public static function createPDF()
    {
        return false;
        include_once HOME . 'libs/pdf/index.php';
    }

    /**
     * @param $file
     * @param null $contentType
     * @return bool
     */
    public static function forcedDownload($file, $contentType = null)
    {
        if (!file_exists($file)) {
            return false;
        }

        if (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Description: File Transfer');

        if ($contentType) {

            header('Content-Type: application/octet-stream');
        } else {

            header('Content-Type: ' . $contentType);
        }

        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));

        readfile($file);
        exit;
    }

    /**
     * @param $str
     * @param $count
     * @return string
     */
    public static function cutString($str, $count)
    {
        mb_internal_encoding('UTF-8');

        if (strlen($str) >= $count) {
            return mb_substr($str, 0, $count) . '...';
        }

        return $str;
    }

    /**
     * @return string
     */
    public static function getLocalTime()
    {

        $translate = array(
            "am" => "дп",
            "pm" => "пп",
            "AM" => "ДП",
            "PM" => "ПП",
            "Monday" => "Понедельник",
            "Mon" => "Пн",
            "Tuesday" => "Вторник",
            "Tue" => "Вт",
            "Wednesday" => "Среда",
            "Wed" => "Ср",
            "Thursday" => "Четверг",
            "Thu" => "Чт",
            "Friday" => "Пятница",
            "Fri" => "Пт",
            "Saturday" => "Суббота",
            "Sat" => "Сб",
            "Sunday" => "Воскресенье",
            "Sun" => "Вс",
            "January" => "Января",
            "Jan" => "Янв",
            "February" => "Февраля",
            "Feb" => "Фев",
            "March" => "Марта",
            "Mar" => "Мар",
            "April" => "Апреля",
            "Apr" => "Апр",
            "May" => "Мая",
            "June" => "Июня",
            "Jun" => "Июн",
            "July" => "Июля",
            "Jul" => "Июл",
            "August" => "Августа",
            "Aug" => "Авг",
            "September" => "Сентября",
            "Sep" => "Сен",
            "October" => "Октября",
            "Oct" => "Окт",
            "November" => "Ноября",
            "Nov" => "Ноя",
            "December" => "Декабря",
            "Dec" => "Дек",
            "st" => "ое",
            "nd" => "ое",
            "rd" => "е",
            "th" => "ое"
        );

        if (func_num_args() > 1) {
            $timestamp = func_get_arg(1);
            return strtr(date(func_get_arg(0), $timestamp), $translate);
        } else {
            return strtr(date(func_get_arg(0)), $translate);
        }
    }

    /**
     * @param string $url
     * @param array $param
     * @param bool $post
     * @return mixed
     */
    public static function cURLCall(string $url, array $param = array(), bool $post = true)
    {

        // todo: move it to Request
        $query = http_build_query($param);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, $post);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }

    // This static function is for counting array length on the bottom level
    // Built-in static function count() unfortunately counting all nodes even if it's array too
    // This static function does not detect infinite recursion automatically
    /**
     * @param $array
     * @return int
     */
    public static function arrayCountLower($array): int
    {

        if (!is_array($array)) {

            return 0;
        }

        $count = 0;

        foreach ($array as $key => $value) {

            if (is_array($value)) {

                $count = self::arrayCountLower($value);
            } else {

                $count++;
            }
        }

        return $count;
    }

    // Custom static function for finding position in string
    // Differ from standard static function is in ability to have an array of needles
    // Sometimes it may be useful
    // It returns the first founded symbol!
    // Returning array of position is in the strposArray() static function

    // @return int
    // @return bool
    /**
     * @param string $haystack
     * @param null $needle
     * @return bool|int|mixed
     */
    public static function strpos(string $haystack = "", $needle = null)
    {
        // If regular string
        if (!is_array($needle)) {
            return strpos($haystack, $needle);
        }

        // if array
        $positions = array();
        foreach ($needle as $item) {
            $strpos = strpos($haystack, $item);
            // because 0 also casts to false
            if ($strpos !== false) {
                $positions[] = $strpos;
            }
        }

        // If founded at least 1 element
        if ($positions) {
            return min($positions);
        }

        return false;
    }

    // For preparing templates
    // Use {{VAR}} to set variables
    // Symbols "{" AND "}" are don't needed to be parameters

    // @return string
    /**
     * @param string $template
     * @param array $find
     * @param array $replace
     * @return string
     */
    public static function tplPrepare(string $template, array $find = array(), array $replace = array()): string
    {
        foreach ($find as $key => $item) {
            $find[$key] = "{{" . $item . "}}";
        }
        $template = str_replace($find, $replace, $template);
        return $template;
    }

    /**
     * @param string $email
     * @return bool
     */
    public static function validateEmail(string $email): bool
    {
        if (!preg_match("/^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i", trim($email))) {
            return false;
        }
        return true;
    }

    /**
     * @param string $url
     * @param string $varName
     * @return string
     * https://stackoverflow.com/questions/1251582/beautiful-way-to-remove-get-variables-with-php
     * todo this function doesn't work properly
     * @deprecated until fixed
     */
    public function removeGETVariableFromURL(string $url, string $varName): string
    {
        return preg_replace('/([?&])' . $varName . '=[^&]+(&|$)/', '', $url);
    }
}
