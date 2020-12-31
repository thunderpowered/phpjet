<?php


namespace CloudStore\App\MVC\Client\Widgets;

use CloudStore\App\Engine\ActiveRecord\Tables\Items;
use CloudStore\App\Engine\Core\Widget;
use CloudStore\App\MVC\Client\Models\ModelItems;
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
    private $host;
    /**
     * @var ModelItems
     */
    private $modelItems;
    /**
     * WidgetMenu constructor.
     * @param Widget|null $widget
     */
    public function __construct(Widget $widget = null)
    {
        parent::__construct($widget);
        $this->host = CloudStore::$app->router->getHost() . '/';

        // i still this about it, not sure it is a good idea to use model in widgets. seems weird to me. we'll see
        if (property_exists($this->parent->controller, 'modelItems')) {
            $this->modelItems = $this->parent->controller->modelItems;
        } else {
            $this->modelItems = new ModelItems();
        }
    }

    /**
     * @return string
     */
    public function getHeaderMenu(): string
    {
        $menu = CloudStore::$app->system->settings->getContext('menu_header', false);
        if (!$menu) {
            return '';
        }

        $menu = json_decode($menu, true);
        foreach ($menu as $key => $item) {
            $menu[$key]['url'] = CloudStore::$app->router->getHost() . $item['url'];
        }
        return $this->render('widget_menu_header', [
            'menu' => $menu
        ]);
    }

    /**
     * @return string
     */
    public function getItemList(): string
    {
        $items = Items::getJoin([
            ['LEFT', 'taxonomy', ['id' => 'item_id']]
        ], [], ['since' => 'DESC'], [0, 30]);

        foreach ($items as $key => $item) {
            $items[$key]->new = $this->modelItems->isThisItemNew($item['since']);
            $items[$key]->url = $this->modelItems->getItemFullURL($item['url']);
        }

        return $this->render('widget_menu_item_list', [
            'items' => $items
        ]);
    }
}