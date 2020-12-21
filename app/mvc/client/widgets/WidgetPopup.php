<?php

namespace CloudStore\App\MVC\Client\Widgets;

use CloudStore\App\Engine\Core\Widget;

class WidgetPopup extends Widget
{

    public $data;
    public $videos;
    public $html;

    public function __construct()
    {

        /* TEMP! */

        return [];

        $this->data = \CloudStore\App\Engine\Components\Getter::getFreeData("SELECT * FROM settings WHERE settings_name = 'help_popup_link' OR settings_name = 'help_popup_name'", null, false, true);

        $this->html = \CloudStore\App\Engine\Components\Getter::getFreeData("SELECT * FROM settings WHERE settings_name = 'help_popup_html'", null, true, false)['settings_value'];

        $this->videos = \CloudStore\App\Engine\Components\Getter::getFreeData("SELECT * FROM help_popups WHERE popup_visible = '1' AND popup_admin = '0' ORDER BY popup_order", null, false, false);
    }

    public function link()
    {
        return $this->data[0]['settings_value'];
    }

    public function name()
    {
        return $this->data[1]['settings_value'];
    }

    public function videos()
    {
        return $this->videos;
    }

    public function html()
    {
        return $this->html;
    }
}
