<?php

namespace CloudStore\App\MVC\Client\Widgets;

use CloudStore\App\Engine\Config\Config;
use CloudStore\App\Engine\Core\Router;
use CloudStore\App\Engine\Core\Store;
use CloudStore\App\Engine\Core\Widget;
use CloudStore\CloudStore;

/**
 * Class WidgetMenu
 * @package CloudStore\App\MVC\Client\Widgets
 */
class WidgetMenu extends Widget
{
    /**
     * @var array
     */
    public $menu_ids;
    /**
     * @var array
     */
    public $ids;
    /**
     * @var array
     */
    public $menu;
    /**
     * @var array
     */
    private $navigationList = [];
    /**
     * @var array
     */
    private $breadCrumbs = [];
    /**
     * @var string
     */
    private $menuItem;
    /**
     * @var array
     */
    private $backToMain = ["title" => "Главная", "url" => ""];
    /**
     * @var array
     */
    private $amountCache;
    /**
     * @var int
     */
    private $categoryId;

    /**
     * WidgetMenu constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getWidgetMain(): string
    {
        if (!$this->menu) {
            $this->menu = $this->getMenu();
        }

        return $this->render("widget_menu_main", [
            "menu" => $this->menu
        ]);
    }

    /**
     * @return string
     */
    public function getWidgetMobile(): string
    {
        if (!$this->menu) {
            $this->menu = $this->getMenu();
        }

        return $this->render("widget_menu_mobile", [
            "menu" => $this->menu
        ]);
    }

    /**
     * @return string
     */
    public function getWidget(): string
    {
        if (!$this->menu) {
            $this->menu = $this->getMenu();
        }

        return $this->render("widget_menu", [
            "menu" => $this->menu
        ]);
    }


    /**
     * @return array
     * menu contains next structure: section -> column -> item
     * @todo i'm definitely sure that it is possible to simplify this function, but i just have no time for that at the moment
     * @deprecated until fixed
     */
    public function getMenu(): array
    {
        if ($this->menu) {
            return $this->menu;
        }

        $menu = CloudStore::$app->store->load("menu", [], ["menu_order" => "ASC"]);
        if (!$menu) {
            return [];
        }

        // sorting menu sections
        $zeros = [];
        foreach ($menu as $c_key => $c_value) {
            if ($c_value["menu_order"] === "0") {
                $zeros[] = $menu[$c_key];
                unset($menu[$c_key]);
            }
        }
        $menu = array_merge($menu, $zeros);

        // getting columns
        foreach ($menu as $menu_item) {
            $this->menu_ids[] = $menu_item['menu_id'];
        }

        $placeholders = implode(',', array_fill(0, count($menu), '?'));
        $this->menu_ids[] = Config::$config["site_id"];
        $columns = CloudStore::$app->store->execGet("SELECT * FROM menu_columns WHERE menu_id IN ($placeholders) AND store = ? ORDER BY column_order ASC", $this->menu_ids);

        if (!$columns) {
            for ($i = 0, $c = count($menu); $i < $c; $i++) {
                $this->menu[$i]['menu_name'] = $menu[$i]['menu_name'];
                $this->menu[$i]['menu_logo'] = $menu[$i]['menu_logo'];
            }
            return $this->menu;
        }

        // sorting columns
        if ($columns) {
            $zeros = [];
            foreach ($columns as $c_key => $c_value) {
                if ($c_value["column_order"] === "0") {
                    $zeros[] = $columns[$c_key];
                    unset($columns[$c_key]);
                }
            }
            $columns = array_merge($columns, $zeros);
        }

        foreach ($columns as $column) {
            $this->ids[] = $column['column_id'];
        }

        // getting items
        $placeholders = implode(',', array_fill(0, count($columns), '?'));
        $sql = "SELECT * FROM menu_columns_items i LEFT JOIN category c ON i.item_category = c.category_id AND i.store = c.store WHERE i.column_id IN ($placeholders) AND i.store = ? ORDER BY i.item_order ASC";

        $this->ids[] = Config::$config["site_id"];
        $items = CloudStore::$app->store->execGet($sql, $this->ids);

        // Load fucking products
        $_counts = CloudStore::$app->store->execGet(
            "SELECT pc.category_id, COUNT(pc.id) FROM menu_columns_items i 
                    INNER JOIN products_category pc ON i.item_category = pc.category_id AND i.store = pc.store
                    INNER JOIN products p ON pc.id = p.id AND p.store = pc.store
                    WHERE i.column_id IN ($placeholders) AND p.avail <> 0 AND i.store = ? GROUP BY (pc.category_id)"
            , $this->ids);

        $counts = [];
        if ($_counts) {
            foreach ($_counts as $key => $value) {
                $counts[$value["category_id"]] = $value["COUNT(pc.id)"];
            }
        }

        for ($i = 0, $c = count($menu); $i < $c; $i++) {
            $this->menu[$i]['menu_name'] = $menu[$i]['menu_name'];
            $this->menu[$i]['menu_logo'] = $menu[$i]['menu_logo'];
            $this->menu[$i]['menu_cover'] = $menu[$i]['menu_cover'];
            $this->menu[$i]['menu_cover_link'] = $menu[$i]['menu_cover_link'];

            for ($j = 0, $n = count($columns); $j < $n; $j++) {
                if ($columns[$j]['menu_id'] !== $menu[$i]['menu_id']) {
                    continue;
                }

                $this->menu[$i]['columns'][$j]['column_id'] = $columns[$j]['column_id'];
                for ($k = 0, $p = count($items); $k < $p; $k++) {
                    if ($items[$k]['column_id'] !== $columns[$j]['column_id']) {
                        continue;
                    }

                    if ($items[$k]['item_category']) {
                        if ($menu[$i]['menu_counter'] === "1" && isset($counts[$items[$k]['item_category']])) {
                            $items[$k]['counter'] = $counts[$items[$k]['item_category']];
                        } else {
                            // Set empty value
                            $items[$k]['counter'] = null;
                        }
                    }
                    $this->menu[$i]['columns'][$j]['items'][] = $items[$k];
                }

                /* SORTING ITEMS */
                if (!empty($this->menu[$i]['columns'][$j]['items'])) {
                    $zeros = [];
                    foreach ($this->menu[$i]['columns'][$j]['items'] as $c_key => $c_value) {
                        if ($c_value["item_order"] === "0") {
                            $zeros[] = $this->menu[$i]['columns'][$j]['items'][$c_key];
                            unset($this->menu[$i]['columns'][$j]['items'][$c_key]);
                        }
                    }
                    $this->menu[$i]['columns'][$j]['items'] = array_merge($this->menu[$i]['columns'][$j]['items'], $zeros);
                }
            }
        }

        return $this->menu;
    }

