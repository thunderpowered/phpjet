<?php


namespace CloudStore\App\Engine\Tools;

use CloudStore\App\Engine\System\Settings;
use CloudStore\CloudStore;

/**
 * Class StringFormatter
 * @package CloudStore\App\Engine\Tools
 */
class Formatter
{
    /**
     * @var string
     */
    private $priceTemplateSettingsName = 'price_template';

    /**
     * @param float $number
     * @return string
     */
    public function numberAsPriceString(float $number)
    {
        $priceTemplate = CloudStore::$app->system->request->getSESSION($this->priceTemplateSettingsName);
        if (!$priceTemplate) {
            $priceTemplate = CloudStore::$app->system->settings->getContext($this->priceTemplateSettingsName);
            if (!$priceTemplate) {
                return $number;
            }

            CloudStore::$app->system->request->setSESSION($this->priceTemplateSettingsName, $priceTemplate);
        }

        $resultString = @number_format($number, intval($priceTemplate['decimals']), $priceTemplate['decimal_delimiter'], $priceTemplate['thousands_delimiter']) . ' ' . $priceTemplate['price_currency'];
        return $resultString;
    }
}