<?php


namespace Jet\App\Engine\Tools;

/**
 * Class Action
 * @package Jet\App\Engine\Tools
 * @@description  Tool for writing/reading action data (admin actions, user actions, whatever)
 * todo implement
 */
class Action
{
    public const ACTION_TYPE_ADMIN = 'admin';
    public const ACTION_TYPE_USER = 'user';
    public const ACTION_TYOE_OTHER = 'other';

    /**
     * Action constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param string $type
     * @return bool
     */
    public function submitAction(string $type = self::ACTION_TYOE_OTHER): bool
    {
        // todo
        return false;
    }
}