<?php

/**
 *  Class ClientManager is for handling all questions for product Clients
 *  Balance information, current tariff etc.
 */

namespace CloudStore\App\Engine\Tools;

use CloudStore\App\Engine\Config\Config;
use CloudStore\CloudStore;

/**
 * Class ClientManager
 * @package CloudStore\App\Engine\Tools
 */
class ClientManager
{

    /**
     * @return bool
     */
    public function checkBalance()
    {
        // todo: temporary disabled
        return true;

        $store = CloudStore::$app->store->loadOne("stores", ["store" => Config::$config["site_id"]]);

        $start = $store["tariff_start"];
        $days = $store["tariff_days"] * 30;

        if (empty($start) || !$days) {
            return false;
        }

        try {
            $start = new \DateTime($start);
            $final = $start->add(new \DateInterval("P" . $days . "D"));

            // 2. Interval between today and day of final
            $today = new \DateTime(date("Y-m-d"));
            $interval = $today->diff($final);

            return $interval->format("%R") === "-" ? false : true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