    /**
     * @return string
     */
    public function getMenuTree(): string
    {
        $tree = CloudStore::$app->store->loadOne("settings", ["settings_name" => "menu"], false);
        if (!$tree) {
            return '';
        }

        $tree = json_decode($tree["settings_value"]);
        $list = $this->buildTree($tree);

        return $this->render("widget_menu_tree", [
            "list" => $list
        ]);
    }

    /**
     * @param $tree
     * @param bool $isSub
     * @param bool $id
     * @return string
     */
    private function buildTree($tree, $isSub = false, $id = false): string
    {
        if (!$tree) {
            return "";
        }

        $collapse = "";
        $class = "";

        if ($isSub) {
            $collapse = "collapse";
            $class = "menu-tree__list--lower";
        }


        $list = "<ul id='list-{$id}' class='menu-tree__list list-group list-group-root {$collapse} {$class}'>";

        foreach ($tree as $key => $value) {
            $subList = null;
            $link = false;

            if ($value->children && empty($value->category_id)) {
                // @todo store the count
                $link = "<a href='#list-{$value->id}' data-toggle='collapse' class='menu-tree__counter menu-tree__down-mark badge badge-primary badge-pill'><i class=\"fa fa-chevron-down\"></i></a>";
                $subList = $this->buildTree($value->children, true, $value->id);

            } else if (!empty($value->category_id)) {
                if ($value->children) {
                    $counter = "<i class=\"fa fa-chevron-down\"></i>";
                    $class = "menu-tree__down-mark";
                } else {
                    $counter = $this->getProducts($value->category_id);
                    $class = "";
                }
                $link = "<a href='#list-{$value->id}' data-toggle='collapse' class='menu-tree__counter {$class} badge badge-primary badge-pill'>{$counter}</a>";
                $subList = $this->buildCategories($value->children, $value->id);
            } else if ($value->url) {
                $counter = $this->getProducts($value->url);
                $link = "<a href='#list-{$value->id}' data-toggle='collapse' class='menu-tree__counter badge badge-primary badge-pill'>{$counter}</a>";
            }

            if ($value->image && empty($value->parent)) {
                $icon = "<div class='menu-tree__icon' style='background-image:url(" . CloudStore::$app->tool->utils->getThumbnailLink($value->image) . ")'></div>";
            } else {
                $icon = null;
            }

            if (!empty($value->category) && !empty($value->category_handle)) {
                $url = CloudStore::$app->router->getHost() . '/catalog/' . $value->category_handle;
            } else {
                $url = CloudStore::$app->router->getHost() . '/catalog/' . $value->url;
            }

            $list .= "<li data-id='{$value->id}' class='menu-tree__item'>
                {$icon}
                <a href='{$url}' class='menu-tree__link list-group-item' >{$value->title}</a>
                {$link}
                <div class='p-clearfix'></div>
                {$subList}
                </li>";
        }

        $list .= "</ul>";

        return $list;

    }

