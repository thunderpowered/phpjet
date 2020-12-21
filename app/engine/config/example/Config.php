<?php

namespace CloudStore\App\Engine\Config;

use CloudStore\App\Engine\Core\System;

class Config
{

    // Database
    public static $db = [
        'host' => 'localhost',
        'username' => 'example_user',
        'database' => 'example_database',
        'password' => 'example_password'
    ];
    //Other
    public static $config = [
        'developer_email' => 'example@dev.com',
        'allowed_controllers' => [
            'father'
        ],
        'protocol' => 'https://',
        'components' => [
            'getter',
            'setter',
            'paginator',
            'seo',
            'request',
            'business',
            'help',
            'filter',
            'store',
            'products'
        ],
        'widgets' => [
            'widget_menu',
            'widget_cart',
            'widget_sorting',
            'widget_theme',
            'widget_filter',
            'widget_account',
            'widget_compilation',
            'widget_slider',
            'widget_pages',
            'widget_message'
        ],
        'points' => 300,
        'orders_location' => 'files/orders/',
        // Secret key
        // TODO: generate for each website
        'http_key' => 'example_key'
    ];
    public static $theme = [
        'layout' => 'example',
        'views' => 'example',
        'mail' => 'example',
        'static' => 'example',
        'parts' => 'example'
    ];
    //Email
    public static $mail = [];
    // Points to vendor site
    public static $vendor = "https://example.com";
    //Development and updates
    public static $dev = [
        "FatherKey" => "example_key"
    ];
    //Sites (dev) (will be in database)
    public static $sites = [
        [
            'app_name' => 'example',
            'app_key' => 'example'
        ],
    ];

    public static function Password()
    {
        return [
            'salt' => Utils::generate_token()
        ];
    }

    public static function setConfig()
    {

        return false;
    }
}
