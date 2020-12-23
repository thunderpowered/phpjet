<?php


namespace CloudStore\App\MVC\Client\Widgets;

use CloudStore\App\Engine\ActiveRecord\Tables\Games;
use CloudStore\App\Engine\Core\Widget;
use CloudStore\CloudStore;

/**
 * Class WidgetMenu
 * @package CloudStore\App\MVC\Client\Widgets
 */
class WidgetMenu extends Widget
{
    /**
     * @var string
     */
    private $gamesMainPage = 'games/';
    /**
     * @var string
     */
    private $host;
    /**
     * WidgetMenu constructor.
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
    public function getGameList(): string
    {
        $games = Games::get([], ['name' => 'ASC']);
        foreach ($games as $key => $game) {
            $games[$key]->url = $this->getGameFullURL($game->url);
        }
        return $this->render('widget_menu_games', [
            'games' => $games
        ]);
    }

    /**
     * @param string $gameURL
     * @return string
     */
    private function getGameFullURL(string $gameURL): string
    {
        return $this->host . $this->gamesMainPage . $gameURL;
    }
}