    /**
     * @return string
     */
    public function getMenuTiles(): string
    {
        $menu = CloudStore::$app->store->load("menu_tree", ["parent" => 0]);
        if (!$menu) {
            return '';
        }

        foreach ($menu as $key => $value) {
            if ($value["category"]) {
                $menu[$key]["url"] = $value["category"];
            }
        }

        return $this->render("widget_menu_tiles", ["menu" => $menu]);
    }

    /**
     * @return string
     */
    public function getNavigation(): string
    {
        $this->setNavigation();
        if (!$this->menuItem) {
            return '';
        }

        $path = $this->getNavigationPath($this->menuItem);
        return $this->render("widget_navigation", [
            "path" => $path
        ]);

    }

    /**
     * @param array $path
     * @return string
     * @deprecated
     */
    public function getBreadCrumbsCustom(array $path = []): string
    {
        if (!$path) {
            $path = [["Страница" => CloudStore::$app->router->getURL()]];
        }

        array_unshift($path, ["Главная" => CloudStore::$app->router->getHost()]);
        foreach ($path as $key => $value) {
            $title = array_keys($value)[0] ?? "";
            $url = $value[$title] ?? "";
            $path[$key] = [
                "url" => $url,
                "title" => $title
            ];
        }
        return $this->render("widget_breadcrumbs_custom", [
            "path" => $path
        ]);
    }

    /**
     * @param bool $product
     * @return string
     */
    public function getBreadCrumbs(bool $product = false): string
    {
        if (!$this->menuItem) {
            $this->setNavigation($product);
        }

        $path = $this->getBreadCrumbsPath($this->menuItem);
        $path = array_reverse($path);

        return $this->render("widget_navigation_breadcrumbs", [
            "path" => $path
        ]);
    }

    /**
     * @param array $children
     * @param $id
     * @return string
     */
    private function buildCategories(array $children = [], $id): string
    {
        if (!$children) {
            return "";
        }

        $list = "<ul id='list-{$id}' class='menu-tree__list list-group list-group-root collapse menu-tree__list--lower'>";
        foreach ($children as $child) {

            $subList = $this->buildCategories($child->children, $child->id);
            if ($subList) {
                $counter = "<i class=\"fa fa-chevron-down\"></i>";
                $class = "menu-tree__down-mark";
            } else {
                $counter = $this->getProducts($child->category_handle);
                $class = "";
            }

            $link = "<a href='#list-{$child->id}' data-toggle='collapse' class='menu-tree__counter badge {$class} badge-primary badge-pill'>{$counter}</a>";

            $list .= "<li data-id='{$child->id}' class='menu-tree__item'>
                <a href='" . CloudStore::$app->router->getHost() . "/catalog/{$child->category_handle}' class='menu-tree__link list-group-item' >{$child->name}</a>
                {$link}
                <div class='p-clearfix'></div>
                {$subList}
                </li>";
        }

        $list .= "</ul>";

        return $list;
    }

