<?php


namespace Jet\App\MVC\Client\Widgets;

use Jet\App\Engine\ActiveRecord\Tables\Items;
use Jet\App\Engine\Core\Widget;
use Jet\App\MVC\Client\Models\ModelItems;
use Jet\PHPJet;

/**
 * Class WidgetMenu
 * @package Jet\App\MVC\Client\Widgets
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
        $this->host = PHPJet::$app->router->getHost() . '/';

        // i still this about it, not sure it is a good idea to use model in widgets. seems weird to me. we'll see
        if (isset($this->parent->controller) && property_exists($this->parent->controller, 'modelItems')) {
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
        $menu = PHPJet::$app->system->settings->getContext('menu_header', false);
        if (!$menu) {
            return '';
        }

        $menu = json_decode($menu, true);
        foreach ($menu as $key => $item) {
            $menu[$key]['url'] = PHPJet::$app->router->getHost() . $item['url'];
        }
        return $this->render('widget_menu_header', [
            'menu' => $menu
        ]);
    }

    /**
     * @return string
     */
    public function getItemRootList(): string
    {
        return $this->getItemList(true);
    }

    /**
     * @param bool $onlyRoot
     * @return string
     */
    public function getItemList(bool $onlyRoot = false): string
    {
        $condition = [];
        if ($onlyRoot) {
            $condition = ['parent' => 0];
        }

        $items = Items::getJoin([
            ['LEFT', 'taxonomy', ['items_id' => 'id']]
        ], $condition, ['since' => 'DESC'], [0, 30]);

        foreach ($items as $key => $item) {
            $items[$key]->new = $this->modelItems->isThisItemNew($item->since);
            $items[$key]->url = $this->modelItems->getItemFullURL($item->url);
            $items[$key]->icon = PHPjet::$app->tool->utils->getThumbnailLink($item->icon);
        }

        return $this->render('widget_menu_item_list', [
            'items' => $items
        ]);
    }
}