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

    /**
     * @param string $string
     * @return string
     */
    public function anyStringToSearchString(string $string): string
    {
        // trim it
        $string = trim($string);
        // remove multiple spaces
        $string = preg_replace('/\s+/', ' ', $string);
        // remove all symbols except latin/cyrillic letters and numbers
        $string = preg_replace('/[^A-Za-zА-Яа-яЁё0-9-]/u', ' ', $string);
        return $string;
    }

    /**
     * @param string $string
     * @return string
     */
    public function anyStringToURLString(string $string): string
    {
        $string = trim($string);
        // replace all spaces with score
        $string = preg_replace('/\s+/', '-', $string);
        // remove all characters that not comply with regex (A-z, 0-9, _, -)
        $string = preg_replace('/[^A-Za-z0-9_-]/', '', $string);
        // cast to lowercase
        $string = strtolower($string);
        return $string;
    }

    /**
     * @param string $email
     * @return bool
     */
    public function validateEmail(string $email): bool
    {
        return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}