    /**
     * @param string $url
     * @return int
     * @todo method is too long. reduce number of mysql-queries
     */
    private function getProducts(string $url = ""): int
    {
        if (!$url) {
            return 0;
        }

        $url = (int)$url;

        // Cache
        $cachedAmount = $this->getCachedProducts($url);
        if ($cachedAmount) {
            return $cachedAmount;
        }

        $category = CloudStore::$app->store->loadOne("category", ["category_id" => $url]);
        if (!$category) {
            return 0;
        }

        $amount = CloudStore::$app->store->count("products_category", ["category_id" => $category["category_id"]]);
        if (!$amount) {
            return 0;
        }

        $this->setCachedProducts($url);
        return $amount;
    }

    /**
     * @param string $url
     * @return bool|mixed
     */
    private function getCachedProducts(string $url = "")
    {
        if (!$this->amountCache) {
            $cache = CloudStore::$app->store->loadOne("settings", ["settings_name" => "amount_cached"], false);
            if (!$cache["settings_value"]) {
                return false;
            }

            $this->amountCache = json_decode($cache["settings_value"], true);
        }

        if ($url && !empty($this->amountCache[$url])) {
            return $this->amountCache[$url];
        }

        return false;
    }

    private function setCachedProducts($url)
    {
        $category = CloudStore::$app->store->loadOne("category", ["category_id" => $url]);
        if (!$category) {
            return false;
        }

        $cachedAmountDB = CloudStore::$app->store->loadOne("settings", ["settings_name" => "amount_cached"], false);
        if ($cachedAmountDB) {
            $cachedAmount = json_decode($cachedAmountDB["settings_value"], true);
        } else {
            $cachedAmount = array();
        }

        $cachedAmount[$url] = CloudStore::$app->store->count("products_category", ["category_id" => $url]);
        $cachedAmount = json_encode($cachedAmount);
        if ($cachedAmountDB) {
            CloudStore::$app->store->update("settings", ["settings_value" => $cachedAmount], ["settings_name" => "amount_cached"]);
        } else {
            CloudStore::$app->store->collect("settings", ["settings_value" => $cachedAmount, "settings_name" => "amount_cached"]);
        }

        return true;
    }

    private function setNavigation(bool $product = false)
    {
        // Get Category from GET
        $category = CloudStore::$app->system->request->getGET("category_id");
        if (!$category) {
            if ($product) {
                $product = CloudStore::$app->router->getLastRoutePart();
                $product = (int)$product;
                $category = CloudStore::$app->store->loadOne("products_category", ["id" => $product]);
                if (!$category) {
                    return false;
                } else {
                    $category = $category["category_id"];
                }
            } else {
                $category = CloudStore::$app->router->getLastRoutePart();
            }
        }

        if (!$category) {
            return false;
        }

        $this->categoryId = (int)$category;
        $id = "%" . $this->categoryId . "%";

        $menuItem = CloudStore::$app->store->execGet("SELECT * FROM menu_tree WHERE (url LIKE ? OR category LIKE ?) AND store = ?", [$id, $id, Config::$config["site_id"]]);

        // Maybe category exists?
        if (!$menuItem) {

            $this->menuItem = CloudStore::$app->store->loadOne("category", ["category_id" => $this->categoryId]);
            if (!$this->menuItem) {
                return false;
            }

            // Parent of parent (temporary)
            // if item has parent category then disable it (deletes lowest item)
//            if ($this->menuItem["parent"] && !$product) {
//                $menuItem = CloudStore::$app->store->loadOne("category", ["category_id" => $this->menuItem["parent"]]);
//                if ($menuItem) {
////                    $this->menuItem = $menuItem;
//                }
//            }

            $this->menuItem["title"] = $this->menuItem["name"];
            $this->menuItem["url"] = $this->menuItem["category_handle"];
            $this->menuItem["category"] = $this->menuItem["category_id"];
        } else {

            $this->menuItem = $menuItem[0];
            if ($this->menuItem["category"]) {
                $category = CloudStore::$app->store->loadOne("category", ["category_id" => $this->menuItem["category"]]);
                $this->menuItem["title"] = $category["name"];
                $this->menuItem["url"] = $category["category_handle"];
            }
        }

        return true;
    }

