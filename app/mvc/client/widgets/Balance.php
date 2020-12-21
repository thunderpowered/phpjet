<?php

namespace CloudStore\App\Engine\Components;

use CloudStore\App\Engine\Core\Component;

class Balance extends Component
{

    public static function getTariffInfo($id)
    {

        // Do nothing
        return false;

        // If user's not logged

        if (empty($id)) {

            return false;
        }

        // Get information about user

        $user = \CloudStore\App\Engine\Components\S::execGet("SELECT * FROM users u LEFT JOIN tariffs t ON u.users_tariff = t.id WHERE u.users_id = :id AND u.store = :store", [":id" => $id, ":store" => \CloudStore\App\Engine\Config\Config::$config["site_id"]])[0];

        if (!$user)
            return false;

        /* BALANCE INFO */

        // Days
        if ($user["daily_payment"] === "0.00") {

            $days = $user["users_tariff_days"];
        } else {

            $days = floor($user["users_tariff_balance"] / $user["daily_payment"]);
        }

        // 1. Date of blocking
        $start = new DateTime($user["users_tariff_start"]);
        $final = $start->add(new DateInterval("P" . $days . "D"));

        // 2. Interval between today and day of final
        $today = new DateTime(date("Y-m-d"));

        $interval = $today->diff($final);

        if ($interval->format("%R") === "-") {

            $days = 0;
        } else {

            $days = $interval->format("%a");
        }

        // Balance
        $balance = \CloudStore\App\Engine\Components\Utils::asSimplePrice($user["users_tariff_balance"] - ($user["users_tariff_days"] - $days) * $user["daily_payment"]);


        $tariff = [
            "name" => $user["name"],
            "days" => $days,
            "daily_payment" => $user["daily_payment"],
            "balance" => $balance
        ];

        return $tariff;
    }
}
