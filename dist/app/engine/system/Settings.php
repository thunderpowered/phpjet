<?php


namespace Jet\App\Engine\System;

use Jet\App\Engine\ActiveRecord\Tables\Context;
use Jet\PHPJet;

/**
 * Class Settings
 * @package Jet\App\Engine\System
 */
class Settings
{
    /**
     * @var string
     */
    private $tableName = 'settings';
    /**
     * @var array
     */
    private $settings;

    /**
     * Settings constructor.
     * @param bool $loadFull
     * @throws \Exception
     */
    public function __construct(bool $loadFull = false)
    {
        if ($loadFull) {
            $this->getSettings();
        }
    }

    /**
     * @param string $name
     * @param bool $removeSpecialChars
     * @return string
     */
    public function getContext(string $name, bool $removeSpecialChars = true): string
    {
        if (!empty($this->settings[$name])) {
            // if it's not empty there is no way that this element does not exist
            return $this->settings[$name]->value;
        }

        $this->settings[$name] = Context::getOne(['name' => $name], [], [], $removeSpecialChars);
        if (!$this->settings[$name]) {
            return '';
        } else {
            return $this->settings[$name]->value;
        }
    }

    /**
     * @param string $name
     * @param string $value
     * @return bool
     */
    public function setContext(string $name, string $value): bool
    {
        if (!$name || !$value) {
            return false;
        }

        // try to get context
        $this->settings[$name] = Context::getOne(['name' => $name]);
        if (!$this->settings[$name]) {
            // if nothing found -> create new
            $this->settings[$name] = new Context();
            $this->settings[$name]->name = $name;
        }

        $this->settings[$name]->value = $value;
        return $this->settings[$name]->save();
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getSettings(): array
    {
        if (!$this->settings) {
            $this->settings = PHPJet::$app->store->load($this->tableName);
        }
        return $this->settings;
    }
}