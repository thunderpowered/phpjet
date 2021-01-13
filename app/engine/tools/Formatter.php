<?php


namespace Jet\App\Engine\Tools;

use Jet\App\Engine\System\Settings;
use Jet\PHPJet;

/**
 * Class StringFormatter
 * @package Jet\App\Engine\Tools
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
        $priceTemplate = PHPJet::$app->system->request->getSESSION($this->priceTemplateSettingsName);
        if (!$priceTemplate) {
            $priceTemplate = PHPJet::$app->system->settings->getContext($this->priceTemplateSettingsName);
            if (!$priceTemplate) {
                return $number;
            }

            PHPJet::$app->system->request->setSESSION($this->priceTemplateSettingsName, $priceTemplate);
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

    /**
     * @param array $array
     * @return string
     */
    public function arrayToListString(array $array): string
    {
        $result = "";
        foreach ($array as $key => $item) {
            $result .= "$key: ";
            if (is_array($item)) {
                $item = $this->arrayToListString($item);
            }
            $result .= $item . "\r\n";
        }
        return $result;
    }

    /**
     * @param string $date
     * @param string $format
     * @return string
     */
    public function formatDateString(string $date, $format = 'd.m.Y H:i:s'): string
    {
        return date($format, strtotime($date));
    }
}