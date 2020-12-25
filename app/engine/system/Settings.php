<?php


namespace CloudStore\App\Engine\System;

use CloudStore\App\Engine\ActiveRecord\Tables\Context;
use CloudStore\CloudStore;

/**
 * Class Settings
 * @package CloudStore\App\Engine\System
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
        // just to prevent setting empty values
        if (!$name || !$value) {
            return false;
        }

        $this->settings[$name] = new Context();
        $this->settings[$name]->value = $value;
        return $this->settings[$name]->save();
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        if (!$this->settings) {
            $this->settings = CloudStore::$app->store->load($this->tableName);
        }
        return $this->settings;
    }
}