    private function getBreadCrumbsPath($menuItem): array
    {
        if (!$menuItem) {
            return [];
        }

        $isCategory = !empty($menuItem["category_id"]);

        // 1. If it's a category try to find it in the menu
        // 2. If found interchange element with element from menu
        // 3. If not, do not something

        if ($isCategory) {
            $_menuItem = CloudStore::$app->store->loadOne("menu_tree", ["category" => $menuItem["category_id"]]);

            if ($_menuItem) {
                $_menuItem["url"] = $menuItem["category_handle"];
                $menuItem = $_menuItem;
                $parent = CloudStore::$app->store->loadOne("menu_tree", ["id" => $menuItem["parent"]]);
            } else {
                $parent = CloudStore::$app->store->loadOne("category", ["category_id" => $menuItem["parent"]]);
                $menuItem["title"] = $menuItem["name"];
                $menuItem["url"] = $menuItem["category_handle"];
            }

        } else {
            $parent = CloudStore::$app->store->loadOne("menu_tree", ["id" => $menuItem["parent"]]);
        }

        $this->breadCrumbs[] = $menuItem;

        if (!$menuItem["parent"]) {
            $this->breadCrumbs[] = $this->backToMain;
            return $this->breadCrumbs;
        }

        if ($parent) {
            $this->getBreadCrumbsPath($parent);
        }

        return $this->breadCrumbs;
    }

    private function getNavigationPath($menuItem): array
    {
        // if category
        if (!empty($menuItem["category"])) {
            if ($menuItem["parent"]) {
                $_menuItem = CloudStore::$app->store->loadOne("category", ["category_id" => $menuItem["parent"]]);
                if ($_menuItem) {
                    $_menuItem["url"] = $menuItem["category_handle"];
                    $menuItem = $_menuItem;
                }
                if (!empty($menuItem["category_id"])) {
                    $menuItem["title"] = $menuItem["name"];
                    $menuItem["url"] = $menuItem["category_handle"];
                    $menuItem["category"] = $menuItem["category_id"];
                }
            }
            $children = CloudStore::$app->store->load("category", ["parent" => $menuItem["category"]]);
        } else {
            $children = CloudStore::$app->store->load("menu_tree", ["parent" => $menuItem["id"]]);
        }

        $this->navigationList["children"] = [];

        if ($children) {

            foreach ($children as $child) {

                if ($menuItem["category"]) {

                    $child = [
                        "id" => $child["category_id"],
                        "url" => $child["category_handle"],
                        "title" => $child["name"],
                        "has_current" => false
                    ];

                    $_children = CloudStore::$app->store->load("category", ["parent" => $child["id"]]);
                    if ($_children) {
                        foreach ($_children as $_key => $_child) {
                            $_children[$_key]["url"] = $_child["category_handle"];
                            $_children[$_key]["title"] = $_child["name"];
                            $_children[$_key]["id"] = $_child["category_id"];
                            if ((int)$_child["category_id"] === $this->categoryId) {
                                $_children[$_key]["current"] = true;
                                $child["has_current"] = true;
                            } else {
                                $_children[$_key]["current"] = false;
                            }
                        }
                    }

                } else {
                    $_children = CloudStore::$app->store->load("menu_tree", ["parent" => $child["id"]]);
                }

                $child["current"] = (int)$child["url"] === $this->categoryId ? true : false;
                $child["children"] = $_children;

                $this->navigationList["children"][] = $child;
            }
        }

        $this->navigationList["current"] = $menuItem;

        if (!empty($menuItem["category_id"])) {
            $_menuItem = CloudStore::$app->store->loadOne("menu_tree", ["category" => $menuItem["category_id"]]);
            if ($_menuItem) {
                $menuItem["parent"] = $_menuItem["parent"];
            }
        }
        // parent
        $this->navigationList["parent"] = [];
        if ($menuItem["parent"]) {

            if (!empty($menuItem["category"])) {
                $category = CloudStore::$app->store->loadOne("category", ["category_id" => $menuItem["parent"]]);
                if ($category) {
                    $this->navigationList["parent"] = [
                        "title" => $category["name"],
                        "url" => $category["category_handle"]
                    ];
                } else {
                    $this->navigationList["parent"] = CloudStore::$app->store->loadOne("menu_tree", ["id" => $menuItem["parent"]]);
                }
            } else {
                $this->navigationList["parent"] = CloudStore::$app->store->loadOne("menu_tree", ["id" => $menuItem["parent"]]);
            }
        } else {

            $this->navigationList["parent"] = $this->backToMain;
        }

        return $this->navigationList;
    }
}
