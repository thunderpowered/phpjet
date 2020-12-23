<?php


namespace CloudStore\App\MVC\Client\Widgets;


use CloudStore\App\Engine\ActiveRecord\Tables\Mods;
use CloudStore\App\Engine\Core\Widget;
use CloudStore\CloudStore;

/**
 * Class WidgetHot
 * @package CloudStore\App\MVC\Client\Widgets
 */
class WidgetHot extends Widget
{
    /**
     * @var int
     */
    private $differenceToNew = 86400;
    /**
     * @var string
     */
    private $modsMainPage = 'mods/';
    /**
     * @var string
     */
    private $usersMainPage = 'users/';
    /**
     * @var string
     */
    private $host;
    /**
     * WidgetHot constructor.
     * @param Widget|null $widget
     */
    public function __construct(Widget $widget = null)
    {
        parent::__construct($widget);
        $this->host = CloudStore::$app->router->getHost() . '/';
    }

    /**
     * @return string
     */
    public function getNewMods(): string
    {
        $mods = Mods::getJoin([
            ['LEFT', 'games', ['id' => 'games_id']],
            ['LEFT', 'users', ['id' => 'users_id']]
        ], [], ['since' => 'DESC'], [0, 20]);
        $currentTime = time();
        foreach ($mods as $key => $mod) {

            $mods[$key]->new = false;

            // if mod uploaded recently - mark it as new
            $time = strtotime($mod->since);
            $timeDifference = $currentTime - $time;
            if ($timeDifference <= $this->differenceToNew) {
                $mods[$key]->new = true;
            }

            // set proper url
            $mods[$key]->url = $this->getModFullURL($mod->url);
            $mods[$key]->user_url = $this->getUserFullURL($mod->username);
        }

        return $this->render('widget_hot_new', [
            'mods' => $mods
        ]);
    }

    /**
     * @param string $modURL
     * @return string
     */
    private function getModFullURL(string $modURL): string
    {
        return $this->host . $this->modsMainPage . $modURL;
    }

    /**
     * @param string $userURL
     * @return string
     */
    private function getUserFullURL(string $userURL): string
    {
        return $this->host . $this->usersMainPage . $userURL;
    }
}