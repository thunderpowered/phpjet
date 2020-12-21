<?php

namespace CloudStore\App\Engine\Ajax\Handlers;

class AjaxUser
{

    public $countries_html;
    public $regions_html;

    public function delete_address()
    {
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            return false;
        }

        $id = \CloudStore\App\Engine\Components\Utils::removeSpecialChars($_POST['id']);
        $csrf = \CloudStore\App\Engine\Components\Utils::removeSpecialChars($_POST['csrf']);
        $ip = \CloudStore\App\Engine\Core\System::getUserIP();
        $user_id = \CloudStore\App\Engine\Components\Request::getSession('user_id');

        if (!\CloudStore\App\Engine\Components\Utils::validateToken($csrf)) {
            return false;
        }

        if (\CloudStore\App\Engine\Components\S::delete("user_addresses", ["address_id" => $id, "address_user" => $user_id])) {
            return 1;
        }
        return 0;
    }

    public function change_address()
    {
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            return false;
        }

        $id = \CloudStore\App\Engine\Components\Request::post('id');
        $csrf = \CloudStore\App\Engine\Components\Request::post('csrf');

        $user_id = \CloudStore\App\Engine\Components\Request::getSession('user_id');

        if (!\CloudStore\App\Engine\Components\Utils::validateToken($csrf)) {
            return false;
        }

        $result = \CloudStore\App\Engine\Components\S::loadOne("user_addresses", ["address_id" => $id, "address_user" => $user_id]);

        $countries = \CloudStore\App\Engine\Components\S::load("countries", ["country_avail" => 1]);

        //TEMPORARY!

        $sql = "SELECT * FROM region WHERE country_id IN ("
            . "SELECT country_id FROM countries WHERE country_handle = :handle) AND region_avail = '1' AND store = :store";
        $regions = \CloudStore\App\Engine\Components\S::execGet($sql, [
            ":handle" => $result['address_country'], ":store" => \CloudStore\App\Engine\Config\Config::$config["site_id"]
        ]);

        foreach ($countries as $country) {

            $selected = $country['country_handle'] === $result['address_country'] ? 'selected' : '';

            $this->countries_html .= '<option ' . $selected . ' class="select_country" value="' . $country['country_handle'] . '" >' . $country['country_name'] . '</option>';
        }

        $result['countries_html'] = $this->countries_html;

        foreach ($regions as $region) {

            $selected = $region['region_handle'] === $result['address_region'] ? 'selected' : '';

            $this->regions_html .= '<option ' . $selected . ' class="select_country" value="' . $region['region_handle'] . '" >' . $region['region_name'] . '</option>';
        }

        $result['regions_html'] = $this->regions_html;

        if ($result) {
            return json_encode($result, JSON_UNESCAPED_UNICODE);
        }
    }
}
